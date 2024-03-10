<?php

namespace App\Http\Controllers;

use App\DataTables\TClassDataTable;
use App\Http\Requests\Class\ClassRequest;
use App\Models\Sport;
use App\Models\TClass;
use App\Models\Training;
use App\Services\TranslatableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClassesController extends Controller
{
    private $classModel, $sportModel, $trainingModel;
    public function __construct(TClass $class, Sport $sport, Training $training)
    {
        $this->classModel = $class;
        $this->sportModel = $sport;
        $this->trainingModel = $training;
    }

    public function index(TClassDataTable $dataTable)
    {
        return $dataTable->render('Academy.pages.clasess.index');
    }

    public function create()
    {
        $sports = $this->sportModel->get();
        $trainings = $this->trainingModel->whereBelongsTo(auth('academy')->user(), 'academy')->get();
        return view('Academy.pages.clasess.create', compact('sports', 'trainings'));
    }

    public function store(ClassRequest $request)
    {

        try {
            $translatable = TranslatableService::generateTranslatableFields($this->classModel::getTranslatableFields() , $request->validated());
            $outcomesJson = json_encode($request->input('outcomes'));
            $bringWithMeJson = json_encode($request->input('bring_with_me'));
             $this->classModel->create(array_merge($translatable , [
                'date'=> $request->date,
                'training_id' => $request->training_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'out_comes' => $outcomesJson,
                'bring_with_me' => $bringWithMeJson,
            ]));
            session()->flash('success',trans('admin.clasess.created_successfully'));
            return redirect(route('academy.class.index'));
        }catch (\Exception $e) {
//            session()->flash('error', $e->getMessage());
//            return back();
            return $e->getMessage();
        }

    }

    public function edit(TClass $class)
    {
        $sports = $this->sportModel->get();
        $trainings = $this->trainingModel->whereBelongsTo(auth('academy')->user(), 'academy')->get();
        return view('Academy.pages.clasess.edit',compact('class', 'sports', 'trainings'));
    }

    public function update(TClass $class , ClassRequest $request)
    {
        try {
            $translatable = TranslatableService::generateTranslatableFields($this->classModel::getTranslatableFields() , $request->validated());
            $outcomesJson = json_encode($request->input('outcomes'));
            $bringWithMeJson = json_encode($request->input('bring_with_me'));
            $class->update(array_merge($translatable ,[
                'date'=> $request->date,
                'training_id' => $request->training_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'out_comes' => $outcomesJson,
                'bring_with_me' => $bringWithMeJson,
            ]));

            session()->flash('success',trans('admin.clasess.updated_successfully'));
            return redirect(route('academy.class.index'));
        }catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back();

        }

    }

    public function delete(Request $request)
    {
        try {
             $this->classModel->findOrFail($request->id);
            return response()->json(['data' => [
                'status' => 'success',
                'model'   => trans('admin.clasess.clasess'),
                'message' => trans('admin.clasess.deleted_successfully'),
            ]]);
        }catch (\Exception $e) {
            return response()->json(['data' => [
                'status' => 'failed',
            ]]);
        }
    }
}
