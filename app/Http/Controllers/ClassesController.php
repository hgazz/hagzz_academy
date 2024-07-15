<?php

namespace App\Http\Controllers;

use App\DataTables\TClassDataTable;
use App\Exports\clasessExport;
use App\Http\Requests\Class\ClassRequest;
use App\Models\Join;
use App\Models\Sport;
use App\Models\TClass;
use App\Models\Training;
use App\Models\User;
use App\Services\Firebase\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

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
        $academyTrainings = $this->trainingModel->whereBelongsTo(auth('academy')->user(), 'academy')->get();
        return view('Academy.pages.clasess.create', compact( 'academyTrainings'));
    }

    public function store(ClassRequest $request)
    {

        try {
             $this->classModel->create([
                 'title' => $request->title,
                 'date'=> $request->date,
                 'training_id' => $request->training_id,
                 'start_time' => $request->start_time,
                 'end_time' => $request->end_time,
                 'out_comes' => $request->input('outcomes'),
                 'bring_with_me' => $request->input('bring_with_me'),
            ]);
            session()->flash('success',trans('admin.clasess.created_successfully'));
            return redirect(route('academy.class.index'));
        }catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return back();
        }

    }

    public function edit(TClass $class)
    {
        $academyTrainings = $this->trainingModel->whereBelongsTo(auth('academy')->user(), 'academy')->get();
        return view('Academy.pages.clasess.edit',compact('class', 'academyTrainings'));
    }

    public function update(TClass $class , ClassRequest $request)
    {
        try {
            DB::beginTransaction();
            $class->update([
                'title' => $request->title,
                'date'=> $request->date,
                'training_id' => $request->training_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'out_comes' => $request->input('outcomes'),
                'bring_with_me' => $request->input('bring_with_me'),
            ]);
            $details = [
                'training_id' => $class->training_id,
                'longitude' => $class->training->longitude,
                'latitude' => $class->training->latitude,
                'academy_name' => auth('academy')->user()->commercial_name
            ];
            //notifications to users
            if ($class->wasChanged('date')) {
                $title = 'Session Rescheduled';
                $body = 'The next session at ' . auth('academy')->user()->commercial_name. ' is rescheduled, please check the new dates';
                $joins = Join::where('training_id', $class->training_id)->get();
                $joins->map(function ($join) use ($title, $body, $details) {
                    NotificationService::dbNotification($join->user_id,User::class, 1, $title, $body, auth('academy')->user()->image, $details);
                });
            }

            DB::commit();
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
             $class = $this->classModel->findOrFail($request->id);
             $class->delete();
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

    public function bulkDelete(Request $request)
    {
        foreach (json_decode($request->ids) as $id) {
            $class = $this->classModel->findOrFail($id);
            $class->delete();
        }
        session()->flash('success', trans('admin.clasess.deleted_successfully'));
        return to_route('academy.class.index');
    }
    public function export()
    {
        return Excel::download(new clasessExport() , 'classes.xlsx');
    }

    public function checkTrainingDate(Request $request)
    {
        $training = $this->trainingModel->findOrFail($request->training_id);
        return response()->json([
            'status' => 'success',
            'data' =>$training
        ]);
    }
}
