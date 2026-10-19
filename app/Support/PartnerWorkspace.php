<?php

namespace App\Support;

use App\Models\Academies;
use App\Models\PartnerUser;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Str;

/** The sidebar and operating guide share the same ordered, permitted pages. */
class PartnerWorkspace
{
    public static function forUser(Authenticatable $user, ?string $routeName = null): array
    {
        $academy = $user instanceof PartnerUser ? $user->academy : $user;
        $type = $academy?->business_type ?? 'academy';
        if (!in_array($type, ['academy', 'gym', 'health_center', 'hybrid', 'venue'], true)) {
            $type = 'academy';
        }
        $venue = in_array($type, ['venue', 'hybrid'], true) && ($academy?->hasVenueModule() ?? false);
        $training = $type !== 'venue';
        $groups = in_array($type, ['academy', 'hybrid'], true);
        $gate = in_array($type, ['gym', 'health_center', 'hybrid'], true);
        $can = fn(string $permission) => $user instanceof Academies || $user->hasPermissionTo($permission);
        $any = fn(string ...$permissions) => collect($permissions)->contains(fn($permission) => $can($permission));
        $students = $training && $any('bookings.view', 'bookings.manage', 'trainings.view');
        $bookings = $any('bookings.view', 'bookings.manage');
        $trainings = $training && $any('trainings.view', 'trainings.manage');
        $settings = $can('settings.manage') || (bool) $user->is_owner;
        $finance = $can('settlements.view');
        $team = $can('users.view'); // The team routes require this exact permission.
        $pages = [];
        $add = function (string $id, bool $visible, string $route, string $title, string $icon, array $patterns, string $lane = 'shared', array $except = []) use (&$pages, $routeName) {
            if (!$visible) {
                return;
            }
            $active = $routeName && Str::is($patterns, $routeName) && !Str::is($except, $routeName);
            $pages[$id] = compact('id', 'route', 'title', 'icon', 'lane', 'active');
        };
        $nav = fn(string $key) => 'partner_navigation.' . $key;
        $typed = fn(string $key) => 'partner_navigation.' . $key . '.' . $type;

        // Prepare locations and coaches before programs; a class requires a program.
        $add('addresses', $training && $settings, 'academy.address.index', $typed('addresses_by_type'), 'map-location-dot', ['academy.address.*'], 'training');
        $add('coaches', $training && $any('coaches.view', 'coaches.manage'), 'academy.coach', $typed('coaches_by_type'), 'user-tie', ['academy.coach', 'academy.coach.*'], 'training');
        $add('programs', $trainings, 'academy.training.index', $typed('programs'), 'dumbbell', ['academy.training.*'], 'training');
        $add('classes', $trainings, 'academy.class.index', $typed('classes'), 'people-roof', ['academy.class.*'], 'training');
        $add('venue_locations', $venue && $bookings, 'academy.venues.index', $nav('venue_locations'), 'location-dot', ['academy.venues.*'], 'venue');
        $add('venue_spaces', $venue && $bookings, 'academy.venue-spaces.index', $nav('venue_spaces'), 'futbol', ['academy.venue-spaces.*'], 'venue');

        // A subscription requires a person; a group optionally links people, coach and program.
        $add('records', $students, 'academy.students.index', $typed('records'), 'id-card', ['academy.students.*'], 'training');
        $add('groups', $students && $groups, 'academy.groups.index', $nav('groups'), 'people-group', ['academy.groups.*'], 'training');
        $add('subscriptions', $students, 'academy.subscriptions.index', $typed('subscriptions'), 'receipt', ['academy.subscriptions.*'], 'training');

        $add('calendar', $trainings, 'academy.calendar.index', $nav('calendar'), 'calendar-days', ['academy.calendar.*'], 'training');
        $add('training_booking', $training && $bookings, 'academy.createBooking', $nav('training_booking'), 'calendar-plus', ['academy.createBooking'], 'training');
        $add('attendance', $students && $groups, 'academy.attendance.index', $nav('class_attendance'), 'clipboard-check', ['academy.attendance.*'], 'training', ['academy.attendance.scanner']);
        $add('attendance_scanner', $students && $groups, 'academy.attendance.scanner', $nav('attendance_scanner'), 'qrcode', ['academy.attendance.scanner'], 'training');
        $add('gate_scanner', $students && $gate, 'academy.gym-gate.scanner', $nav('gate_scanner'), 'door-open', ['academy.gym-gate.scanner'], 'training');
        $add('gate_log', $students && $gate, 'academy.gym-gate.log', $nav('gate_log'), 'clock-rotate-left', ['academy.gym-gate.log'], 'training');
        $add('competitions', $students && $groups, 'academy.competitions.index', $nav('competitions'), 'trophy', ['academy.competitions.*'], 'training');
        $add('camps', $students && $groups, 'academy.camps.index', $nav('camps'), 'plane-departure', ['academy.camps.*'], 'training');
        $add('student_reports', $students, 'academy.student-reports.index', $typed('people_reports'), 'chart-line', ['academy.student-reports.*'], 'training');
        $add('venue_new_booking', $venue && $can('bookings.manage'), 'academy.venue-bookings.create', $nav('venue_new_booking'), 'calendar-plus', ['academy.venue-bookings.create'], 'venue');
        $add('venue_calendar', $venue && $bookings, 'academy.venue-bookings.calendar', $nav('venue_calendar'), 'calendar-days', ['academy.venue-bookings.calendar'], 'venue');
        $add('venue_bookings', $venue && $bookings, 'academy.venue-bookings.index', $nav('venue_bookings'), 'calendar-check', ['academy.venue-bookings.*'], 'venue', ['academy.venue-bookings.create', 'academy.venue-bookings.calendar']);

        $add('overview', $training && $finance, 'academy.report.overview', $nav('overview'), 'chart-line', ['academy.report.overview*']);
        $add('settlements', $training && $finance, 'academy.report.settlement.index', 'admin.settlement.Settlements', 'scale-balanced', ['academy.report.settlement.*']);
        $add('payments', $training && $finance, 'academy.report.transaction.index', $nav('payments'), 'money-bill-transfer', ['academy.report.transaction.*']);
        $add('bookings', $training && $finance, 'academy.report.joins', $nav('bookings'), 'ticket', ['academy.report.joins']);
        $add('manual_bookings', $training && $finance, 'academy.report.offline-joins', $nav('manual_bookings'), 'cash-register', ['academy.report.offline-joins']);
        $add('shift_closings', $finance, 'academy.shift-closings.index', $nav('shift_closings'), 'cash-register', ['academy.shift-closings.*']);
        $add('expenses', $finance, 'academy.expenses.index', $nav('expenses'), 'coins', ['academy.expenses.*']);
        $add('billing', $finance, 'academy.billing-invoices.index', $nav('billing'), 'file-invoice-dollar', ['academy.billing-invoices.*']);
        $communication = $training && ($bookings || $can('dashboard.view'));
        $add('whatsapp', $communication, 'academy.whatsapp.index', $nav('whatsapp'), 'comment-dots', ['academy.whatsapp.*']);
        $add('gallery', $communication, 'academy.gallery.index', 'admin.gallery.gallery', 'images', ['academy.gallery.*']);
        $add('notifications', true, 'academy.notification.index', 'admin.notifications.notifications', 'bell', ['academy.notification.*']);
        $add('team', $team, 'academy.team.index', $nav('team'), 'users-gear', ['academy.team.*']);
        $add('staff_attendance', $training && $team, 'academy.staff-attendance.index', $nav('staff_attendance'), 'user-clock', ['academy.staff-attendance.index']);
        $add('attendance_screen', $training && $team, 'academy.staff-attendance.live-screen', $nav('attendance_screen'), 'qrcode', ['academy.staff-attendance.live-screen']);
        if (isset($pages['attendance_screen'])) {
            $pages['attendance_screen']['newTab'] = true;
        }
        $add('terms', $settings, 'academy.terms.index', 'admin.terms.terms', 'file-shield', ['academy.terms.*']);

        $order = [
            'setup' => ['addresses', 'coaches', 'programs', 'classes', 'venue_locations', 'venue_spaces'],
            'registration' => ['records', 'groups', 'subscriptions'],
            'daily' => ['calendar', 'training_booking', 'attendance', 'attendance_scanner', 'gate_scanner', 'gate_log', 'competitions', 'camps', 'student_reports', 'venue_calendar', 'venue_new_booking', 'venue_bookings'],
            'finance' => ['overview', 'settlements', 'payments', 'bookings', 'manual_bookings', 'shift_closings', 'expenses', 'billing'],
            'communication' => ['whatsapp', 'gallery', 'notifications'],
            'administration' => ['team', 'staff_attendance', 'attendance_screen', 'terms'],
        ];
        // A gym membership does not depend on a training program or a class.
        if (in_array($type, ['gym', 'health_center'], true)) {
            $order = [
                'setup' => ['addresses'],
                'registration' => ['records', 'subscriptions'],
                'daily' => ['gate_scanner', 'gate_log', 'student_reports'],
                'training' => ['coaches', 'programs', 'classes', 'calendar', 'training_booking'],
                'finance' => $order['finance'],
                'communication' => $order['communication'],
                'administration' => $order['administration'],
            ];
        }
        $sections = [];
        foreach ($order as $id => $keys) {
            $items = array_values(array_filter(array_map(fn($key) => $pages[$key] ?? null, $keys)));
            if ($items) {
                $sections[] = ['id' => $id, 'items' => $items, 'active' => collect($items)->contains('active', true)];
            }
        }

        $pathOrder = match ($type) {
            'gym', 'health_center' => ['membership' => ['addresses', 'records', 'subscriptions', 'gate_scanner']],
            'venue' => ['venue' => ['venue_locations', 'venue_spaces', 'venue_calendar', 'venue_new_booking', 'venue_bookings']],
            'hybrid' => [
                'training' => ['addresses', 'coaches', 'programs', 'classes', 'records', 'groups', 'subscriptions', 'attendance'],
                'membership' => ['addresses', 'records', 'subscriptions', 'gate_scanner'],
                'venue' => ['venue_locations', 'venue_spaces', 'venue_calendar', 'venue_new_booking', 'venue_bookings'],
            ],
            default => ['training' => ['addresses', 'coaches', 'programs', 'classes', 'records', 'groups', 'subscriptions', 'attendance']],
        };
        $paths = [];
        foreach ($pathOrder as $id => $keys) {
            $items = array_values(array_filter(array_map(fn($key) => $pages[$key] ?? null, $keys)));
            if ($items) {
                $paths[] = ['id' => $id, 'items' => $items];
            }
        }

        return ['type' => $type, 'venueEnabled' => $venue, 'showDashboard' => $can('dashboard.view'), 'sections' => $sections, 'paths' => $paths];
    }
}
