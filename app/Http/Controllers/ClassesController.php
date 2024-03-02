<?php

namespace App\Http\Controllers;

use App\DataTables\TClassDataTable;
use App\Http\Requests\Class\ClassRequest;
use App\Models\Sport;
use App\Models\TClass;
use App\Services\TranslatableService;
use Illuminate\Http\Request;

class ClassesController extends Controller
{
    private $classModel, $sportModel;
    public function __construct(TClass $class, Sport $sport)
    {
        $this->classModel = $class;
        $this->sportModel = $sport;
    }

    public function index(TClassDataTable $dataTable)
    {
        return $dataTable->render('Academy.pages.clasess.index');
    }

    public function create()
    {
        $sports = auth()->user()->sports;
        return view('Academy.pages.clasess.create', compact('sports'));
    }

    public function store(ClassRequest $request)
    {
        $translatable = TranslatableService::generateTranslatableFields($this->classModel::getTranslatableFields() , $request->validated());
        $this->classModel->create(array_merge($translatable , [
            'date'=>$request->date,
            'academy_id' => auth()->id(),
            'sport_id' => $request->sport_id
        ]));
        session()->flash('success',trans('admin.clasess.created_successfully'));
        return redirect(route('academy.class.index'));
    }

    public function edit(TClass $class)
    {
        $sports = auth()->user()->sports;
        return view('Academy.pages.clasess.edit',compact('class', 'sports'));
    }

    public function update(TClass $class , ClassRequest $request)
    {
        $translatable = TranslatableService::generateTranslatableFields($this->classModel::getTranslatableFields() , $request->validated());
        $class->update(array_merge($translatable ,[
            'date' => $request->date,
            'academy_id' => auth()->id(),
            'sport_id' => $request->sport_id
        ]));
        session()->flash('success',trans('admin.clasess.updated_successfully'));
        return redirect(route('academy.class.index'));
    }

    public function delete(Request $request)
    {
        $class = $this->classModel->findOrFail($request->id);
        $class->delete();
        return response()->json(['data' => [
            'status' => 'success',
            'model'   => trans('admin.clasess.clasess'),
            'message' => trans('admin.clasess.deleted_successfully'),
        ]]);
    }
}
