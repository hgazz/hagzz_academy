<?php

namespace App\Http\Controllers;

use App\DataTables\CoachDataTable;
use App\Http\Requests\Coach\CoachRequest;
use App\Http\Traits\FileUpload;
use App\Models\Coach;
use Illuminate\Http\Request;

class CoachController extends Controller
{
    use FileUpload;
    private $coachModel;
    public function __construct(Coach $coachModel)
    {
        $this->coachModel = $coachModel;
    }
    public function index(CoachDataTable $dataTable)
    {
        return $dataTable->render('Academy.pages.coaches.index');
    }

    public function create()
    {
        return view('Academy.pages.coaches.create');
    }

    public function store(CoachRequest $request)
    {

        $imageName =  $this->upload($request->file('image') , $this->coachModel::PATH);
        $this->coachModel->create(array_merge($request->validated() , [
            'image'=>$imageName,
            'active'=> $request->has('active') ? 1 : 0,
            'academy_id'=> auth()->id()
        ]));
        session()->flash('success',trans('admin.coaches.created_successfully'));
        return to_route('academy.coach');
    }

    public function edit(Coach $coach)
    {
        return view('Academy.pages.coaches.edit', compact('coach'));
    }

    public function update(Coach $coach , CoachRequest $request)
    {
        $imageName = $request->hasFile('image') ? $this->upload($request->file('image') , $this->coachModel::PATH,  $coach->getRawOriginal('image')) : $coach->getRawOriginal('image');
        $coach->update(array_merge($request->validated(),[
            'image'=> $imageName,
            'active'=> $request->has('active') ? 1 : 0,
            'academy_id'=> auth()->id()
        ]));
        session()->flash('success',trans('admin.coaches.updated_successfully'));
        return to_route('academy.coach');
    }

    public function delete(Coach $coach)
    {
        $coach->delete();
        $this->deleteFile($this->coachModel::PATH. DIRECTORY_SEPARATOR .$coach->getRawOriginal('image'));
        session()->flash('success', trans('admin.coaches.deleted_successfully'));
        return to_route('academy.coach');
    }
}
