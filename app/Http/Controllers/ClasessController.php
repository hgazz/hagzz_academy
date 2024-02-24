<?php

namespace App\Http\Controllers;

use App\DataTables\TClassDataTable;
use App\Http\Requests\Class\ClassRequest;
use App\Models\TClass;
use App\Services\TranslatableService;

class ClasessController extends Controller
{
    private $classModel;
    public function __construct(TClass $class)
    {
        $this->classModel = $class;
    }

    public function index(TClassDataTable $dataTable)
    {
        return $dataTable->render('Academy.pages.clasess.index');
    }

    public function create()
    {
        return view('Academy.pages.clasess.create');
    }

    public function store(ClassRequest $request)
    {
        $translatable = TranslatableService::generateTranslatableFields($this->classModel::getTranslatableFields() , $request->validated());
        $this->classModel->create(array_merge($translatable , [
            'date'=>$request->date,
        ]));
        session()->flash('success',trans('admin.clasess.created_successfully'));
        return redirect(route('academy.class.index'));
    }

    public function edit(TClass $class)
    {
        return view('Academy.pages.clasess.edit',compact('class'));
    }

    public function update(TClass $class , ClassRequest $request)
    {
        $translatable = TranslatableService::generateTranslatableFields($this->classModel::getTranslatableFields() , $request->validated());
        $class->update(array_merge($translatable ,[
            'date'=>$request->date,
        ]));
        session()->flash('success',trans('admin.clasess.updated_successfully'));
        return redirect(route('academy.class.index'));
    }

    public function delete(TClass $class)
    {
        $class->delete();
        session()->flash('success',trans('admin.clasess.deleted_successfully'));
        return redirect(route('academy.class.index'));
    }
}
