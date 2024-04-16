<?php

namespace App\Http\Controllers;

use App\DataTables\CoachDataTable;
use App\Exports\Coaches;
use App\Http\Requests\Coach\CoachRequest;
use App\Http\Traits\FileUpload;
use App\Models\Coach;
use App\Models\Sport;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class CoachController extends Controller
{
    use FileUpload;
    private Coach $coachModel;
    private Sport $sportModel;
    public function __construct(Coach $coachModel, Sport $sport)
    {
        $this->coachModel = $coachModel;
        $this->sportModel = $sport;
    }
    public function index(CoachDataTable $dataTable)
    {
        return $dataTable->render('Academy.pages.coaches.index');
    }

    public function create()
    {
        $sports = $this->sportModel::get(['id', 'name']);
        return view('Academy.pages.coaches.create', get_defined_vars());
    }

    public function store(CoachRequest $request)
    {
        try {
            DB::beginTransaction();
            $imageName =  $this->upload($request->file('image') , $this->coachModel::PATH);
            $coach = $this->coachModel->create(array_merge($request->validated() , [
                'image'=>$imageName,
                'phone'=> $request->phone,
                'active'=> $request->has('active') ? 1 : 0,
                'academy_id'=> auth()->id(),
                'license' => $request->license,
                'license_type' => $request->license_type
            ]));
            $coach->sports()->attach($request->sport_id);
            DB::commit();
            session()->flash('success',trans('admin.coaches.created_successfully'));
            return to_route('academy.coach');
        }catch (Exception $exception) {
            DB::rollBack();
            session()->flash('error', $exception->getMessage());
            return back()->withInput($request->all());
        }

    }

    public function edit(Coach $coach)
    {
        $sports = $this->sportModel::get(['id', 'name']);
        return view('Academy.pages.coaches.edit', compact('coach', 'sports'));
    }

    public function update(Coach $coach , CoachRequest $request)
    {
        try {
            DB::beginTransaction();
            $imageName = $request->hasFile('image') ? $this->upload($request->file('image') , $this->coachModel::PATH,  $coach->getRawOriginal('image')) : $coach->getRawOriginal('image');
            $coach->update(array_merge($request->validated(),[
                'image'=> $imageName,
                'phone'=> $request->phone,
                'active'=> $request->has('active') ? 1 : 0,
                'academy_id'=> auth()->id(),
                'license' => $request->license,
                'license_type' => $request->license_type
            ]));
            $coach->sports()->sync($request->sport_id);
            DB::commit();
            session()->flash('success',trans('admin.coaches.updated_successfully'));
            return to_route('academy.coach');
        }catch (Exception $exception){
            DB::rollBack();
            session()->flash('error', $exception->getMessage());
            return back()->withInput($request->all());
        }

    }

    public function delete(Request $request)
    {
        try {
            // Start Transaction
            DB::beginTransaction();

            $coach = $this->coachModel->findOrFail($request->id);
            $coachTrainings = $coach->trainings()->exists();

            if ($coachTrainings) {
                return response()->json(['data' => [
                    'status' => 'error',
                    'model'   => trans('admin.coaches.coaches'),
                    'message' => trans('admin.coaches.error_delete'),
                ]]);
            }

            // Attempt to delete the coach and detach related sports
            $coach->delete();
            $coach->sports()->detach();

            // Attempt to delete the coach's image file
            $this->deleteFile($this->coachModel::PATH . DIRECTORY_SEPARATOR . $coach->getRawOriginal('image'));

            // If everything is fine, commit the transaction
            DB::commit();

            return response()->json(['data' => [
                'status' => 'success',
                'model'   => trans('admin.coaches.coaches'),
                'message' => trans('admin.coaches.deleted_successfully'),
            ]]);
        } catch (\Exception $e) {
            // Rollback Transaction on any error
            DB::rollback();

            // Log the error or handle it as required
            return response()->json(['data' => [
                'status' => 'error',
                'model' => trans('admin.coaches.coaches'),
                'message' =>  $e->getMessage(),
            ]]);
        }
    }

    public function export()
    {
        return Excel::download(new Coaches() , 'coaches.xlsx');
    }
}
