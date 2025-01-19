<?php

namespace App\Http\Controllers;

use App\DataTables\TrainingDataTable;
use App\Exports\TrainingsExport;
use App\Http\Requests\BookingRequest;
use App\Http\Requests\Training\TrainingRequest;
use App\Http\Traits\CoacheTrait;
use App\Models\Academies;
use App\Models\Address;
use App\Models\Area;
use App\Models\City;
use App\Models\Coach;
use App\Models\CoachSport;
use App\Models\Country;
use App\Models\Follow;
use App\Models\Invoice;
use App\Models\Join;
use App\Models\Training;
use App\Models\User;
use App\Services\Firebase\NotificationService;
use App\Services\TranslatableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainingController extends Controller
{
    use CoacheTrait;
   private $trainingModel, $addressModel, $coachModel;
   public function __construct(Training $training, Address $address, Coach $coach)
   {
       $this->trainingModel = $training;
       $this->addressModel = $address;
       $this->coachModel = $coach;
   }

   public function index(TrainingDataTable $dataTable)
   {
        return $dataTable->render('Academy.pages.training.index');
   }
   public function create()
   {
       $sports = auth('academy')->user()->sports;
       $academyCoaches = $this->coachModel::where('academy_id', auth('academy')->id())->where('active', 1)->get();
       $addresses = $this->addressModel::whereBelongsTo(auth('academy')->user(), 'academy')->get();
        return view('Academy.pages.training.create',compact('academyCoaches', 'addresses', 'sports'));
   }

    public function getCoachesBySports($id)
    {
        $coaches = CoachSport::where('sport_id', $id)
            ->whereHas('coach', function ($query)  {
                // Filter coaches by the academy of the authenticated user
                $query->select('id', 'name')
                ->where('academy_id', auth('academy')->id());
            })
            ->with(['coach' => function ($query) {
                $query->select('id', 'name'); // Limit fields to avoid unnecessary data
            }])
            ->get()
            ->pluck('coach')
            ->unique(); // Remove duplicate coaches (if any)

        // Return a structured JSON response
        return response()->json([
            'coaches' => $coaches
        ]);
    }
   public function store(TrainingRequest $request)
   {
       \DB::transaction(function() use ($request){
           $translatable = TranslatableService::generateTranslatableFields($this->trainingModel::getTranslatableFields() , $request->validated());
           $this->trainingModel->create(array_merge($translatable,[
               'start_date'=> $request->start_date,
               'end_date'=> $request->end_date,
               'start_time' => $request->start_time,
               'end_time' => $request->end_time,
               'coach_id'=> $request->coach_id,
               'price'=> $request->price,
               'max_players'=> $request->max_players,
               'level'=> $request->level,
               'gender' => $request->gender,
               'age_group' => $request->age_group,
               'address_id' => $request->address_id,
               'academy_id' => auth()->id(),
               'sport_id' => $request->sport_id,
               'discount_price' => $request->discount_price,
               'classes_days' => $request->classes_days,
               'color' => $request->color,
              'classes_number' => $request->classes_number
           ]));
//           NotificationService::dbNotification(auth('academy')->id(),Academies::class,1,'New Training','New Training has been added to your academy',auth('academy')->user()->image,['training_id'=>$training->id]);
       });
       session()->flash('success',trans('admin.training.created_successfully'));
       return to_route('academy.training.index');
   }

    public function edit(Training $training)
    {
        $academyCoaches = $this->coachModel::where('academy_id', auth('academy')->id())->where('active', 1)->get(['id','name']);
        $sports = auth('academy')->user()->sports;
        $addresses = $this->addressModel::whereBelongsTo(auth('academy')->user(), 'academy')->get();
        return view('Academy.pages.training.edit',compact('academyCoaches', 'sports','training', 'addresses'));
    }

    public function update(Training $training , TrainingRequest $request)
    {
        try {
            DB::transaction(function () use ($request, $training) {
                $originalStartDate = $training->start_date;
                $translatable = TranslatableService::generateTranslatableFields($this->trainingModel::getTranslatableFields(), $request->validated());
                $training->update(array_merge($translatable, [
                    'start_date' => $request->start_date,
                    'end_date' => $request->end_date,
                    'start_time' => $request->start_time,
                    'end_time' => $request->end_time,
                    'coach_id' => $request->coach_id,
                    'price' => $request->price,
                    'max_players'=> $request->max_players,
                    'level'=> $request->level,
                    'gender' => $request->gender,
                    'age_group' => $request->age_group,
                    'address_id' => $request->address_id,
                    'sport_id' => $request->sport_id,
                    'discount_price' => $request->discount_price,
                    'classes_days' => $request->classes_days,
               'color' => $request->color,
              'classes_number' => $request->classes_number
                ]));
                $details = [
                    'training_id' => $training->id,
                    'longitude' => $training->longitude,
                    'latitude' => $training->latitude,
                    'academy_name' => auth('academy')->user()->commercial_name
                ];
                //notifications to users
                if ($originalStartDate != $training->start_date) {
                    $title = 'Booking Rescheduled';
                    $body = 'Your booking with ' . $training->academy->getTranslation('commercial_name', 'en') . ' is rescheduled.please check the new dates';
                    $joins = Join::where('training_id', $training->id)->get();
                    $data = [
                        'title' => $title,
                        'body' => $body,
                        'image' => auth('academy')->user()->image,
                        'details' => $details,
                        "id" => $training->id,
                        'page' => 'details',
                        'class_id' => null
                    ];
                    $joins->map(function ($join) use ($data) {
                        NotificationService::firebaseNotification($data,$join->user->fcm_token,);
                    });

                }
            });
            session()->flash('success',trans('admin.training.updated_successfully'));
            return to_route('academy.training.index');
        }catch (\Exception $e){
            return  response($e->getMessage());
        }



    }

    public function updateActive(Training $training)
    {
        if ($training->active){
            $newStatus = 0;
            $successMessage = trans('admin.training.status_inactive_successfully');
        } else {
            $newStatus = 1;
            $successMessage = trans('admin.training.status_active_successfully');
        }

        $training->update([
            'active' => $newStatus,
        ]);

        $this->sendNotification($training);
        session()->flash('success', $successMessage);
        return redirect()->route('academy.training.index');
    }
    public function delete(Request $request)
    {
       $training = $this->trainingModel->findOrFail($request->id);
       $training->delete();
       return response()->json(['data' => [
            'status' => 'success',
            'model'   => trans('admin.training.training'),
            'message' => trans('admin.training.deleted_successfully'),
       ]]);
    }

    public function createBooking()
    {
        $countries = Country::get(['id','name']);
        $data = $this->trainingModel::where('academy_id', auth('academy')->id())->get();
        return view('Academy.pages.training.create_booking', compact('countries', 'data'));
    }
    public function getAreaByCity(Request $request)
    {
        $areas = Area::where('city_id', $request->city_id)->get();
        return response()->json($areas);
    }

    public function getCityByCountry(Request $request)
    {
        $cities = City::where('country_id', $request->country_id)->get();
        return response()->json($cities);
    }
    public function storeBooking(BookingRequest $request)
    {
        try {
            $training = $this->trainingModel->findOrFail($request->training_id);
            DB::beginTransaction();
            $user = User::updateOrCreate(
                // Attributes to search for an existing user
                ['phone' => $request->phone],
                // Data to update or create
                [
                    'name' => $request->name,
                    'gender' => $request->gender,
                    'country_code' => $request->country_code,
                    'country_id' => $request->country_id,
                    'city_id' => $request->city_id,
                    'area_id' => $request->area_id,
                    'user_type'=> 'system',
                    'birth_date'=> $request->birth_date,
                    'email' => $request->email,
                    'child_type' => $request->child_type,
                    'school_name' => $request->school_name,
                    'parent_name' => $request->parent_name,
                    'parent_phone' => $request->parent_phone,
                    'coach_preference' =>  $request->coach_preference,
                    'frequent_attendance' => $request->frequent_attendance,
                    'relation_with_child' => $request->relation_with_child,
                    'referral_source' => $request->referral_source,
                    'medical_condition' => $request->medical_condition,
                    'medical_condition_details' => $request->medical_condition == 'yes' ? $request->medical_condition_details : null,
                    'additional_information' => $request->has('additional_information') ? $request->additional_information : null
                ]
            );
            $booking = Invoice::create([
                'user_id' => $user->id,
                'training_id' => $request->training_id,
                'amount' => $request->price,
                'order_number' => uniqid(),
                'status' => 'paid',
                'user_type' => 'offline'
            ]);
            Join::create([
                'user_id' => $user->id,
                'training_id' => $request->training_id,
                'price' => $booking->amount,
                'invoice_id' => $booking->id,
            ]);
            DB::commit();
            session()->flash('success', __('admin.training.Booking created successfully'));
            return back();
        }catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function export()
    {
        return Excel::download(new TrainingsExport() ,'training.xlsx');
    }

    /**
     * @param Training $training
     * @return void
     */
    public function sendNotification(Training $training): void
    {
        if ($training->active) {
            $details = [
                'training_id' => $training->id,
                'longitude' => $training->longitude,
                'latitude' => $training->latitude,
                'academy_name' => auth('academy')->user()->commercial_name
            ];
            $AcademyTitle = 'Don’t miss out!';
            $AcademyBody = 'just added a new activity. Check it out!';
            $academyFollows = Follow::where([
                'followable_type' => Academies::class,
                'followable_id' => auth('academy')->id(),
            ])->get();
            $academyFollows->map(function ($follow) use ($AcademyTitle, $AcademyBody, $details) {
                NotificationService::dbNotification($follow->user_id, User::class, 1, $AcademyTitle, $AcademyBody, auth('academy')->user()->image, $details);
            });

            $coachTitle = 'Don’t miss out!';
            $coachBody = $training->coach->name . ' is leading a new training.Tap for details';
            $coachFollows = Follow::where([
                'followable_type' => Coach::class,
                'followable_id' => $training->coach_id,
            ])->get();
            $coachFollows->map(function ($follow) use ($coachTitle, $coachBody, $details) {
                NotificationService::dbNotification($follow->user_id, User::class, 1, $coachTitle, $coachBody, auth('academy')->user()->image, $details);
            });
        }
    }

    public function bulkDelete(Request $request)
    {
        $trainingIds = json_decode($request->ids);

        foreach ($trainingIds as $trainingId) {
            $training = $this->trainingModel->findOrFail($trainingId);
            if ($training->joins()->count() > 0) {
                continue;
            }
            $training->delete();
        }
        session()->flash('success', 'Training Deleted Successfully');
        return back();
    }

    public function publish(Request $request)
    {
        $trainingIds = json_decode($request->pub_ids);
        foreach ($trainingIds as $trainingId) {
            $training = $this->trainingModel->findOrFail($trainingId);
            $status = ($training->active)  ? 0 : 1;
            $training->update(['active'=>$status]);
        }
        session()->flash('success', trans('admin.training.status_active_successfully'));
        return back();
    }
}
