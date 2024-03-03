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
        $sports = auth()->user()->sports;
        $trainings = $this->trainingModel->whereBelongsTo(auth('academy')->user(), 'academy')->get();
        return view('Academy.pages.clasess.create', compact('sports', 'trainings'));
    }

    public function store(ClassRequest $request)
    {
        try {
            DB::beginTransaction();
            $translatable = TranslatableService::generateTranslatableFields($this->classModel::getTranslatableFields() , $request->validated());
            $class = $this->classModel->create(array_merge($translatable , [
                'date'=> $request->date,
                'academy_id' => auth()->id(),
                'sport_id' => $request->sport_id
            ]));
            $trainings = $request->training_id;
            $class->trainings()->attach($trainings);
            DB::commit();
            session()->flash('success',trans('admin.clasess.created_successfully'));
            return redirect(route('academy.class.index'));
        }catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
            return back();
        }

    }

    public function edit(TClass $class)
    {
        $sports = auth()->user()->sports;
        $trainings = $this->trainingModel->whereBelongsTo(auth('academy')->user(), 'academy')->get();
        return view('Academy.pages.clasess.edit',compact('class', 'sports', 'trainings'));
    }

    public function update(TClass $class , ClassRequest $request)
    {
        try {
            DB::beginTransaction();
            $translatable = TranslatableService::generateTranslatableFields($this->classModel::getTranslatableFields() , $request->validated());
            $class->update(array_merge($translatable ,[
                'date' => $request->date,
                'academy_id' => auth()->id(),
                'sport_id' => $request->sport_id
            ]));
            $trainings = $request->training_id;
            $class->trainings()->sync($trainings);
            DB::commit();
            session()->flash('success',trans('admin.clasess.updated_successfully'));
            return redirect(route('academy.class.index'));
        }catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', $e->getMessage());
            return back();
        }

    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();
            $class = $this->classModel->findOrFail($request->id);
            $class->delete();
            $class->trainings()->detach($class->id);
            DB::commit();
            return response()->json(['data' => [
                'status' => 'success',
                'model'   => trans('admin.clasess.clasess'),
                'message' => trans('admin.clasess.deleted_successfully'),
            ]]);
        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['data' => [
                'status' => 'failed',
            ]]);
        }
    }
}
