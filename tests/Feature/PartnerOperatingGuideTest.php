<?php

namespace Tests\Feature;

use App\Models\Academies;
use App\Models\PartnerPermission;
use App\Models\PartnerRole;
use App\Models\PartnerUser;
use App\Support\PartnerWorkspace;
use Illuminate\Support\Collection;
use Tests\TestCase;

class PartnerOperatingGuideTest extends TestCase
{
    private function owner(string $type, bool $venueEnabled = true): Academies
    {
        $owner = $this->getMockBuilder(Academies::class)->onlyMethods(['hasVenueModule'])->getMock();
        $owner->method('hasVenueModule')->willReturn($venueEnabled);
        $owner->forceFill(['id' => 900, 'business_type' => $type, 'name' => 'Local partner', 'status' => 'active']);
        $owner->setRelation('currentSubscription', null);
        $owner->setRelation('unreadNotifications', new Collection());

        return $owner;
    }

    private function staff(string $type, array $permissions = [], bool $venueEnabled = true): PartnerUser
    {
        $role = new PartnerRole();
        $role->setRelation('permissions', collect($permissions)->map(fn($name) => new PartnerPermission(['name' => $name])));
        $user = new PartnerUser(['name' => 'Local staff', 'is_owner' => false]);
        $user->forceFill(['id' => 901]);
        $user->setRelation('academy', $this->owner($type, $venueEnabled));
        $user->setRelation('roles', collect([$role]));
        $user->setRelation('unreadNotifications', new Collection());

        return $user;
    }

    private function pageIds(array $workspace): array
    {
        return collect($workspace['sections'])->flatMap(fn($section) => array_column($section['items'], 'id'))->all();
    }

    public function test_academy_setup_follows_the_actual_program_dependencies(): void
    {
        $workspace = PartnerWorkspace::forUser($this->owner('academy'));
        $this->assertSame(['addresses', 'coaches', 'programs', 'classes'], array_column($workspace['sections'][0]['items'], 'id'));
        $this->assertSame(['records', 'groups', 'subscriptions'], array_column($workspace['sections'][1]['items'], 'id'));
        $this->assertNotContains('gate_scanner', $this->pageIds($workspace));
        $this->assertNotContains('venue_spaces', $this->pageIds($workspace));
    }

    public function test_gym_memberships_do_not_require_training_or_groups(): void
    {
        $workspace = PartnerWorkspace::forUser($this->owner('gym'));
        $this->assertSame(['addresses'], array_column($workspace['sections'][0]['items'], 'id'));
        $this->assertSame(['records', 'subscriptions'], array_column($workspace['sections'][1]['items'], 'id'));
        $this->assertSame(['addresses', 'records', 'subscriptions', 'gate_scanner'], array_column($workspace['paths'][0]['items'], 'id'));
        $this->assertSame('training', $workspace['sections'][3]['id']);
        $this->assertNotContains('groups', $this->pageIds($workspace));
        $this->assertNotContains('camps', $this->pageIds($workspace));
    }

    public function test_venue_workflow_has_locations_spaces_and_bookings_without_academy_pages(): void
    {
        $workspace = PartnerWorkspace::forUser($this->owner('venue'));
        $this->assertSame(['venue_locations', 'venue_spaces'], array_column($workspace['sections'][0]['items'], 'id'));
        $this->assertSame(['venue_calendar', 'venue_new_booking', 'venue_bookings'], array_column($workspace['sections'][1]['items'], 'id'));
        foreach (['records', 'groups', 'coaches', 'programs', 'subscriptions'] as $page) {
            $this->assertNotContains($page, $this->pageIds($workspace));
        }
    }

    public function test_hybrid_has_distinct_training_membership_and_rental_paths(): void
    {
        $workspace = PartnerWorkspace::forUser($this->owner('hybrid'));
        $this->assertSame(['training', 'membership', 'venue'], array_column($workspace['paths'], 'id'));
        foreach (['groups', 'attendance', 'gate_scanner', 'venue_spaces', 'venue_calendar'] as $page) {
            $this->assertContains($page, $this->pageIds($workspace));
        }
    }

