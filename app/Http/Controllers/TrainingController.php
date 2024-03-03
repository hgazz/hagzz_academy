<?php

namespace App\Http\Controllers;

use App\DataTables\TrainingDataTable;
use App\Http\Requests\Training\TrainingRequest;
use App\Http\Traits\CoacheTrait;
use App\Http\Traits\FileUpload;
use App\Models\Training;
use App\Services\TranslatableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TrainingController extends Controller
{
    use CoacheTrait ,FileUpload;
   private $trainingModel;
   public function __construct(Training $training)
   {
       $this->trainingModel = $training;
   }

   public function index(TrainingDataTable $dataTable)
   {
        return $dataTable->render('Academy.pages.training.index');
   }
   public function create()
   {
       $coaches = $this->getCoaches();
        return view('Academy.pages.training.create',compact('coaches'));
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
               'academy_id' => auth()->id()

           ]));
       });
       session()->flash('success',trans('admin.training.created_successfully'));
       return to_route('academy.training.index');
   }

    public function edit(Training $training)
    {
        $coaches = $this->getCoaches();
        return view('Academy.pages.training.edit',compact('coaches','training'));
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
                'price' => $request->price
             ]));
        });
        session()->flash('success',trans('admin.training.updated_successfully'));
        return to_route('academy.training.index');

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
