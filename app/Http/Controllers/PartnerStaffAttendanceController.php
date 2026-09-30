<?php

namespace App\Http\Controllers;

use App\Models\Academies;
use App\Models\Address;
use App\Models\Coach;
use App\Models\PartnerStaffAttendance;
use App\Models\PartnerUser;
use App\Services\StaffAttendanceQrService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PartnerStaffAttendanceController extends Controller
{
    private function getAcademyId(): int
    {
        $user = auth('academy')->user();
        if ($user instanceof PartnerUser) {
            return (int) $user->academy_id;
        }
        return (int) ($user?->id ?? auth('academy')->id());
    }

    /**
     * Attendance Management Dashboard
     */
    public function index(Request $request)
    {
        $academyId = $this->getAcademyId();
        $date = $request->filled('date') ? Carbon::parse($request->date)->toDateString() : Carbon::today()->toDateString();
        $branchId = $request->filled('branch_id') ? (int) $request->branch_id : null;
        $staffType = $request->get('staff_type', 'all');

        $query = PartnerStaffAttendance::with(['coach', 'partnerUser', 'branch'])
            ->where('academy_id', $academyId)
            ->whereDate('attendance_date', $date);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($staffType && $staffType !== 'all') {
            $query->where('staff_type', $staffType);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->latest('check_in_at')->paginate(25)->withQueryString();

        // Stats for the selected day
        $allDayQuery = PartnerStaffAttendance::where('academy_id', $academyId)->whereDate('attendance_date', $date);
        if ($branchId) {
            $allDayQuery->where('branch_id', $branchId);
        }

        $stats = [
            'total_present' => (clone $allDayQuery)->count(),
            'currently_in' => (clone $allDayQuery)->whereNull('check_out_at')->count(),
            'checked_out' => (clone $allDayQuery)->whereNotNull('check_out_at')->count(),
            'total_late' => (clone $allDayQuery)->where('status', 'late')->count(),
            'total_hours' => round((clone $allDayQuery)->sum('work_minutes') / 60, 1),
        ];

        // Branches & Staff for filters and manual entry
        $branches = Address::where('academy_id', $academyId)->get();
        $employees = PartnerUser::where('academy_id', $academyId)->where('status', 'active')->orderBy('name')->get();
        $coaches = Coach::where('academy_id', $academyId)->where('active', true)->orderBy('name')->get();
        $academy = Academies::find($academyId);

        return view('Academy.pages.staff_attendance.index', compact(
            'attendances',
            'stats',
            'branches',
            'employees',
            'coaches',
            'academy',
            'date',
            'branchId',
            'staffType'
        ));
    }

    /**
     * Reception / Kiosk Live Dynamic QR Screen
     */
    public function liveScreen(Request $request)
    {
        $academyId = $this->getAcademyId();
        $academy = Academies::find($academyId);
        $branches = Address::where('academy_id', $academyId)->get();
        $selectedBranchId = (int) ($request->branch_id ?: ($branches->first()?->id ?? 0));
        $selectedBranch = $branches->firstWhere('id', $selectedBranchId) ?? $branches->first();

        $tokenData = StaffAttendanceQrService::generateToken($academyId, $selectedBranchId);

        // Recent check-ins for this branch today
        $recentCheckins = PartnerStaffAttendance::with(['coach', 'partnerUser'])
            ->where('academy_id', $academyId)
            ->where('branch_id', $selectedBranchId)
            ->whereDate('attendance_date', Carbon::today())
            ->latest('check_in_at')
            ->take(8)
            ->get();

        return view('Academy.pages.staff_attendance.live_screen', compact(
            'academy',
            'branches',
            'selectedBranch',
            'tokenData',
            'recentCheckins'
        ));
    }

    /**
     * JSON Endpoint: Refresh QR Token
     */
    public function getQrToken(Request $request)
    {
        $academyId = $this->getAcademyId();
        $branchId = (int) ($request->branch_id ?: 0);

        $tokenData = StaffAttendanceQrService::generateToken($academyId, $branchId);

        return response()->json($tokenData);
    }

    /**
     * Mobile Page reached by scanning the QR code
     */
    public function processScan(Request $request)
    {
        $academyId = (int) $request->aid;
        $branchId = (int) $request->bid;
        $token = (string) $request->token;
        $slot = (int) $request->slot;

        $isValid = StaffAttendanceQrService::verifyToken($academyId, $branchId, $token, $slot);

        $academy = Academies::find($academyId);
        $branch = Address::find($branchId);

        if (!$isValid) {
            return view('Academy.pages.staff_attendance.scan_result', [
                'success' => false,
                'message' => app()->getLocale() === 'ar'
                    ? 'عذراً، هذا الكود انتهت صلاحيته الزمنية (يتجدد كل 20 ثانية لمنع تداوله). يرجى مسح الكود الظاهر حالياً على شاشة الاستقبال بالفرع.'
                    : 'Sorry, this dynamic QR code has expired (refreshes every 20 seconds). Please rescan the active code displayed at reception.',
                'academy' => $academy,
                'branch' => $branch,
            ]);
        }

        // Fetch staff candidates for this academy
        $employees = PartnerUser::where('academy_id', $academyId)->where('status', 'active')->orderBy('name')->get();
        $coaches = Coach::where('academy_id', $academyId)->where('active', true)->orderBy('name')->get();

        // Check if current user is logged in
        $authUser = auth('academy')->user();
        $loggedStaffType = null;
        $loggedStaffId = null;

        if ($authUser instanceof PartnerUser && $authUser->academy_id == $academyId) {
            $loggedStaffType = 'employee';
            $loggedStaffId = $authUser->id;
        }

        return view('Academy.pages.staff_attendance.scan_form', compact(
            'academy',
            'branch',
            'token',
            'slot',
            'employees',
            'coaches',
            'loggedStaffType',
            'loggedStaffId'
        ));
    }

    /**
     * Confirm and Record Attendance with Multi-Layer Security Checks
     */
    public function confirmScan(Request $request)
    {
        $data = $request->validate([
            'academy_id' => 'required|integer',
            'branch_id' => 'required|integer',
            'token' => 'required|string',
            'slot' => 'required|integer',
            'staff_target' => 'required|string', // format: "employee:ID" or "coach:ID"
            'security_pin' => 'nullable|string|max:10',
        ]);

        $academyId = (int) $data['academy_id'];
        $branchId = (int) $data['branch_id'];
        $token = $data['token'];
        $slot = (int) $data['slot'];

        // 1. Cryptographic Dynamic Token & Slot verification
        $isValid = StaffAttendanceQrService::verifyToken($academyId, $branchId, $token, $slot);
        if (!$isValid) {
            return back()->with('error', app()->getLocale() === 'ar'
                ? 'انتهت صلاحية الكود (يتجدد كل 20 ثانية). يرجى مسح الكود الظاهر حالياً على الشاشة.'
                : 'QR Code expired. Please rescan the active screen.');
        }

        // 2. Validate Academy and Branch
        $academy = Academies::findOrFail($academyId);
        $branch = null;
        if ($branchId > 0) {
            $branch = Address::where('academy_id', $academyId)->find($branchId);
            if (!$branch) {
                return back()->with('error', app()->getLocale() === 'ar' ? 'الفرع المحدد غير صحيح أو غير تابع للمنشأة.' : 'Invalid branch.');
            }
        }

        [$type, $staffId] = explode(':', $data['staff_target']);
        $staffId = (int) $staffId;
        $today = Carbon::today()->toDateString();
        $now = Carbon::now();

        // 3. User Identity & Permission checks
        $authUser = auth('academy')->user();
        $isSessionAuthenticated = false;

        if ($authUser instanceof PartnerUser && $authUser->academy_id == $academyId) {
            // Lock target strictly to the authenticated user (prevent checking in for others)
            $type = 'employee';
            $staffId = $authUser->id;
            $isSessionAuthenticated = true;
        }

        // Fetch target record
        $staffName = '';
        $expectedPhone = null;

        if ($type === 'employee') {
            $employee = PartnerUser::where('academy_id', $academyId)->where('status', 'active')->findOrFail($staffId);
            $staffName = $employee->name;
            $expectedPhone = $employee->phone;

            // Check if employee has permission for this branch
            if ($branchId > 0 && !$employee->is_owner && !$employee->access_all_branches) {
                if (!$employee->canAccessBranch($branchId)) {
                    return back()->with('error', app()->getLocale() === 'ar'
                        ? 'عذراً، هذا الموظف غير مسجل للعمل في هذا الفرع المحدد.'
                        : 'Staff member is not assigned to this branch.');
                }
            }
        } else {
            $coach = Coach::where('academy_id', $academyId)->where('active', true)->findOrFail($staffId);
            $staffName = $coach->name;
            $expectedPhone = $coach->phone;
        }

        // 4. Anti-Impersonation PIN check (Mandatory if not logged into personal account)
        if (!$isSessionAuthenticated) {
            if (!$request->filled('security_pin')) {
                return back()->with('error', app()->getLocale() === 'ar'
                    ? 'يرجى إدخال رمز التحقق الأمني (آخر 4 أرقام من رقم هاتفك المسجل).'
                    : 'Security PIN required (last 4 digits of your registered phone).');
            }

            $cleanPhone = preg_replace('/[^0-9]/', '', (string) $expectedPhone);
            $last4 = substr($cleanPhone, -4);

            if (empty($last4) || $data['security_pin'] !== $last4) {
                return back()->with('error', app()->getLocale() === 'ar'
                    ? 'رمز التحقق غير صحيح. يجب إدخال آخر 4 أرقام من هاتفك المسجل بالمنظومة.'
                    : 'Invalid security PIN.');
            }
        }

        // 5. Database Transaction & Anti-Replay / Cooldown Logic
        return DB::transaction(function () use ($academyId, $branchId, $type, $staffId, $today, $now, $staffName, $academy, $branch, $request) {
            // Check existing open check-in for today
            $existing = PartnerStaffAttendance::where('academy_id', $academyId)
                ->where('staff_type', $type)
                ->when($type === 'employee', fn ($q) => $q->where('partner_user_id', $staffId))
                ->when($type === 'coach', fn ($q) => $q->where('coach_id', $staffId))
                ->whereDate('attendance_date', $today)
                ->latest('check_in_at')
                ->lockForUpdate()
                ->first();

            if ($existing && !$existing->check_out_at) {
                // Cooldown: prevent accidental check-out within 2 minutes of check-in
                $minutesDiff = (int) $existing->check_in_at->diffInMinutes($now);
                if ($minutesDiff < 2) {
                    return back()->with('error', app()->getLocale() === 'ar'
                        ? 'تم تسجيل حضورك للتو قبل أقل من دقيقتين. لا يمكن تسجيل الانصراف فوراً لمنع التكرار العرضي.'
                        : 'You just checked in less than 2 minutes ago. Please wait before checking out.');
                }

                // Register Check-Out
                $workMinutes = (int) $existing->check_in_at->diffInMinutes($now);
                $existing->update([
                    'check_out_at' => $now,
                    'work_minutes' => $workMinutes,
                ]);

                $h = intdiv($workMinutes, 60);
                $m = $workMinutes % 60;
                $durationStr = ($h > 0 ? "{$h} " . (app()->getLocale() === 'ar' ? 'ساعة ' : 'hrs ') : '') . ($m > 0 ? "{$m} " . (app()->getLocale() === 'ar' ? 'دقيقة' : 'mins') : '');

                return view('Academy.pages.staff_attendance.scan_result', [
                    'success' => true,
                    'action_type' => 'check_out',
                    'staff_name' => $staffName,
                    'time_str' => $now->format('H:i'),
                    'duration_str' => $durationStr ?: (app()->getLocale() === 'ar' ? 'أقل من دقيقة' : '< 1 min'),
                    'message' => app()->getLocale() === 'ar'
                        ? "تم تسجيل انصرافك بنجاح يا {$staffName}. إجمالي فترة التواجد: {$durationStr}."
                        : "Check-out confirmed, {$staffName}. Total work duration: {$durationStr}.",
                    'academy' => $academy,
                    'branch' => $branch,
                ]);
            }

            // Register Check-In
            PartnerStaffAttendance::create([
                'academy_id' => $academyId,
                'branch_id' => $branchId ?: null,
                'staff_type' => $type,
                'partner_user_id' => $type === 'employee' ? $staffId : null,
                'coach_id' => $type === 'coach' ? $staffId : null,
                'attendance_date' => $today,
                'check_in_at' => $now,
                'status' => 'present',
                'verification_method' => 'dynamic_qr',
                'ip_address' => $request->ip(),
                'device_info' => $request->header('User-Agent'),
            ]);

            return view('Academy.pages.staff_attendance.scan_result', [
                'success' => true,
                'action_type' => 'check_in',
                'staff_name' => $staffName,
                'time_str' => $now->format('H:i'),
                'duration_str' => null,
                'message' => app()->getLocale() === 'ar'
                    ? "مرحباً بك يا {$staffName}! تم تسجيل حضورك بنجاح في فرع " . ($branch?->address ?: 'الرئيسي') . " الساعة " . $now->format('H:i') . "."
                    : "Welcome {$staffName}! Check-in recorded at " . $now->format('H:i') . ".",
                'academy' => $academy,
                'branch' => $branch,
            ]);
        });
    }

    /**
     * Manual Attendance Entry by Supervisor
     */
    public function storeManual(Request $request)
    {
        $academyId = $this->getAcademyId();

        $data = $request->validate([
            'staff_target' => 'required|string',
            'branch_id' => 'nullable|integer',
            'attendance_date' => 'required|date',
            'check_in_time' => 'required|string',
            'check_out_time' => 'nullable|string',
            'status' => 'required|in:present,late,absent,excused',
            'notes' => 'nullable|string|max:500',
        ]);

        [$type, $staffId] = explode(':', $data['staff_target']);
        $staffId = (int) $staffId;

        $checkInAt = Carbon::parse($data['attendance_date'] . ' ' . $data['check_in_time']);
        $checkOutAt = $request->filled('check_out_time')
            ? Carbon::parse($data['attendance_date'] . ' ' . $data['check_out_time'])
            : null;

        $workMinutes = ($checkOutAt && $checkOutAt->gt($checkInAt))
            ? (int) $checkInAt->diffInMinutes($checkOutAt)
            : 0;

        PartnerStaffAttendance::create([
            'academy_id' => $academyId,
            'branch_id' => $data['branch_id'] ?: null,
            'staff_type' => $type,
            'partner_user_id' => $type === 'employee' ? $staffId : null,
            'coach_id' => $type === 'coach' ? $staffId : null,
            'attendance_date' => $data['attendance_date'],
            'check_in_at' => $checkInAt,
            'check_out_at' => $checkOutAt,
            'work_minutes' => $workMinutes,
            'status' => $data['status'],
            'verification_method' => 'manual',
            'notes' => $data['notes'],
            'created_by_user_id' => auth('academy')->id(),
        ]);

        return back()->with('success', app()->getLocale() === 'ar' ? 'تم إضافة قيد الحضور اليدوي بنجاح.' : 'Manual attendance record added successfully.');
    }

    /**
     * Delete Attendance Record
     */
    public function destroy(PartnerStaffAttendance $attendance)
    {
        abort_unless((int) $attendance->academy_id === $this->getAcademyId(), 403);
        $attendance->delete();

        return back()->with('success', app()->getLocale() === 'ar' ? 'تم حذف سجل الحضور بنجاح.' : 'Attendance record deleted.');
    }
}
