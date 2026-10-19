<?php

namespace Tests\Feature;

use App\Http\Controllers\AcademyGroupController;
use App\Models\Academies;
use App\Models\AcademyAttendanceRecord;
use App\Models\AcademyAttendanceSession;
use App\Models\AcademyGroup;
use App\Models\AcademyStudent;
use App\Models\AcademyStudentSubscription;
use App\Models\PartnerUser;
use App\Support\MembershipCode;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class PartnerWorkflowRegressionTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        DB::purge('sqlite');

        Schema::create('academies', function (Blueprint $t) {
            $t->id(); $t->string('name'); $t->text('commercial_name');
            $t->string('business_type')->default('academy'); $t->string('status')->default('active');
            $t->unsignedBigInteger('country_id')->nullable(); $t->timestamps();
        });
        foreach (['users', 'countries', 'cities', 'areas', 'sports', 'coaches', 'trainings'] as $table) {
            Schema::create($table, function (Blueprint $t) {
                $t->id(); $t->string('name')->nullable(); $t->unsignedBigInteger('academy_id')->nullable(); $t->timestamps();
            });
        }
        Schema::create('academy_sport', function (Blueprint $t) { $t->unsignedBigInteger('academy_id'); $t->unsignedBigInteger('sport_id'); });
        Schema::create('settlements', function (Blueprint $t) { $t->id(); $t->unsignedBigInteger('partner_id'); $t->timestamps(); });
        Schema::create('academy_competitions', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('academy_id'); $t->unsignedBigInteger('sport_id')->nullable();
            $t->string('home_team_name'); $t->string('opponent_name'); $t->date('competition_date'); $t->string('status');
            $t->integer('home_score')->nullable(); $t->integer('opponent_score')->nullable(); $t->text('result_notes')->nullable(); $t->timestamps();
        });
        Schema::create('academy_competition_players', function (Blueprint $t) {
            $t->unsignedBigInteger('academy_competition_id'); $t->unsignedBigInteger('academy_student_id');
            $t->string('role')->nullable(); $t->text('notes')->nullable(); $t->timestamps();
        });
        Schema::create('academy_students', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('academy_id'); $t->unsignedBigInteger('user_id')->nullable();
            $t->string('name'); $t->string('phone')->nullable(); $t->string('email')->nullable();
            $t->string('club_card_number')->nullable(); $t->string('guardian_name')->nullable(); $t->string('guardian_phone')->nullable();
            foreach (['country_id', 'city_id', 'area_id'] as $col) $t->unsignedBigInteger($col)->nullable();
            $t->string('status')->default('active'); $t->timestamps();
        });
        Schema::create('academy_groups', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('academy_id'); $t->string('name');
            foreach (['training_id', 'coach_id', 'sport_id'] as $col) $t->unsignedBigInteger($col)->nullable();
            $t->text('days')->nullable(); $t->time('start_time')->nullable(); $t->time('end_time')->nullable();
            $t->integer('capacity')->nullable(); $t->string('status')->default('active'); $t->text('notes')->nullable(); $t->timestamps();
        });
        Schema::create('academy_group_students', function (Blueprint $t) {
            $t->unsignedBigInteger('academy_group_id'); $t->unsignedBigInteger('academy_student_id');
            $t->date('joined_at')->nullable(); $t->string('status')->default('active'); $t->timestamps();
        });
        Schema::create('academy_student_subscriptions', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('academy_student_id'); $t->unsignedBigInteger('academy_group_id')->nullable();
            $t->date('starts_on'); $t->date('ends_on'); $t->decimal('amount', 12, 2);
            $t->decimal('discount_amount', 12, 2)->default(0); $t->string('discount_reason')->nullable();
            $t->string('discount_approved_by')->nullable(); $t->timestamp('discount_approved_at')->nullable();
            $t->string('status'); $t->string('payment_status'); $t->text('notes')->nullable(); $t->timestamps();
        });
        Schema::create('academy_student_payments', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('academy_student_subscription_id'); $t->decimal('amount', 12, 2);
            $t->date('paid_at'); $t->string('method'); $t->string('method_other')->nullable();
            $t->string('reference')->nullable(); $t->text('notes')->nullable(); $t->timestamps();
        });
        Schema::create('academy_attendance_sessions', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('academy_group_id'); $t->date('session_date');
            $t->time('starts_at')->nullable(); $t->time('ends_at')->nullable(); $t->text('notes')->nullable(); $t->timestamps();
        });
        Schema::create('academy_attendance_records', function (Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('academy_attendance_session_id'); $t->unsignedBigInteger('academy_student_id');
            $t->string('status')->default('absent'); $t->time('check_in_at')->nullable(); $t->time('check_out_at')->nullable();
            $t->text('notes')->nullable(); $t->timestamps();
        });

        foreach ([7, 8] as $id) {
            DB::table('academies')->insert(['id' => $id, 'name' => "Academy $id", 'commercial_name' => json_encode(['ar' => "Academy $id", 'en' => "Academy $id"])]);
        }
        foreach ([[11, 7, 'Our player'], [22, 8, 'Other player']] as [$id, $academy, $name]) {
            $player = new AcademyStudent(['academy_id' => $academy, 'name' => $name, 'phone' => '010000000'.$id, 'status' => 'active']);
            $player->id = $id; $player->save();
        }
        foreach ([[31, 7, 11, 'Our group'], [32, 8, 22, 'Other group']] as [$id, $academy, $student, $name]) {
            $group = new AcademyGroup(['academy_id' => $academy, 'name' => $name, 'status' => 'active']);
            $group->id = $id; $group->save(); $group->students()->attach($student);
            $session = AcademyAttendanceSession::create(['academy_group_id' => $id, 'session_date' => today()->toDateString(), 'starts_at' => '23:00', 'ends_at' => '23:59']);
            $session->records()->create(['academy_student_id' => $student]);
        }
        $academy = Academies::findOrFail(7); $academy->setRelation('currentSubscription', null);
        $user = new PartnerUser(['academy_id' => 7, 'name' => 'Staff', 'is_owner' => true]);
        $user->id = 8; $user->setRelation('academy', $academy); $user->setRelation('roles', collect());
        $user->setRelation('unreadNotifications', collect());
        $this->actingAs($user, 'academy');
    }

    private function subscription(array $attributes = []): AcademyStudentSubscription
    {
        return AcademyStudentSubscription::create(array_merge([
            'academy_student_id' => 11, 'academy_group_id' => 31, 'starts_on' => today(),
            'ends_on' => today()->addMonth(), 'amount' => 500, 'status' => 'active',
        ], $attributes));
    }

    private function pay(AcademyStudentSubscription $subscription, float $amount, string $method = 'cash', string $reference = 'REF-1'): void
    {
        $this->post(route('academy.subscriptions.payments.store', $subscription), [
            'amount' => $amount, 'paid_at' => today()->toDateString(), 'method' => $method, 'reference' => $reference,
        ])->assertSessionHasNoErrors()->assertRedirect();
    }

    public function test_different_user_id_sees_only_the_owning_academy_groups_and_attendance(): void
    {
        foreach (['academy.groups.index', 'academy.attendance.create', 'academy.attendance.index', 'academy.attendance.scanner'] as $route) {
            $this->get(route($route))->assertOk()->assertSee('Our group')->assertDontSee('Other group');
        }
        $data = app(AcademyGroupController::class)->create()->getData();
        $this->assertSame([11], $data['groupStudents']->modelKeys());
    }

    public function test_creating_and_updating_groups_keeps_the_real_academy_and_members(): void
    {
        $this->post(route('academy.groups.store'), ['name' => 'New group', 'status' => 'active', 'student_ids' => [11]])->assertRedirect();
        $group = AcademyGroup::where('name', 'New group')->firstOrFail();
        $this->assertSame(7, $group->academy_id);
        $this->assertSame([11], $group->students->modelKeys());
        $this->put(route('academy.groups.update', $group), ['name' => 'Renamed', 'status' => 'active', 'student_ids' => [11]])->assertRedirect();
        $this->assertSame('Renamed', $group->fresh()->name);
    }

    public function test_foreign_group_writes_and_foreign_student_assignment_are_rejected(): void
    {
        $this->get(route('academy.groups.edit', 32))->assertNotFound();
        $this->put(route('academy.groups.update', 32), ['name' => 'Wrong', 'status' => 'active'])->assertNotFound();
        $this->delete(route('academy.groups.destroy', 32))->assertNotFound();
        $this->post(route('academy.groups.store'), ['name' => 'Invalid', 'status' => 'active', 'student_ids' => [22]])->assertNotFound();
        $this->assertDatabaseMissing('academy_groups', ['name' => 'Invalid']);
    }

    public function test_attendance_creation_updates_and_scans_are_scoped_to_the_academy(): void
    {
        $own = AcademyAttendanceSession::where('academy_group_id', 31)->firstOrFail();
        $foreign = AcademyAttendanceSession::where('academy_group_id', 32)->firstOrFail();
        $this->post(route('academy.attendance.store'), ['academy_group_id' => 31, 'session_date' => today()->addDay()->toDateString()])->assertRedirect();
        $this->get(route('academy.attendance.show', $foreign))->assertNotFound();
        $this->post(route('academy.attendance.store'), ['academy_group_id' => 32, 'session_date' => today()->toDateString()])->assertNotFound();
        $this->put(route('academy.attendance.update', $foreign), ['records' => []])->assertNotFound();
        $record = $own->records()->firstOrFail();
        $this->put(route('academy.attendance.update', $own), ['records' => [$record->id => ['status' => 'present', 'check_in_at' => '17:00']]])->assertRedirect();
        $this->assertSame('present', $record->fresh()->status);
        $code = MembershipCode::make(AcademyStudent::findOrFail(11));
        $this->postJson(route('academy.attendance.scan'), ['session_id' => $own->id, 'code' => $code, 'mode' => 'check_out'])->assertOk()->assertJsonPath('action', 'check_out');
        $this->postJson(route('academy.attendance.scan'), ['session_id' => $foreign->id, 'code' => $code, 'mode' => 'check_in'])->assertNotFound();
        $this->postJson(route('academy.attendance.scan'), ['session_id' => $own->id, 'code' => $code.'bad', 'mode' => 'check_in'])->assertUnprocessable();
    }

    public function test_legacy_membership_formats_remain_scoped_and_exact(): void
    {
        foreach (['HZ-7-11', 'STUDENT-11', 'MEMB-7-11', '11', '01000000011'] as $code) {
            $this->assertSame(11, MembershipCode::studentId($code, 7));
        }
        $this->assertNull(MembershipCode::studentId('HZ-8-11', 7));
        $this->assertNull(MembershipCode::studentId('MEMB-8-11', 7));
        $this->assertNull(MembershipCode::studentId('STUDENT-22', 7));
        $this->assertNull(MembershipCode::studentId('prefix-STUDENT-11-suffix', 7));
    }

    public function test_subscriptions_cannot_be_marked_paid_without_a_payment(): void
    {
        $payload = ['academy_student_id' => 11, 'academy_group_id' => 31, 'starts_on' => today()->toDateString(), 'ends_on' => today()->addMonth()->toDateString(), 'amount' => 500, 'status' => 'active', 'payment_status' => 'paid'];
        $this->post(route('academy.subscriptions.store'), $payload)->assertRedirect()->assertSessionHasNoErrors();
        $subscription = AcademyStudentSubscription::firstOrFail();
        $this->assertSame('unpaid', $subscription->payment_status);
        $this->assertSame(0.0, $subscription->paid_amount);
        $this->put(route('academy.subscriptions.update', $subscription), $payload)->assertRedirect();
        $this->assertSame('unpaid', $subscription->fresh()->payment_status);
        $this->assertSame('paid', $this->subscription(['amount' => 0])->payment_status);
    }

    public function test_discounted_collections_and_amount_edits_recalculate_payment_state(): void
    {
        $subscription = $this->subscription(['discount_amount' => 100]);
        $this->pay($subscription, 300);
        $this->assertSame('partial', $subscription->fresh()->payment_status);
        $this->pay($subscription, 100, 'instapay');
        $subscription->refresh();
        $this->assertSame('paid', $subscription->payment_status);
        $this->assertSame(0.0, $subscription->remaining_amount);
        $subscription->update(['amount' => 600, 'payment_status' => 'paid']);
        $this->assertSame('partial', $subscription->payment_status);
    }

    public function test_adding_and_reversing_discounts_recalculates_status_from_the_ledger(): void
    {
        $subscription = $this->subscription(); $this->pay($subscription, 400);
        $this->post(route('academy.subscriptions.apply-discount', $subscription), ['discount_amount' => 100, 'discount_reason' => 'Approved'])->assertRedirect()->assertSessionHasNoErrors();
        $this->assertSame('paid', $subscription->fresh()->payment_status);
        $this->post(route('academy.subscriptions.remove-discount', $subscription))->assertRedirect();
        $this->assertSame('partial', $subscription->fresh()->payment_status);
        $this->assertSame(100.0, $subscription->fresh()->remaining_amount);
    }

    public function test_other_academy_cannot_collect_or_print_a_students_subscription(): void
    {
        $subscription = $this->subscription(['academy_student_id' => 22, 'academy_group_id' => 32]);
        $this->post(route('academy.subscriptions.payments.store', $subscription), ['amount' => 500, 'paid_at' => today()->toDateString(), 'method' => 'cash'])->assertNotFound();
        $this->get(route('academy.invoices.students.print', $subscription))->assertNotFound();
        $this->assertSame(0.0, $subscription->paid_amount);
    }

    public function test_invoice_details_include_both_same_day_payment_methods_and_references(): void
    {
        $subscription = $this->subscription();
        $this->pay($subscription, 300, 'cash', 'CASH-001'); $this->pay($subscription, 200, 'instapay', 'TRANSFER-002');
        $response = $this->get(route('academy.invoices.students.print', $subscription))->assertOk();
        $response->assertSee('CASH-001')->assertSee('TRANSFER-002')->assertSee('300.00')->assertSee('200.00');
        $payments = $response->viewData('document')['payment_details'];
        $this->assertSame([300.0, 200.0], array_column($payments, 'amount'));
        $this->assertStringContainsString(trans('admin.payment_methods.cash'), $response->viewData('document')['payment_method']);
        $this->assertStringContainsString(trans('admin.payment_methods.instapay'), $response->viewData('document')['payment_method']);
    }

    public function test_reconciliation_repairs_historical_labels_without_changing_the_ledger(): void
    {
        $discounted = $this->subscription(['discount_amount' => 100]);
        $this->pay($discounted, 400);
        $unpaid = $this->subscription();
        DB::table('academy_student_subscriptions')->where('id', $discounted->id)->update(['payment_status' => 'partial']);
        DB::table('academy_student_subscriptions')->where('id', $unpaid->id)->update(['payment_status' => 'paid']);
        $before = DB::table('academy_student_payments')->get()->toJson();
        $updatedAt = $discounted->fresh()->getRawOriginal('updated_at');
        $migration = require database_path('migrations/2026_10_19_000001_reconcile_student_subscription_payment_statuses.php');
        $migration->up(); $migration->up();
        $this->assertSame('paid', $discounted->fresh()->payment_status);
        $this->assertSame('unpaid', $unpaid->fresh()->payment_status);
        $this->assertSame($before, DB::table('academy_student_payments')->get()->toJson());
        $this->assertSame($updatedAt, $discounted->fresh()->getRawOriginal('updated_at'));
    }

    public function test_related_reports_exports_and_legacy_owner_resolve_the_actual_academy(): void
    {
        foreach ([7, 8] as $academyId) {
            DB::table('coaches')->insert(['id' => $academyId, 'academy_id' => $academyId, 'name' => json_encode(['en' => 'Coach '.$academyId])]);
            DB::table('settlements')->insert(['id' => $academyId, 'partner_id' => $academyId]);
        }
        $coaches = (new \App\Exports\CoachExport())->view()->getData()['coachesExport'];
        $this->assertSame([7], $coaches->modelKeys());
        $settlements = app(\App\DataTables\SettlementDataTable::class)->query(new \App\Models\Settlement());
        $this->assertSame([7], $settlements->pluck('id')->all());
        $this->get(route('academy.student-reports.index'))->assertOk()->assertSee('Our player')->assertDontSee('Other player');
        $this->actingAs(Academies::findOrFail(7)->setRelation('currentSubscription', null)->setRelation('unreadNotifications', collect()), 'academy');
        $this->get(route('academy.groups.index'))->assertOk()->assertSee('Our group')->assertDontSee('Other group');
    }

    public function test_competitions_use_the_owning_academy_and_reject_foreign_players(): void
    {
        $payload = ['home_team_name' => 'Our team', 'opponent_name' => 'Opponent', 'competition_date' => today()->toDateString(), 'status' => 'scheduled', 'student_ids' => [11]];
        $this->post(route('academy.competitions.store'), $payload)->assertRedirect()->assertSessionHasNoErrors();
        $competition = \App\Models\AcademyCompetition::firstOrFail();
        $this->assertSame(7, $competition->academy_id);
        $this->assertSame([11], $competition->students->modelKeys());
        $this->post(route('academy.competitions.store'), array_merge($payload, ['student_ids' => [22]]))->assertNotFound();
        $this->assertSame(1, \App\Models\AcademyCompetition::count());
    }
}
