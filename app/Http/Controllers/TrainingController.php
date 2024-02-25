<?php

namespace App\Http\Controllers;

use App\DataTables\TrainingDataTable;
use App\Http\Requests\Training\TrainigRequest;
use App\Http\Traits\CoacheTrait;
use App\Http\Traits\FileUpload;
use App\Models\Training;
use App\Services\TranslatableService;
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
       $classes = $this->getClass();
        return view('Academy.pages.training.create',compact('coaches','classes'));
   }
   public function store(TrainigRequest $request)
   {
       DB::transaction(function() use ($request){
           $imageName = $this->upload($request->file('image') , $this->trainingModel::PATH);
           $translatable = TranslatableService::generateTranslatableFields($this->trainingModel::getTranslatableFields() , $request->validated());
         $training =  $this->trainingModel->create(array_merge($translatable,[

               'image'=>$imageName,
               'start_date'=>$request->start_date,
               'end_date'=>$request->end_date,
               'coach_id'=>$request->coach_id

           ]));

           $classes = $request->class_id;
           $training->classes()->attach($classes);
       });
       session()->flash('success',trans('admin.training.created_successfully'));
       return to_route('academy.training.index');
   }

    public function edit(Training $training)
    {
        $coaches = $this->getCoaches();
        $classes = $this->getClass();
        return view('Academy.pages.training.edit',compact('coaches','classes','training'));
    }

    public function update(Training $training , TrainigRequest $request)
    {
        DB::transaction(function () use ($request, $training) {
            $imageName = $request->hasFile('image') ? $this->upload($request->file('image') , $this->trainingModel::PATH,  $training->getRawOriginal('image')) : $training->getRawOriginal('image');
            $translatable = TranslatableService::generateTranslatableFields($this->trainingModel::getTranslatableFields(), $request->validated());
             $training->update(array_merge($translatable, [
                'image' => $imageName,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'coach_id' => $request->coach_id

            ]));
            $classes = $request->class_id;
            $training->classes()->sync($classes);
        });
        session()->flash('success',trans('admin.training.updated_successfully'));
        return to_route('academy.training.index');

    }

    public function delete(Training $training)
    {
        DB::transaction(function () use ($training){
           $training->delete();
            $this->deleteFile($this->trainingModel::PATH . $training->getRawOriginal('image'));
            $training->classes()->detach($training->id);
        });
        session()->flash('success',trans('admin.training.deleted_successfully'));
        return to_route('academy.training.index');
    }
}
