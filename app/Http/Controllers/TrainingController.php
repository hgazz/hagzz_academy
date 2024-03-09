<?php

namespace App\Http\Controllers;

use App\DataTables\TrainingDataTable;
use App\Http\Requests\Training\TrainingRequest;
use App\Http\Traits\CoacheTrait;
use App\Http\Traits\FileUpload;
use App\Models\Address;
use App\Models\Sport;
use App\Models\Training;
use App\Services\TranslatableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainingController extends Controller
{
    use CoacheTrait ,FileUpload;
   private $trainingModel, $addressModel;
   public function __construct(Training $training, Address $address)
   {
       $this->trainingModel = $training;
       $this->addressModel = $address;
   }

   public function index(TrainingDataTable $dataTable)
   {
        return $dataTable->render('Academy.pages.training.index');
   }
   public function create()
   {
       $sports = auth('academy')->user()->sports;
       $coaches = $this->getCoaches();
       $addresses = $this->addressModel::whereBelongsTo(auth('academy')->user(), 'academy')->get();
        return view('Academy.pages.training.create',compact('coaches', 'addresses', 'sports'));
   }
   public function store(TrainingRequest $request)
   {
       DB::transaction(function() use ($request){
           $imageName = $this->upload($request->file('image') , $this->trainingModel::PATH);
           $translatable = TranslatableService::generateTranslatableFields($this->trainingModel::getTranslatableFields() , $request->validated());
           $this->trainingModel->create(array_merge($translatable,[
               'image'=> $imageName,
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

           ]));
       });
       session()->flash('success',trans('admin.training.created_successfully'));
       return to_route('academy.training.index');
   }

    public function edit(Training $training)
    {
        $coaches = $this->getCoaches();
        $sports = auth('academy')->user()->sports;
        $addresses = $this->addressModel::whereBelongsTo(auth('academy')->user(), 'academy')->get();
        return view('Academy.pages.training.edit',compact('coaches', 'sports','training', 'addresses'));
    }

    public function update(Training $training , TrainingRequest $request)
    {
        DB::transaction(function () use ($request, $training) {
            $imageName = $request->hasFile('image') ? $this->upload($request->file('image') , $this->trainingModel::PATH,  $training->getRawOriginal('image')) : $training->getRawOriginal('image');
            $translatable = TranslatableService::generateTranslatableFields($this->trainingModel::getTranslatableFields(), $request->validated());
             $training->update(array_merge($translatable, [
                'image' => $imageName,
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
             ]));
        });
        session()->flash('success',trans('admin.training.updated_successfully'));
        return to_route('academy.training.index');

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

        session()->flash('success', $successMessage);
        return redirect()->route('academy.training.index');
    }
    public function delete(Request $request)
    {
       $training = $this->trainingModel->findOrFail($request->id);
       $training->delete();
       $this->deleteFile($this->trainingModel::PATH . $training->getRawOriginal('image'));
       return response()->json(['data' => [
            'status' => 'success',
            'model'   => trans('admin.training.training'),
            'message' => trans('admin.training.deleted_successfully'),
       ]]);
    }
}
