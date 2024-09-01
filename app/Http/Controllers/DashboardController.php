<?php

namespace App\Http\Controllers;

use App\Http\Traits\BookingFilterTrait;
use App\Http\Traits\TrainingsTrait;
use App\Http\Traits\UsersTrait;
use App\Models\Join;
use App\Models\Settlement;
use App\Models\Sport;
use App\Models\User;
use App\Services\Chart\ChartsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

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
        $totalUsers = $this->getUsersByPartner();
        $maleUsers = $this->getAllMaleUsersCount();
        $femaleUsers = $this->getAllFemaleUsersCount();
        $usersBooking = $this->getUsersBooking();
        $newCustomers = $this->getUserBookingLast7Days();
        $beginnerLevels = $this->getBeginnerSportsCount();
        $intermediateLevels = $this->getIntermediateSportsCount();
        $advancedLevels = $this->getAdvancedSportsCount();
        $settlements = Settlement::whereBelongsTo( auth('academy')->user(), 'partner')->latest()->first();
        $fullTrainings = $this->getFullTrainings();
        $cancelledTrainings = $this->getCancelledTrainings();
        $upcomingTrainings = $this->getUpcomingTrainings();
        $inProgressTrainings = $this->getInProgressTrainings();
        return view('Academy.index', get_defined_vars());
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

}