    public function test_unlicensed_venue_links_are_removed_for_owner_and_staff(): void
    {
        foreach ([$this->owner('hybrid', false), $this->staff('venue', ['bookings.view', 'bookings.manage'], false)] as $user) {
            $workspace = PartnerWorkspace::forUser($user);
            $this->assertFalse($workspace['venueEnabled']);
            $this->assertEmpty(array_filter($this->pageIds($workspace), fn($id) => str_starts_with($id, 'venue_')));
            $this->assertNotContains('venue', array_column($workspace['paths'], 'id'));
        }
    }

    public function test_staff_profile_is_resolved_from_their_owning_business_and_permissions(): void
    {
        $workspace = PartnerWorkspace::forUser($this->staff('venue', ['bookings.view']));
        $this->assertSame('venue', $workspace['type']);
        $this->assertContains('venue_calendar', $this->pageIds($workspace));
        foreach (['venue_new_booking', 'records', 'coaches', 'team', 'expenses', 'billing'] as $page) {
            $this->assertNotContains($page, $this->pageIds($workspace));
        }
        $this->assertFalse($workspace['showDashboard']);
    }

    public function test_active_pages_expand_their_own_stage_without_selecting_multiple_pages(): void
    {
        foreach (['academy.camps.index', 'academy.attendance.scanner', 'academy.venue-bookings.create', 'academy.venue-bookings.calendar'] as $route) {
            $workspace = PartnerWorkspace::forUser($this->owner('hybrid'), $route);
            $active = collect($workspace['sections'])->flatMap(fn($section) => $section['items'])->where('active', true);
            $this->assertCount(1, $active, $route);
            $this->assertCount(1, array_filter($workspace['sections'], fn($section) => $section['active']), $route);
        }
    }

    public function test_guide_requires_partner_authentication(): void
    {
        $this->get(route('academy.operating-guide'))->assertRedirect(route('academy.loginPage'));
    }

    public function test_guide_renders_the_permitted_workflow_for_each_business_and_language(): void
    {
        foreach (['ar', 'en'] as $locale) {
            foreach (['academy', 'gym', 'health_center', 'hybrid', 'venue'] as $type) {
                $user = $this->owner($type);
                $response = $this->actingAs($user, 'academy')->withSession(['locale' => $locale])->get(route('academy.operating-guide'));
                $response->assertOk()->assertSee(trans('partner_workflow.title', [], $locale));
                $response->assertDontSee('partner_workflow.')->assertDontSee('partner_navigation.');
                $expected = PartnerWorkspace::forUser($user);
                $dom = new \DOMDocument();
                @$dom->loadHTML('<?xml encoding="utf-8" ?>' . $response->getContent());
                $xpath = new \DOMXPath($dom);
                $guidePages = [];
                foreach ($xpath->query('//article[@data-page]') as $node) {
                    $guidePages[] = $node->getAttribute('data-page');
                }
                $this->assertSame($this->pageIds($expected), $guidePages, "$locale/$type");
                $menuLinks = [];
                foreach ($xpath->query('//nav[@id="sidebar"]//ul[contains(@class,"submenu")]//a') as $node) {
                    $menuLinks[] = $node->getAttribute('href');
                }
                $guideLinks = [];
                foreach ($xpath->query('//article[@data-page]/a') as $node) {
                    $guideLinks[] = $node->getAttribute('href');
                }
                $this->assertSame($menuLinks, $guideLinks, "$locale/$type links differ");
                foreach ($xpath->query('//a[starts-with(@href,"#guide-") or starts-with(@href,"#workflow-")]') as $anchor) {
                    $this->assertSame('true', $anchor->getAttribute('data-no-loader'));
                }
            }
        }
    }

    public function test_restricted_staff_guide_does_not_leak_management_links(): void
    {
        $this->actingAs($this->staff('gym', ['bookings.view']), 'academy')
            ->withSession(['locale' => 'ar'])
            ->get(route('academy.operating-guide'))
            ->assertOk()
            ->assertDontSee('data-page="expenses"', false)
            ->assertDontSee('data-page="team"', false)
            ->assertDontSee('data-page="addresses"', false)
            ->assertSee('data-page="records"', false);
    }
}
