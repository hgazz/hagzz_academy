<?php

namespace App\Http\Controllers;

use App\Http\Traits\BookingFilterTrait;
use App\Http\Traits\TrainingsTrait;
use App\Http\Traits\UsersTrait;
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
        $academy = auth('academy')->user();
        $academyId = $academy->id;
        $now = now();
        $monthStart = $now->copy()->subMonths(11)->startOfMonth();
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
                ->with(['student:id,name', 'group:id,name'])
                ->where('status', 'active')
                ->whereBetween('ends_on', [$now->toDateString(), $now->copy()->addDays(14)->toDateString()])
                ->orderBy('ends_on')
                ->limit(6)
                ->get();

            $monthlyPayments = (clone $paymentScope)
                ->where('paid_at', '>=', $monthStart->toDateString())
                ->selectRaw("DATE_FORMAT(paid_at, '%Y-%m') as month_key, COALESCE(SUM(amount), 0) as total")
                ->groupBy('month_key')
                ->pluck('total', 'month_key');
        }

        $monthlyBookings = Join::query()
            ->join('trainings', 'trainings.id', '=', 'joins.training_id')
            ->where('trainings.academy_id', $academyId)
            ->where('joins.created_at', '>=', $monthStart)
            ->selectRaw("DATE_FORMAT(joins.created_at, '%Y-%m') as month_key, COUNT(*) as bookings_count, COALESCE(SUM(joins.price), 0) as revenue")
            ->groupBy('month_key')
            ->get()
            ->keyBy('month_key');

        $months = collect(range(0, 11))->map(function ($offset) use ($monthStart, $monthlyBookings, $monthlyPayments) {
            $month = $monthStart->copy()->addMonths($offset);
            $key = $month->format('Y-m');
            $booking = $monthlyBookings->get($key);

            return [
                'label' => $month->locale(app()->getLocale())->translatedFormat('M Y'),
                'bookings' => (int) ($booking->bookings_count ?? 0),
                'bookingRevenue' => round((float) ($booking->revenue ?? 0), 2),
                'subscriptionRevenue' => round((float) ($monthlyPayments->get($key, 0)), 2),
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

        $recentBookings = Join::query()
            ->with(['user:id,name,phone', 'training:id,name,academy_id', 'invoice:id,status,is_canceled'])
            ->whereHas('training', fn ($query) => $query->where('academy_id', $academyId))
            ->latest('joins.created_at')
            ->limit(6)
            ->get();

        $latestSettlement = Schema::hasTable('settlements')
            ? Settlement::where('partner_id', $academyId)->latest()->first()
            : null;

        $dashboard = [
            'academyName' => $this->localizedValue($academy->getRawOriginal('commercial_name')),
            'ownerName' => $academy->owner_name ?: $academy->first_name ?: $academy->name,
            'totalBookings' => $totalBookings,
            'totalRevenue' => $totalRevenue,
            'uniqueCustomers' => $uniqueCustomers,
            'bookingTrend' => $this->percentageChange($currentBookings, $previousBookings),
            'totalTrainings' => $totalTrainings,
            'activeTrainings' => $activeTrainings,
            'totalCoaches' => $totalCoaches,
            'followers' => $academy->follows()->count(),
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
        ];

        return view('Academy.index', compact('dashboard'));
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

    public function getBeginnerSportsCount()
    {
        $academyId = Auth::id(); // Assuming the authenticated user is an academy
        return Sport::select('sports.id', 'sports.name', 'user_sport.level', 'user_sport.user_id')
            ->join('academy_sport', 'sports.id', '=', 'academy_sport.sport_id')
            ->join('user_sport', 'sports.id', '=', 'user_sport.sport_id')
            ->join('trainings', 'trainings.sport_id', '=', 'sports.id')  // Assuming there is a sport_id in the training table
            ->join('joins', 'joins.training_id', '=', 'trainings.id')  // Assuming there is a sport_id in the training table
            ->where('user_sport.level', 'Beginner')
            ->where('academy_sport.academy_id', $academyId)
            ->where('trainings.academy_id', $academyId)
            ->whereColumn('joins.user_id', 'user_sport.user_id')  // Ensures the user_id matches
            ->count();
    }

    public function getIntermediateSportsCount()
    {
        $academyId = Auth::id(); // Assuming the authenticated user is an academy

        return Sport::select('sports.id', 'sports.name', 'user_sport.level', 'user_sport.user_id')
            ->join('academy_sport', 'sports.id', '=', 'academy_sport.sport_id')
            ->join('user_sport', 'sports.id', '=', 'user_sport.sport_id')
            ->join('trainings', 'trainings.sport_id', '=', 'sports.id')  // Assuming there is a sport_id in the training table
            ->join('joins', 'joins.training_id', '=', 'trainings.id')  // Assuming there is a sport_id in the training table
            ->where('user_sport.level', 'Intermediate')
            ->where('academy_sport.academy_id', $academyId)
            ->where('trainings.academy_id', $academyId)
            ->whereColumn('joins.user_id', 'user_sport.user_id')  // Ensures the user_id matches
            ->count();
    }

    public function getAdvancedSportsCount()
    {
        $academyId = Auth::id(); // Assuming the authenticated user is an academy

        return Sport::select('sports.id', 'sports.name', 'user_sport.level', 'user_sport.user_id')
            ->join('academy_sport', 'sports.id', '=', 'academy_sport.sport_id')
            ->join('user_sport', 'sports.id', '=', 'user_sport.sport_id')
            ->join('trainings', 'trainings.sport_id', '=', 'sports.id')  // Assuming there is a sport_id in the training table
            ->join('joins', 'joins.training_id', '=', 'trainings.id')  // Assuming there is a sport_id in the training table
            ->where('user_sport.level', 'Advanced')
            ->where('academy_sport.academy_id', $academyId)
            ->where('trainings.academy_id', $academyId)
            ->whereColumn('joins.user_id', 'user_sport.user_id')  // Ensures the user_id matches
            ->count();
    }
    private function getAllMaleUsersCount()
    {
        return User::whereHas('joins.training', function ($query) {
            $query->where('academy_id', auth('academy')->id());
        })->select('id')->whereGender('male')->count();
    }

    private function getAllFemaleUsersCount()
    {
        return User::whereHas('joins.training', function ($query) {
            $query->where('academy_id', auth('academy')->id());
        })->select('id')->whereGender('female')->count();
    }

    /**
     * @return mixed
     */
    public function getUsersBooking()
    {
        return Join::whereHas('training', function ($query) {
            $query->where('academy_id', auth('academy')->id());
        })->get()->unique('user_id');
    }

    private function getUserBookingLast7Days()
    {
        return Join::whereHas('training', function ($query) {
            $query->where('academy_id', auth('academy')->id());
        })
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->get()
            ->unique('user_id');
    }

    public function getUnreadNotificationCount()
    {
        return response()->json([
            'unread_count' => auth('academy')->user()->unreadNotifications->count()
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

}
