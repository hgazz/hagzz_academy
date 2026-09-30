<?php

namespace App\Http\Controllers;

use App\Http\Traits\BookingFilterTrait;
use App\Http\Traits\TrainingsTrait;
use App\Http\Traits\UsersTrait;
use App\Models\Academies;
use App\Models\AcademyAttendanceRecord;
use App\Models\AcademyAttendanceSession;
use App\Models\AcademyGroup;
use App\Models\AcademyStudent;
use App\Models\AcademyStudentPayment;
use App\Models\AcademyStudentSubscription;
use App\Models\Coach;
use App\Models\Invoice;
use App\Models\Join;
use App\Models\Settlement;
use App\Models\Sport;
use App\Models\Training;
use App\Models\User;
use App\Models\Venue;
use App\Models\VenueBooking;
use App\Models\VenueSpace;
use App\Services\Chart\ChartsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    use BookingFilterTrait, UsersTrait, TrainingsTrait;

    private ChartsService $chartsService;

    /**
     * @param ChartsService $chartsService
     */
    public function __construct(ChartsService $chartsService)
    {
        $this->chartsService = $chartsService;
    }


    public function index()
    {
        try {
        $user = auth('academy')->user();
        if (!$user) {
            return redirect()->route('academy.loginPage');
        }
        $academy = ($user instanceof \App\Models\PartnerUser && $user->academy) ? $user->academy : $user;
        if (!$academy) {
            return redirect()->route('academy.loginPage');
        }
        $academyId = (int) ($user->academy_id ?? $academy->id);
        $now = now();
        $monthStart = $now->copy()->subMonths(11)->startOfMonth();
        $venueDashboard = in_array($academy->business_type, ['venue', 'hybrid'], true)
            ? $this->venueDashboardData($academyId, $monthStart)
            : null;

        if ($academy->business_type === 'venue') {
            return view('Academy.venue-dashboard', [
                'venueDashboard' => $venueDashboard,
                'academyName' => $this->localizedValue($academy->getRawOriginal('commercial_name')),
                'ownerName' => $academy->owner_name ?: $academy->first_name ?: $academy->name,
            ]);
        }
        $currentPeriodStart = $now->copy()->subDays(29)->startOfDay();
        $previousPeriodStart = $currentPeriodStart->copy()->subDays(30);

        $bookingQuery = Join::whereHas('training', fn ($query) => $query->where('academy_id', $academyId));
        $totalBookings = (clone $bookingQuery)->count();
        $totalRevenue = (float) (clone $bookingQuery)->sum('price');
        $uniqueCustomers = (clone $bookingQuery)->distinct('user_id')->count('user_id');
        $currentBookings = (clone $bookingQuery)->where('joins.created_at', '>=', $currentPeriodStart)->count();
        $previousBookings = (clone $bookingQuery)
            ->whereBetween('joins.created_at', [$previousPeriodStart, $currentPeriodStart])
            ->count();

        $totalTrainings = Training::where('academy_id', $academyId)->count();
        $activeTrainings = Training::where('academy_id', $academyId)->where('active', 1)->count();
        $totalCoaches = Coach::where('academy_id', $academyId)->count();

        $hasStudentModule = Schema::hasTable('academy_students')
            && Schema::hasTable('academy_groups')
            && Schema::hasTable('academy_student_subscriptions')
            && Schema::hasTable('academy_student_payments')
            && Schema::hasTable('academy_attendance_sessions')
            && Schema::hasTable('academy_attendance_records');

        $activeStudents = 0;
        $activeGroups = 0;
        $activeSubscriptions = 0;
        $subscriptionRevenue = 0.0;
        $outstandingSubscriptions = 0.0;
        $attendanceRate = 0.0;
        $todaySessions = 0;
        $attendanceStatuses = collect(['present' => 0, 'late' => 0, 'absent' => 0, 'excused' => 0]);
        $expiringSubscriptions = collect();
        $monthlyPayments = collect();

        if ($hasStudentModule) {
            $activeStudents = AcademyStudent::where('academy_id', $academyId)->where('status', 'active')->count();
            $activeGroups = AcademyGroup::where('academy_id', $academyId)->where('status', 'active')->count();

            $subscriptionScope = AcademyStudentSubscription::whereHas(
                'student',
                fn ($query) => $query->where('academy_id', $academyId)
            );
            $activeSubscriptions = (clone $subscriptionScope)->where('status', 'active')->count();
            $subscriptionTotal = (float) (clone $subscriptionScope)
                ->whereIn('status', ['pending', 'active', 'expired'])
                ->sum('amount');

            $paymentScope = AcademyStudentPayment::whereHas(
                'subscription.student',
                fn ($query) => $query->where('academy_id', $academyId)
            );
            $subscriptionRevenue = (float) (clone $paymentScope)->sum('amount');
            $outstandingSubscriptions = max(0, $subscriptionTotal - $subscriptionRevenue);

            $attendanceQuery = AcademyAttendanceRecord::whereHas(
                'session.group',
                fn ($query) => $query->where('academy_id', $academyId)
            )->whereHas('session', fn ($query) => $query->where('session_date', '>=', $now->copy()->subDays(29)));

            $attendanceStatuses = (clone $attendanceQuery)
                ->select('status')
                ->selectRaw('COUNT(*) as records_count')
                ->groupBy('status')
                ->pluck('records_count', 'status');

            $attendanceTotal = (int) $attendanceStatuses->sum();
            $attended = (int) ($attendanceStatuses->get('present', 0) + $attendanceStatuses->get('late', 0));
            $attendanceRate = $attendanceTotal > 0 ? round(($attended / $attendanceTotal) * 100, 1) : 0;

            $todaySessions = AcademyAttendanceSession::whereDate('session_date', $now->toDateString())
                ->whereHas('group', fn ($query) => $query->where('academy_id', $academyId))
                ->count();

            $expiringSubscriptions = (clone $subscriptionScope)
                ->with(['student:id,name,phone', 'group:id,name'])
                ->where('status', 'active')
                ->whereBetween('ends_on', [$now->toDateString(), $now->copy()->addDays(10)->toDateString()])
                ->orderBy('ends_on')
                ->limit(10)
                ->get();

            $monthlyPayments = (clone $paymentScope)
                ->where('paid_at', '>=', $monthStart->toDateString())
                ->selectRaw("DATE_FORMAT(paid_at, '%Y-%m') as month_key, COALESCE(SUM(amount), 0) as total")
                ->groupBy('month_key')
                ->pluck('total', 'month_key');
        }

        // Expenses breakdown data
        $monthlyExpenses = \App\Models\PartnerExpense::where('academy_id', $academyId)
            ->where('expense_date', '>=', $monthStart)
            ->selectRaw("DATE_FORMAT(expense_date, '%Y-%m') as month_key, COALESCE(SUM(COALESCE(base_amount, amount)), 0) as total")
            ->groupBy('month_key')
            ->pluck('total', 'month_key');

        $expenseCategoriesBreakdown = \App\Models\PartnerExpenseCategory::where(function ($q) use ($academyId) {
                $q->whereNull('academy_id')->orWhere('academy_id', $academyId);
            })
            ->withSum(['expenses' => function ($q) use ($academyId) {
                $q->where('academy_id', $academyId);
            }], DB::raw('COALESCE(base_amount, amount)'))
            ->get()
            ->map(function ($cat) {
                return [
                    'name' => app()->getLocale() === 'ar' ? $cat->name_ar : ($cat->name_en ?: $cat->name_ar),
                    'total' => (float) ($cat->expenses_sum_coalescebase_amount_amount ?? 0),
                ];
            })
            ->filter(fn ($cat) => $cat['total'] > 0)
            ->values();

        // Expiring subscriptions 10 to 0 days countdown data
        $expiringCountdown = collect(range(10, 0, -1))->map(function ($days) use ($academyId, $now) {
            $targetDate = $now->copy()->addDays($days)->toDateString();
            $count = \App\Models\AcademyStudentSubscription::whereHas('student', fn ($q) => $q->where('academy_id', $academyId))
                ->where('status', 'active')
                ->whereDate('ends_on', $targetDate)
                ->count();

            return [
                'days' => $days,
                'label' => $days === 0 
                    ? (app()->getLocale() === 'ar' ? 'ينتهي اليوم' : 'Expires Today') 
                    : ($days . ' ' . (app()->getLocale() === 'ar' ? 'أيام متبقية' : 'days left')),
                'count' => $count,
            ];
        });

        $monthlyBookings = Join::query()
            ->join('trainings', 'trainings.id', '=', 'joins.training_id')
            ->where('trainings.academy_id', $academyId)
            ->where('joins.created_at', '>=', $monthStart)
            ->selectRaw("DATE_FORMAT(joins.created_at, '%Y-%m') as month_key, COUNT(*) as bookings_count, COALESCE(SUM(joins.price), 0) as revenue")
            ->groupBy('month_key')
            ->get()
            ->keyBy('month_key');

        $months = collect(range(0, 11))->map(function ($offset) use ($monthStart, $monthlyBookings, $monthlyPayments, $monthlyExpenses) {
            $month = $monthStart->copy()->addMonths($offset);
            $key = $month->format('Y-m');
            $booking = $monthlyBookings->get($key);
            $bookingRev = round((float) ($booking->revenue ?? 0), 2);
            $subRev = round((float) ($monthlyPayments->get($key, 0)), 2);
            $totalRev = round($bookingRev + $subRev, 2);
            $exp = round((float) ($monthlyExpenses->get($key, 0)), 2);
            $net = round($totalRev - $exp, 2);

            return [
                'label' => $month->locale(app()->getLocale())->translatedFormat('M Y'),
                'bookings' => (int) ($booking->bookings_count ?? 0),
                'bookingRevenue' => $bookingRev,
                'subscriptionRevenue' => $subRev,
                'totalRevenue' => $totalRev,
                'expenses' => $exp,
                'netProfit' => $net,
            ];
        });

        $topTrainings = Training::query()
            ->where('academy_id', $academyId)
            ->withCount('joins')
            ->orderByDesc('joins_count')
            ->limit(6)
            ->get(['id', 'name'])
            ->map(fn ($training) => [
                'name' => $this->localizedValue($training->getRawOriginal('name')),
                'bookings' => (int) $training->joins_count,
            ]);

        // For Gyms or facilities with membership plans, include plan subscription counts
        $topGroups = AcademyGroup::where('academy_id', $academyId)
            ->withCount(['subscriptions' => fn($q) => $q->where('status', 'active')])
            ->orderByDesc('subscriptions_count')
            ->limit(6)
            ->get();

        if ($topGroups->sum('subscriptions_count') > 0) {
            $topTrainings = $topGroups->map(fn ($g) => [
                'name' => $this->localizedValue($g->name),
                'bookings' => (int) $g->subscriptions_count,
            ]);
        }

        $recentBookings = Join::query()
            ->with(['user:id,name,phone', 'training:id,name,academy_id', 'invoice:id,status,is_canceled'])
            ->whereHas('training', fn ($query) => $query->where('academy_id', $academyId))
            ->latest('joins.created_at')
            ->limit(6)
            ->get();

        $latestSettlement = Schema::hasTable('settlements')
            ? Settlement::where('partner_id', $academyId)->latest()->first()
            : null;

        $totalCollectedRevenue = (float) ($subscriptionRevenue + $totalRevenue);
        $totalExpenses = (float) \App\Models\PartnerExpense::where('academy_id', $academyId)->sum(DB::raw('COALESCE(base_amount, amount)'));
        $netProfit = (float) ($totalCollectedRevenue - $totalExpenses);
        $profitMargin = $totalCollectedRevenue > 0 ? round(($netProfit / $totalCollectedRevenue) * 100, 1) : 0;
        $isGymFacilityType = in_array($academy->business_type, ['gym', 'fitness_center', 'crossfit', 'health_club'], true);
        $coachesFinancial = $this->getCoachesFinancialSummary($academyId);

        $dashboard = [
            'academyName' => $this->localizedValue($academy->getRawOriginal('commercial_name')),
            'ownerName' => $academy->owner_name ?: $academy->first_name ?: $academy->name,
            'totalBookings' => $totalBookings,
            'totalRevenue' => $totalRevenue,
            'totalCollectedRevenue' => $totalCollectedRevenue,
            'totalExpenses' => $totalExpenses,
            'netProfit' => $netProfit,
            'profitMargin' => $profitMargin,
            'uniqueCustomers' => $uniqueCustomers,
            'bookingTrend' => $this->percentageChange($currentBookings, $previousBookings),
            'totalTrainings' => $totalTrainings,
            'activeTrainings' => $activeTrainings,
            'totalCoaches' => $totalCoaches,
            'followers' => method_exists($academy, 'follows') ? $academy->follows()->count() : 0,
            'activeStudents' => $activeStudents,
            'activeGroups' => $activeGroups,
            'activeSubscriptions' => $activeSubscriptions,
            'subscriptionRevenue' => $subscriptionRevenue,
            'outstandingSubscriptions' => $outstandingSubscriptions,
            'attendanceRate' => $attendanceRate,
            'todaySessions' => $todaySessions,
            'latestSettlement' => $latestSettlement,
            'monthLabels' => $months->pluck('label'),
            'monthlyBookings' => $months->pluck('bookings'),
            'monthlyBookingRevenue' => $months->pluck('bookingRevenue'),
            'monthlySubscriptionRevenue' => $months->pluck('subscriptionRevenue'),
            'monthlyTotalRevenue' => $months->pluck('totalRevenue'),
            'monthlyExpenses' => $months->pluck('expenses'),
            'monthlyNetProfit' => $months->pluck('netProfit'),
            'expenseCategories' => $expenseCategoriesBreakdown->pluck('name'),
            'expenseCategoryTotals' => $expenseCategoriesBreakdown->pluck('total'),
            'expiringCountdownLabels' => $expiringCountdown->pluck('label'),
            'expiringCountdownCounts' => $expiringCountdown->pluck('count'),
            'attendanceStatuses' => [
                (int) $attendanceStatuses->get('present', 0),
                (int) $attendanceStatuses->get('late', 0),
                (int) $attendanceStatuses->get('absent', 0),
                (int) $attendanceStatuses->get('excused', 0),
            ],
            'topTrainings' => $topTrainings,
            'recentBookings' => $recentBookings,
            'expiringSubscriptions' => $expiringSubscriptions,
            'hasStudentModule' => $hasStudentModule,
            'venue' => $venueDashboard,
            'dashboardMode' => $academy->business_type === 'hybrid' ? 'hybrid' : 'academy',
            'paymentBreakdown' => $this->getPaymentMethodBreakdown($academyId),
            'partialPayments' => $this->getStudentPartialPaymentsSummary($academyId),
            'coachesFinancial' => $coachesFinancial,
            'currencySymbol' => $academy->currency_symbol ?: (app()->getLocale() === 'ar' ? 'ج.م' : 'EGP'),
            'isGym' => $isGymFacilityType,
        ];

        return view('Academy.index', compact('dashboard'));
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('[Dashboard Exception] ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response('<div style="padding:25px;background:#fff5f5;border:1px solid #feb2b2;border-radius:8px;font-family:sans-serif;direction:ltr;">'
                . '<h3 style="color:#c53030;margin-top:0;">[Hagzz Dashboard Diagnostic Error]</h3>'
                . '<p><b>Message:</b> ' . e($e->getMessage()) . '</p>'
                . '<p><b>File:</b> ' . e($e->getFile()) . ':' . e($e->getLine()) . '</p>'
                . '<details open><summary><b>Stack Trace:</b></summary><pre style="background:#edf2f7;padding:10px;border-radius:4px;max-height:400px;overflow:auto;">' . e($e->getTraceAsString()) . '</pre></details>'
                . '</div>', 500);
        }
    }

    private function venueDashboardData(int $academyId, Carbon $monthStart): array
    {
        $base = VenueBooking::where('academy_id', $academyId);
        $today = (clone $base)->whereDate('starts_at', today());
        $currentStart = now()->subDays(29)->startOfDay();
        $previousStart = $currentStart->copy()->subDays(30);
        $currentCount = (clone $base)->where('created_at', '>=', $currentStart)->count();
        $previousCount = (clone $base)->whereBetween('created_at', [$previousStart, $currentStart])->count();

        $monthly = (clone $base)->where('starts_at', '>=', $monthStart)
            ->where('status', '!=', 'cancelled')
            ->selectRaw("DATE_FORMAT(starts_at, '%Y-%m') as month_key, COUNT(*) as bookings_count, COALESCE(SUM(total_amount),0) as revenue, COALESCE(SUM(paid_amount),0) as collected")
            ->groupBy('month_key')->get()->keyBy('month_key');
        $months = collect(range(0, 11))->map(function ($offset) use ($monthStart, $monthly) {
            $month = $monthStart->copy()->addMonths($offset);
            $row = $monthly->get($month->format('Y-m'));
            return [
                'label' => $month->locale(app()->getLocale())->translatedFormat('M Y'),
                'bookings' => (int) ($row->bookings_count ?? 0),
                'revenue' => (float) ($row->revenue ?? 0),
                'collected' => (float) ($row->collected ?? 0),
            ];
        });
        $statuses = (clone $base)->select('status')->selectRaw('COUNT(*) as total')->groupBy('status')->pluck('total', 'status');
        $topSpaces = VenueSpace::whereHas('venue', fn ($query) => $query->where('academy_id', $academyId))
            ->with('venue')->withCount(['bookings' => fn ($query) => $query->where('status', '!=', 'cancelled')])
            ->orderByDesc('bookings_count')->limit(6)->get();

        return [
            'venues' => Venue::where('academy_id', $academyId)->where('active', true)->count(),
            'spaces' => VenueSpace::whereHas('venue', fn ($query) => $query->where('academy_id', $academyId))->where('active', true)->count(),
            'todayBookings' => (clone $today)->where('status', '!=', 'cancelled')->count(),
            'todayCollected' => (float) (clone $today)->where('status', '!=', 'cancelled')->sum('paid_amount'),
            'upcoming' => (clone $base)->where('starts_at', '>', now())->whereIn('status', ['pending', 'confirmed'])->count(),
            'totalBookings' => (clone $base)->where('status', '!=', 'cancelled')->count(),
            'totalRevenue' => (float) (clone $base)->where('status', '!=', 'cancelled')->sum('total_amount'),
            'totalCollected' => (float) (clone $base)->where('status', '!=', 'cancelled')->sum('paid_amount'),
            'outstanding' => max(0, (float) (clone $base)->where('status', '!=', 'cancelled')->sum('total_amount') - (float) (clone $base)->where('status', '!=', 'cancelled')->sum('paid_amount')),
            'bookingTrend' => $this->percentageChange($currentCount, $previousCount),
            'monthLabels' => $months->pluck('label'), 'monthlyBookings' => $months->pluck('bookings'),
            'monthlyRevenue' => $months->pluck('revenue'), 'monthlyCollected' => $months->pluck('collected'),
            'statuses' => collect(['pending','confirmed','checked_in','completed','cancelled','no_show'])->map(fn ($status) => (int) $statuses->get($status, 0)),
            'topSpaces' => $topSpaces,
            'recentBookings' => (clone $base)->with(['space.venue','customer'])->latest('created_at')->limit(7)->get(),
        ];
    }

    public function filterBookings(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $academyId = auth('academy')->id();

        $totalBookingBalance = $this->getTotalBookingBalance($startDate, $endDate, $academyId);
        $totalBookingRefundCount = $this->getTotalBookingRefundCount($startDate, $endDate, $academyId);
        $totalBookingRefundAmount = $this->getTotalBookingRefundAmount($startDate, $endDate, $academyId);
        $totalBookingCount = $this->getTotalBookingCount($startDate, $endDate, $academyId);

        return response()->json([
            'total_booking_balance' => $totalBookingBalance,
            'total_booking_refund_count' => $totalBookingRefundCount,
            'total_booking_refund_amount' => $totalBookingRefundAmount,
            'total_booking_count' => $totalBookingCount,
        ]);
    }

    public function getRevenueDataByMonth()
    {
        $ordersData = $this->chartsService->getBookingsDataByMonth();

        return response()->json([
            'ordersData' => $ordersData['joinsData'],
            'totalProfit' => $ordersData['total']
        ]);
    }

    public function getUserDataByMonthAjax(Request $request): JsonResponse
    {
        $maleUsersByMonth = User::select('id')
            ->whereGender('male')
            ->whereMonth('created_at', now()->month)
            ->get()
            ->count();

        $femaleUsersByMonth = User::select('id')
            ->whereGender('female')
            ->whereMonth('created_at', now()->month)
            ->get()
            ->count();

        return Response::json(['maleUsersByMonth' => $maleUsersByMonth, 'femaleUsersByMonth' => $femaleUsersByMonth]);
    }

    public function getUserDataByYearAjax(Request $request): JsonResponse
    {
        $maleUsersByYear = User::select('id')
            ->whereGender('male')
            ->whereYear('created_at', now()->year)
            ->count();

        $femaleUsersByYear = User::select('id')
            ->whereGender('female')
            ->whereYear('created_at', now()->year)
            ->count();

        return Response::json(['maleUsersByYear' => $maleUsersByYear, 'femaleUsersByYear' => $femaleUsersByYear]);
    }

    private function getAcademyId(): int
    {
        $user = auth('academy')->user();
        if (!$user) {
            return 0;
        }
        $academy = ($user instanceof \App\Models\PartnerUser && $user->academy) ? $user->academy : $user;
        return (int) ($user->academy_id ?? $academy->id ?? 0);
    }

    public function getBeginnerSportsCount()
    {
        try {
            $academyId = $this->getAcademyId();
            return Sport::select('sports.id')
                ->join('academy_sport', 'sports.id', '=', 'academy_sport.sport_id')
                ->join('user_sport', 'sports.id', '=', 'user_sport.sport_id')
                ->join('trainings', 'trainings.sport_id', '=', 'sports.id')
                ->join('joins', 'joins.training_id', '=', 'trainings.id')
                ->where('user_sport.level', 'Beginner')
                ->where('academy_sport.academy_id', $academyId)
                ->where('trainings.academy_id', $academyId)
                ->whereColumn('joins.user_id', 'user_sport.user_id')
                ->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function getIntermediateSportsCount()
    {
        try {
            $academyId = $this->getAcademyId();
            return Sport::select('sports.id')
                ->join('academy_sport', 'sports.id', '=', 'academy_sport.sport_id')
                ->join('user_sport', 'sports.id', '=', 'user_sport.sport_id')
                ->join('trainings', 'trainings.sport_id', '=', 'sports.id')
                ->join('joins', 'joins.training_id', '=', 'trainings.id')
                ->where('user_sport.level', 'Intermediate')
                ->where('academy_sport.academy_id', $academyId)
                ->where('trainings.academy_id', $academyId)
                ->whereColumn('joins.user_id', 'user_sport.user_id')
                ->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function getAdvancedSportsCount()
    {
        try {
            $academyId = $this->getAcademyId();
            return Sport::select('sports.id')
                ->join('academy_sport', 'sports.id', '=', 'academy_sport.sport_id')
                ->join('user_sport', 'sports.id', '=', 'user_sport.sport_id')
                ->join('trainings', 'trainings.sport_id', '=', 'sports.id')
                ->join('joins', 'joins.training_id', '=', 'trainings.id')
                ->where('user_sport.level', 'Advanced')
                ->where('academy_sport.academy_id', $academyId)
                ->where('trainings.academy_id', $academyId)
                ->whereColumn('joins.user_id', 'user_sport.user_id')
                ->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function getAllMaleUsersCount()
    {
        try {
            $academyId = $this->getAcademyId();
            return User::whereHas('joins.training', function ($query) use ($academyId) {
                $query->where('academy_id', $academyId);
            })->select('id')->whereGender('male')->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    private function getAllFemaleUsersCount()
    {
        try {
            $academyId = $this->getAcademyId();
            return User::whereHas('joins.training', function ($query) use ($academyId) {
                $query->where('academy_id', $academyId);
            })->select('id')->whereGender('female')->count();
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function getUsersBooking()
    {
        try {
            $academyId = $this->getAcademyId();
            return Join::whereHas('training', function ($query) use ($academyId) {
                $query->where('academy_id', $academyId);
            })->get()->unique('user_id');
        } catch (\Throwable $e) {
            return collect();
        }
    }

    private function getUserBookingLast7Days()
    {
        try {
            $academyId = $this->getAcademyId();
            return Join::whereHas('training', function ($query) use ($academyId) {
                $query->where('academy_id', $academyId);
            })
                ->where('created_at', '>=', Carbon::now()->subDays(7))
                ->get()
                ->unique('user_id');
        } catch (\Throwable $e) {
            return collect();
        }
    }

    public function getUnreadNotificationCount()
    {
        $user = auth('academy')->user();
        $count = 0;
        if ($user) {
            try {
                $count = $user->unreadNotifications()->count();
            } catch (\Throwable $e) {
                $count = 0;
            }
        }
        return response()->json([
            'unread_count' => $count
        ]);
    }

    private function percentageChange(int $current, int $previous): float
    {
        if ($previous === 0) {
            return $current > 0 ? 100 : 0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function localizedValue(?string $value): string
    {
        if (blank($value)) {
            return app()->getLocale() === 'ar' ? 'غير محدد' : 'Not specified';
        }

        $translations = json_decode($value, true);
        if (!is_array($translations)) {
            return $value;
        }

        return $translations[app()->getLocale()]
            ?? $translations['en']
            ?? $translations['ar']
            ?? reset($translations)
            ?? $value;
    }

    private function getPaymentMethodBreakdown(int $academyId): array
    {
        $isArabic = app()->getLocale() === 'ar';

        $academy = Academies::with('country')->find($academyId);
        $iso2 = strtoupper((string) ($academy?->country?->iso2 ?? ''));
        if (!$iso2 && $academy) {
            $curr = $academy->currency_code;
            $iso2 = match ($curr) {
                'EGP' => 'EG',
                'SAR' => 'SA',
                'QAR' => 'QA',
                default => 'EG',
            };
        }
        $academyIso2 = in_array($iso2, ['EG', 'SA', 'QA'], true) ? $iso2 : 'EG';
        $academyCurrency = $academy?->currency_symbol ?: ($isArabic ? 'ج.م' : 'EGP');

        $studentPayments = AcademyStudentPayment::query()
            ->whereHas('subscription.student', fn ($q) => $q->where('academy_id', $academyId))
            ->with(['subscription.student.country'])
            ->get();

        $invoicePayments = Invoice::query()
            ->whereHas('training', fn ($q) => $q->where('academy_id', $academyId))
            ->with(['training.address.country', 'user.country'])
            ->get();

        $countryMethods = [
            'EG' => [],
            'SA' => [],
            'QA' => [],
        ];
        $allMethods = [];

        foreach ($studentPayments as $p) {
            $m = strtolower(trim((string)$p->method)) ?: 'cash';
            $amount = (float) $p->amount;

            $cIso = strtoupper((string) ($p->subscription?->student?->country?->iso2 ?? ''));
            if (!in_array($cIso, ['EG', 'SA', 'QA'], true)) {
                $cIso = $academyIso2;
            }

            $countryMethods[$cIso][$m]['amount'] = ($countryMethods[$cIso][$m]['amount'] ?? 0) + $amount;
            $countryMethods[$cIso][$m]['count'] = ($countryMethods[$cIso][$m]['count'] ?? 0) + 1;

            $allMethods[$m]['amount'] = ($allMethods[$m]['amount'] ?? 0) + $amount;
            $allMethods[$m]['count'] = ($allMethods[$m]['count'] ?? 0) + 1;
        }

        foreach ($invoicePayments as $p) {
            $m = strtolower(trim((string)$p->payment_method)) ?: 'cash';
            $amount = (float) ($p->paid_amount ?? $p->amount);

            $cIso = strtoupper((string) ($p->training?->address?->country?->iso2 ?? ($p->user?->country?->iso2 ?? '')));
            if (!in_array($cIso, ['EG', 'SA', 'QA'], true)) {
                $cIso = $academyIso2;
            }

            $countryMethods[$cIso][$m]['amount'] = ($countryMethods[$cIso][$m]['amount'] ?? 0) + $amount;
            $countryMethods[$cIso][$m]['count'] = ($countryMethods[$cIso][$m]['count'] ?? 0) + 1;

            $allMethods[$m]['amount'] = ($allMethods[$m]['amount'] ?? 0) + $amount;
            $allMethods[$m]['count'] = ($allMethods[$m]['count'] ?? 0) + 1;
        }

        $getCountryAmount = function(string $country, $keys) use (&$countryMethods) {
            $total = 0.0;
            foreach ((array)$keys as $k) {
                if (isset($countryMethods[$country][$k])) {
                    $total += $countryMethods[$country][$k]['amount'];
                }
            }
            return round($total, 2);
        };

        $getCountryCount = function(string $country, $keys) use (&$countryMethods) {
            $total = 0;
            foreach ((array)$keys as $k) {
                if (isset($countryMethods[$country][$k])) {
                    $total += $countryMethods[$country][$k]['count'];
                }
            }
            return $total;
        };

        $getAllAmount = function($keys) use (&$allMethods) {
            $total = 0.0;
            foreach ((array)$keys as $k) {
                if (isset($allMethods[$k])) {
                    $total += $allMethods[$k]['amount'];
                }
            }
            return round($total, 2);
        };

        $getAllCount = function($keys) use (&$allMethods) {
            $total = 0;
            foreach ((array)$keys as $k) {
                if (isset($allMethods[$k])) {
                    $total += $allMethods[$k]['count'];
                }
            }
            return $total;
        };

        $egData = [
            'country_code' => 'EG',
            'country_name' => $isArabic ? 'مصر' : 'Egypt',
            'flag' => '🇪🇬',
            'currency' => $isArabic ? 'ج.م' : 'EGP',
            'methods' => [
                [
                    'key' => 'instapay',
                    'name' => $isArabic ? 'إنستا باي (InstaPay)' : 'InstaPay',
                    'amount' => $getCountryAmount('EG', ['instapay']),
                    'count' => $getCountryCount('EG', ['instapay']),
                    'color' => '#8b5cf6',
                    'logo' => asset('assetsAdmin/img/payments/instapay.png'),
                    'icon' => 'zap',
                ],
                [
                    'key' => 'fawry',
                    'name' => $isArabic ? 'فوري (Fawry)' : 'Fawry',
                    'amount' => $getCountryAmount('EG', ['fawry']),
                    'count' => $getCountryCount('EG', ['fawry']),
                    'color' => '#f59e0b',
                    'logo' => asset('assetsAdmin/img/payments/fawry.png'),
                    'icon' => 'dollar-sign',
                ],
                [
                    'key' => 'card',
                    'name' => $isArabic ? 'كارت / فيزا (Card)' : 'Card / Visa',
                    'amount' => $getCountryAmount('EG', ['card', 'visa', 'mastercard', 'online', 'app_online', 'card_bank']),
                    'count' => $getCountryCount('EG', ['card', 'visa', 'mastercard', 'online', 'app_online', 'card_bank']),
                    'color' => '#3b82f6',
                    'logo' => asset('assetsAdmin/img/payments/card_bank.png'),
                    'icon' => 'credit-card',
                ],
                [
                    'key' => 'cash',
                    'name' => $isArabic ? 'كاش ونقدي (Cash)' : 'Cash',
                    'amount' => $getCountryAmount('EG', ['cash']),
                    'count' => $getCountryCount('EG', ['cash']),
                    'color' => '#10b981',
                    'logo' => asset('assetsAdmin/img/payments/cash.png'),
                    'icon' => 'dollar-sign',
                ],
                [
                    'key' => 'wallet',
                    'name' => $isArabic ? 'محفظة إلكترونية' : 'E-Wallet',
                    'amount' => $getCountryAmount('EG', ['wallet', 'e_wallet', 'vodafone_cash']),
                    'count' => $getCountryCount('EG', ['wallet', 'e_wallet', 'vodafone_cash']),
                    'color' => '#ef4444',
                    'logo' => asset('assetsAdmin/img/payments/wallet.jpg'),
                    'icon' => 'smartphone',
                ],
                [
                    'key' => 'bank_transfer',
                    'name' => $isArabic ? 'تحويل بنكي' : 'Bank Transfer',
                    'amount' => $getCountryAmount('EG', ['bank_transfer', 'bank']),
                    'count' => $getCountryCount('EG', ['bank_transfer', 'bank']),
                    'color' => '#06b6d4',
                    'logo' => asset('assetsAdmin/img/payments/bank_transfer.jpg'),
                    'icon' => 'send',
                ],
            ],
        ];

        $saData = [
            'country_code' => 'SA',
            'country_name' => $isArabic ? 'السعودية' : 'Saudi Arabia',
            'flag' => '🇸🇦',
            'currency' => $isArabic ? 'ر.س' : 'SAR',
            'methods' => [
                [
                    'key' => 'mada',
                    'name' => $isArabic ? 'مدى (Mada)' : 'Mada',
                    'amount' => $getCountryAmount('SA', ['mada', 'card', 'visa', 'online']),
                    'count' => $getCountryCount('SA', ['mada', 'card', 'visa', 'online']),
                    'color' => '#059669',
                    'logo' => asset('assetsAdmin/img/payments/mada.png'),
                    'icon' => 'credit-card',
                ],
                [
                    'key' => 'stc_pay',
                    'name' => $isArabic ? 'STC Pay' : 'STC Pay',
                    'amount' => $getCountryAmount('SA', ['stc_pay', 'stc']),
                    'count' => $getCountryCount('SA', ['stc_pay', 'stc']),
                    'color' => '#7c3aed',
                    'logo' => asset('assetsAdmin/img/payments/stc_pay.png'),
                    'icon' => 'smartphone',
                ],
                [
                    'key' => 'apple_pay',
                    'name' => $isArabic ? 'Apple Pay' : 'Apple Pay',
                    'amount' => $getCountryAmount('SA', ['apple_pay', 'apple']),
                    'count' => $getCountryCount('SA', ['apple_pay', 'apple']),
                    'color' => '#000000',
                    'logo' => asset('assetsAdmin/img/payments/apple_pay.png'),
                    'icon' => 'smartphone',
                ],
                [
                    'key' => 'sadad_ksa',
                    'name' => $isArabic ? 'سداد (Sadad KSA)' : 'Sadad KSA',
                    'amount' => $getCountryAmount('SA', ['sadad_ksa', 'sadad']),
                    'count' => $getCountryCount('SA', ['sadad_ksa', 'sadad']),
                    'color' => '#0284c7',
                    'logo' => asset('assetsAdmin/img/payments/sadad_ksa.png'),
                    'icon' => 'shield',
                ],
                [
                    'key' => 'cash',
                    'name' => $isArabic ? 'كاش ونقدي (Cash)' : 'Cash',
                    'amount' => $getCountryAmount('SA', ['cash']),
                    'count' => $getCountryCount('SA', ['cash']),
                    'color' => '#10b981',
                    'logo' => asset('assetsAdmin/img/payments/cash.png'),
                    'icon' => 'dollar-sign',
                ],
                [
                    'key' => 'bank_transfer',
                    'name' => $isArabic ? 'تحويل بنكي' : 'Bank Transfer',
                    'amount' => $getCountryAmount('SA', ['bank_transfer', 'bank']),
                    'count' => $getCountryCount('SA', ['bank_transfer', 'bank']),
                    'color' => '#06b6d4',
                    'logo' => asset('assetsAdmin/img/payments/bank_transfer.jpg'),
                    'icon' => 'send',
                ],
            ],
        ];

        $qaData = [
            'country_code' => 'QA',
            'country_name' => $isArabic ? 'قطر' : 'Qatar',
            'flag' => '🇶🇦',
            'currency' => $isArabic ? 'ر.ق' : 'QAR',
            'methods' => [
                [
                    'key' => 'naps',
                    'name' => $isArabic ? 'بطاقة الخصم (NAPS قطر)' : 'NAPS Qatar',
                    'amount' => $getCountryAmount('QA', ['naps', 'local_card']),
                    'count' => $getCountryCount('QA', ['naps', 'local_card']),
                    'color' => '#800020',
                    'logo' => asset('assetsAdmin/img/payments/naps.jpg'),
                    'icon' => 'credit-card',
                ],
                [
                    'key' => 'fawran',
                    'name' => $isArabic ? 'فوران قطر (Fawran)' : 'Fawran Qatar',
                    'amount' => $getCountryAmount('QA', ['fawran']),
                    'count' => $getCountryCount('QA', ['fawran']),
                    'color' => '#b91c1c',
                    'logo' => asset('assetsAdmin/img/payments/fawran.jpg'),
                    'icon' => 'zap',
                ],
                [
                    'key' => 'sadad_qa',
                    'name' => $isArabic ? 'سداد قطر (Sadad QA)' : 'Sadad Qatar',
                    'amount' => $getCountryAmount('QA', ['sadad_qa', 'sadad']),
                    'count' => $getCountryCount('QA', ['sadad_qa', 'sadad']),
                    'color' => '#0369a1',
                    'logo' => asset('assetsAdmin/img/payments/sadad_qa.png'),
                    'icon' => 'shield',
                ],
                [
                    'key' => 'card',
                    'name' => $isArabic ? 'بطاقة بنكية (Card)' : 'Bank Card',
                    'amount' => $getCountryAmount('QA', ['card', 'visa', 'online', 'card_bank']),
                    'count' => $getCountryCount('QA', ['card', 'visa', 'online', 'card_bank']),
                    'color' => '#3b82f6',
                    'logo' => asset('assetsAdmin/img/payments/card_bank.png'),
                    'icon' => 'credit-card',
                ],
                [
                    'key' => 'apple_pay',
                    'name' => $isArabic ? 'Apple Pay' : 'Apple Pay',
                    'amount' => $getCountryAmount('QA', ['apple_pay', 'apple']),
                    'count' => $getCountryCount('QA', ['apple_pay', 'apple']),
                    'color' => '#000000',
                    'logo' => asset('assetsAdmin/img/payments/apple_pay.png'),
                    'icon' => 'smartphone',
                ],
                [
                    'key' => 'cash',
                    'name' => $isArabic ? 'كاش ونقدي (Cash)' : 'Cash',
                    'amount' => $getCountryAmount('QA', ['cash']),
                    'count' => $getCountryCount('QA', ['cash']),
                    'color' => '#10b981',
                    'logo' => asset('assetsAdmin/img/payments/cash.png'),
                    'icon' => 'dollar-sign',
                ],
            ],
        ];

        $allData = [
            'country_code' => 'ALL',
            'country_name' => $isArabic ? 'جميع الدول' : 'All Countries',
            'flag' => '🌐',
            'currency' => $academyCurrency,
            'methods' => [
                [
                    'key' => 'cash',
                    'name' => $isArabic ? 'كاش ونقدي (Cash)' : 'Cash',
                    'amount' => $getAllAmount(['cash']),
                    'count' => $getAllCount(['cash']),
                    'color' => '#10b981',
                    'logo' => asset('assetsAdmin/img/payments/cash.png'),
                    'icon' => 'dollar-sign',
                ],
                [
                    'key' => 'mada',
                    'name' => $isArabic ? 'مدى (Mada)' : 'Mada',
                    'amount' => $getAllAmount(['mada']),
                    'count' => $getAllCount(['mada']),
                    'color' => '#059669',
                    'logo' => asset('assetsAdmin/img/payments/mada.png'),
                    'icon' => 'credit-card',
                ],
                [
                    'key' => 'card',
                    'name' => $isArabic ? 'كارت / بطاقات بنكية' : 'Card / Bank Cards',
                    'amount' => $getAllAmount(['card', 'visa', 'mastercard', 'online', 'app_online', 'card_bank']),
                    'count' => $getAllCount(['card', 'visa', 'mastercard', 'online', 'app_online', 'card_bank']),
                    'color' => '#3b82f6',
                    'logo' => asset('assetsAdmin/img/payments/card_bank.png'),
                    'icon' => 'credit-card',
                ],
                [
                    'key' => 'apple_pay',
                    'name' => $isArabic ? 'Apple Pay' : 'Apple Pay',
                    'amount' => $getAllAmount(['apple_pay', 'apple']),
                    'count' => $getAllCount(['apple_pay', 'apple']),
                    'color' => '#000000',
                    'logo' => asset('assetsAdmin/img/payments/apple_pay.png'),
                    'icon' => 'smartphone',
                ],
                [
                    'key' => 'instapay',
                    'name' => $isArabic ? 'إنستا باي (InstaPay)' : 'InstaPay',
                    'amount' => $getAllAmount(['instapay']),
                    'count' => $getAllCount(['instapay']),
                    'color' => '#8b5cf6',
                    'logo' => asset('assetsAdmin/img/payments/instapay.png'),
                    'icon' => 'zap',
                ],
                [
                    'key' => 'fawry',
                    'name' => $isArabic ? 'فوري (Fawry)' : 'Fawry',
                    'amount' => $getAllAmount(['fawry']),
                    'count' => $getAllCount(['fawry']),
                    'color' => '#f59e0b',
                    'logo' => asset('assetsAdmin/img/payments/fawry.png'),
                    'icon' => 'dollar-sign',
                ],
                [
                    'key' => 'stc_pay',
                    'name' => $isArabic ? 'STC Pay' : 'STC Pay',
                    'amount' => $getAllAmount(['stc_pay', 'stc']),
                    'count' => $getAllCount(['stc_pay', 'stc']),
                    'color' => '#7c3aed',
                    'logo' => asset('assetsAdmin/img/payments/stc_pay.png'),
                    'icon' => 'smartphone',
                ],
                [
                    'key' => 'bank_transfer',
                    'name' => $isArabic ? 'تحويل بنكي' : 'Bank Transfer',
                    'amount' => $getAllAmount(['bank_transfer', 'bank']),
                    'count' => $getAllCount(['bank_transfer', 'bank']),
                    'color' => '#06b6d4',
                    'logo' => asset('assetsAdmin/img/payments/bank_transfer.jpg'),
                    'icon' => 'send',
                ],
            ],
        ];

        return [
            'countries' => [
                'ALL' => $allData,
                'EG' => $egData,
                'SA' => $saData,
                'QA' => $qaData,
            ],
            'defaultCountry' => $academyIso2,
        ];
    }

    private function getStudentPartialPaymentsSummary(int $academyId): array
    {
        $isArabic = app()->getLocale() === 'ar';
        $academy = Academies::with('country')->find($academyId);
        $currency = $academy ? $academy->currency_symbol : ($isArabic ? 'ج.م' : 'EGP');

        $subscriptions = AcademyStudentSubscription::with(['student', 'group.training'])
            ->withSum('payments', 'amount')
            ->whereHas('student', fn ($q) => $q->where('academy_id', $academyId))
            ->where('status', '!=', 'cancelled')
            ->get();

        $fullyPaidSubCount = 0;
        $fullyPaidSubAmount = 0.0;
        $partialSubCount = 0;
        $partialSubCollected = 0.0;
        $partialSubRemaining = 0.0;
        $unpaidSubCount = 0;
        $unpaidSubRemaining = 0.0;

        $partialStudents = [];

        foreach ($subscriptions as $sub) {
            $amount = (float) $sub->amount;
            $paid = (float) ($sub->payments_sum_amount ?? 0);
            $remaining = max(0, $amount - $paid);

            if ($paid >= $amount && $amount > 0) {
                $fullyPaidSubCount++;
                $fullyPaidSubAmount += $paid;
            } elseif ($paid > 0 && $remaining > 0) {
                $partialSubCount++;
                $partialSubCollected += $paid;
                $partialSubRemaining += $remaining;

                $partialStudents[] = [
                    'type' => 'subscription',
                    'student_name' => $sub->student?->name ?? ($isArabic ? 'لاعب غير محدد' : 'Unknown Player'),
                    'phone' => $sub->student?->phone ?: $sub->student?->guardian_phone ?: '',
                    'service_name' => $sub->group?->name ?: ($sub->group?->training?->name ?: ($isArabic ? 'اشتراك تدريب' : 'Training Subscription')),
                    'amount' => $amount,
                    'paid_amount' => $paid,
                    'remaining_amount' => $remaining,
                    'currency' => $currency,
                ];
            } else {
                $unpaidSubCount++;
                $unpaidSubRemaining += $amount;

                if ($amount > 0) {
                    $partialStudents[] = [
                        'type' => 'subscription',
                        'student_name' => $sub->student?->name ?? ($isArabic ? 'لاعب غير محدد' : 'Unknown Player'),
                        'phone' => $sub->student?->phone ?: $sub->student?->guardian_phone ?: '',
                        'service_name' => $sub->group?->name ?: ($sub->group?->training?->name ?: ($isArabic ? 'اشتراك تدريب' : 'Training Subscription')),
                        'amount' => $amount,
                        'paid_amount' => $paid,
                        'remaining_amount' => $remaining,
                        'currency' => $currency,
                    ];
                }
            }
        }

        $invoices = Invoice::with(['user', 'training'])
            ->whereHas('training', fn ($q) => $q->where('academy_id', $academyId))
            ->where('is_canceled', false)
            ->get();

        foreach ($invoices as $inv) {
            $amount = (float) $inv->amount;
            $rawStatus = (string) $inv->getRawOriginal('status');
            $paid = (float) ($inv->paid_amount ?? ($rawStatus === 'paid' ? $amount : 0));
            $remaining = max(0, $amount - $paid);

            if ($paid >= $amount && $amount > 0) {
                $fullyPaidSubCount++;
                $fullyPaidSubAmount += $paid;
            } elseif ($paid > 0 && $remaining > 0) {
                $partialSubCount++;
                $partialSubCollected += $paid;
                $partialSubRemaining += $remaining;

                $partialStudents[] = [
                    'type' => 'invoice',
                    'student_name' => $inv->user?->name ?? ($isArabic ? 'طالب/عميل' : 'Customer'),
                    'phone' => $inv->user?->phone ?: '',
                    'service_name' => $inv->training?->name ?: ($isArabic ? 'حجز تدريب' : 'Training Booking'),
                    'amount' => $amount,
                    'paid_amount' => $paid,
                    'remaining_amount' => $remaining,
                    'currency' => $currency,
                ];
            } else {
                $unpaidSubCount++;
                $unpaidSubRemaining += $amount;
            }
        }

        usort($partialStudents, fn ($a, $b) => $b['remaining_amount'] <=> $a['remaining_amount']);
        $topPartialStudents = array_slice($partialStudents, 0, 6);

        $totalRemaining = $partialSubRemaining + $unpaidSubRemaining;

        return [
            'fullyPaidCount' => $fullyPaidSubCount,
            'fullyPaidAmount' => round($fullyPaidSubAmount, 2),
            'partialCount' => $partialSubCount,
            'partialCollected' => round($partialSubCollected, 2),
            'partialRemaining' => round($partialSubRemaining, 2),
            'unpaidCount' => $unpaidSubCount,
            'unpaidRemaining' => round($unpaidSubRemaining, 2),
            'totalRemaining' => round($totalRemaining, 2),
            'currency' => $currency,
            'topStudents' => $topPartialStudents,
        ];
    }

    private function getCoachesFinancialSummary(int $academyId): array
    {
        $coaches = Coach::where('academy_id', $academyId)->get();
        $isAr = app()->getLocale() === 'ar';
        $items = [];
        $totalCoachCost = 0.0;
        $totalCoachPaid = 0.0;

        foreach ($coaches as $coach) {
            $assignedGroups = AcademyGroup::where('academy_id', $academyId)
                ->where(function ($q) use ($coach) {
                    $q->where('coach_id', $coach->id)
                      ->orWhereHas('training', fn ($t) => $t->where('coach_id', $coach->id));
                })
                ->with(['subscriptions.payments'])
                ->get();

            $assignedTrainings = Training::where('academy_id', $academyId)
                ->where('coach_id', $coach->id)
                ->with(['joins'])
                ->get();

            $enrolledCount = 0;
            $totalBilled = 0.0;
            $totalCollected = 0.0;

            foreach ($assignedTrainings as $training) {
                $enrolledCount += $training->joins->count();
                $totalBilled += (float) $training->joins->sum('price');
                $totalCollected += (float) $training->joins->sum('net_amount');
            }

            foreach ($assignedGroups as $group) {
                foreach ($group->subscriptions as $sub) {
                    $enrolledCount++;
                    $totalBilled += (float) $sub->amount;
                    $totalCollected += (float) $sub->payments->sum('amount');
                }
            }

            $compType = $coach->compensation_type ?: 'session';
            $compVal = (float) ($coach->compensation_value ?? 0);
            $coachCost = 0.0;

            $conductedSessions = AcademyAttendanceSession::whereHas('group', fn ($q) => 
                $q->where('academy_id', $academyId)->where(function ($gq) use ($coach) {
                    $gq->where('coach_id', $coach->id)
                       ->orWhereHas('training', fn ($t) => $t->where('coach_id', $coach->id));
                })
            )->count();

            if ($compType === 'session') {
                $sessionCount = $conductedSessions > 0 ? $conductedSessions : max(1, ($assignedGroups->count() + $assignedTrainings->count()) * 4);
                $coachCost = $compVal > 0 ? ($sessionCount * $compVal) : 0;
                $compLabel = number_format($compVal, 2) . ($isAr ? ' ج.م / للحصة' : ' EGP / session');
            } elseif ($compType === 'percentage') {
                $coachCost = ($totalCollected * $compVal) / 100;
                $compLabel = $compVal . ($isAr ? '% من الإيراد' : '% of revenue');
            } else {
                $coachCost = $compVal; // fixed monthly salary
                $compLabel = number_format($compVal, 2) . ($isAr ? ' ج.م راتب شهري' : ' EGP salary');
            }

            $actualPaid = (float) \App\Models\PartnerExpense::where('academy_id', $academyId)
                ->where('coach_id', $coach->id)
                ->sum(DB::raw('COALESCE(base_amount, amount)'));

            $duesRemaining = max(0, $coachCost - $actualPaid);
            $totalCoachCost += $coachCost;
            $totalCoachPaid += $actualPaid;

            $items[] = [
                'id' => $coach->id,
                'name' => $this->localizedValue($coach->getRawOriginal('name')),
                'phone' => $coach->phone,
                'image' => $coach->image,
                'compensation_type' => $compType,
                'compensation_value' => $compVal,
                'compensation_label' => $compLabel,
                'enrolled_members' => $enrolledCount,
                'total_revenue' => $totalCollected,
                'coach_cost' => $coachCost,
                'actual_paid' => $actualPaid,
                'dues_remaining' => $duesRemaining,
            ];
        }

        return [
            'coaches' => $items,
            'totalCoachCost' => round($totalCoachCost, 2),
            'totalCoachPaid' => round($totalCoachPaid, 2),
            'totalCoachRemaining' => round(max(0, $totalCoachCost - $totalCoachPaid), 2),
            'totalEnrolledUnderCoaches' => collect($items)->sum('enrolled_members'),
        ];
    }
}
