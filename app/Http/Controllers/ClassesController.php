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
use App\Services\TranslatableService;
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
        $translatable =  TranslatableService::generateTranslatableFields(TClass::getTranslatableFields(),$request->validated());
        // Prepare bilingual fields for outcomes and bring_with_me
        $outcomes = [
            'en' => $request->input('outcomes.en', []),
            'ar' => $request->input('outcomes.ar', [])
        ];

        $bringWithMe = [
            'en' => $request->input('bring_with_me.en', []),
            'ar' => $request->input('bring_with_me.ar', [])
        ];

        // Create the class with merged data
        $this->classModel->create(array_merge($translatable, [
            'date' => $request->date,
            'training_id' => $request->training_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'out_comes' => $outcomes,
            'bring_with_me' => $bringWithMe
        ]));

        session()->flash('success',trans('admin.clasess.created_successfully'));
        return redirect(route('academy.class.index'))->withErrors("error");
    }

    public function edit(TClass $class)
    {
        $academyTrainings = $this->trainingModel->whereBelongsTo(auth('academy')->user(), 'academy')->get();
        return view('Academy.pages.clasess.edit',compact('class', 'academyTrainings'));
    }

    public function update(TClass $class , ClassRequest $request)
    {
        $translation = TranslatableService::generateTranslatableFields(TClass::getTranslatableFields() , $request->validated());
        try {
            DB::beginTransaction();
            // Prepare bilingual fields for outcomes and bring_with_me
            $outcomes = [
                'en' => $request->input('outcomes.en', []),
                'ar' => $request->input('outcomes.ar', [])
            ];

            $bringWithMe = [
                'en' => $request->input('bring_with_me.en', []),
                'ar' => $request->input('bring_with_me.ar', [])
            ];

            // Update the class with merged data
            $class->update(array_merge($translation, [
                'date' => $request->date,
                'training_id' => $request->training_id,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'out_comes' => $outcomes,
                'bring_with_me' => $bringWithMe,
            ]));
            $details = [
                'training_id' => $class->training_id,
                'longitude' => $class->training->longitude,
                'latitude' => $class->training->latitude,
                'academy_name' => auth('academy')->user()->commercial_name
            ];
            //notifications to users
            if ($class->wasChanged('date')) {
                $title = 'Session Rescheduled';
                $body = 'Your Next Session at' . auth('academy')->user()->commercial_name. ' is rescheduled, at ' . $class->date . ' please check the new timings.Apologies for the inconvenience';
                $joins = Join::where('training_id', $class->training_id)->get();
                $data = [
                    'title' => $title,
                    'body' => $body,
                    'image' => auth('academy')->user()->image,
                    'details' => $details,
                    "id" => $class->training_id,
                    'page' => 'details'
                ];
                $joins->map(function ($join) use ($data) {
                    NotificationService::firebaseNotification($data, $join->user->fcm_token);
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
