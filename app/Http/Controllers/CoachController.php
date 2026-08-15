<?php

namespace App\Http\Controllers;

use App\DataTables\CoachDataTable;
use App\Exports\CoachExport;
use App\Http\Requests\Coach\CoachRequest;
use App\Http\Traits\FileUpload;
use App\Models\Coach;
use App\Models\PartnerUser;
use App\Models\Sport;
use App\Services\TranslatableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

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

    private function getAcademyId(): int
    {
        $user = auth('academy')->user();
        if ($user instanceof PartnerUser) {
            return (int) $user->academy_id;
        }
        return (int) ($user?->id ?? auth('academy')->id());
    }

    public function index(CoachDataTable $dataTable)
    {
        return $dataTable->render('Academy.pages.coaches.index');
    }

    public function filter(Request $request, CoachDataTable $dataTable)
    {
        /** @var \App\Models\PartnerUser $authUser */
        $authUser = auth('academy')->user();
        $service = new \App\Services\PartnerAccessService($authUser);
        $query = $service->scopeCoaches(Coach::query());

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereDate('coaches.created_at', '>=', $request->input('start_date'))
                ->whereDate('coaches.created_at', '<=', $request->input('end_date'));
        }

        return $dataTable->with('query', $query)->render('Academy.pages.coaches.index');
    }

    public function create()
    {
        /** @var \App\Models\PartnerUser $authUser */
        $authUser = auth('academy')->user();
        $sports = $authUser->getAccessibleSports();

        return view('Academy.pages.coaches.create', compact('sports'));
    }

    public function store(CoachRequest $request)
    {
        $academyId = $this->getAcademyId();
        $requestId = (string) \Illuminate\Support\Str::uuid();

        Log::info('Coach creation started', [
            'request_id' => $requestId,
            'academy_id' => $academyId,
            'sports' => $request->input('sport_id', []),
            'has_image' => $request->hasFile('image'),
        ]);

        try {
            DB::beginTransaction();

            $translatable = TranslatableService::generateTranslatableFields($this->coachModel::getTranslatableFields(), $request->validated());
            $imageName = $this->upload($request->file('image'), $this->coachModel::PATH);

            $coach = $this->coachModel->create(array_merge($translatable, [
                'image' => $imageName,
                'phone' => $request->phone,
                'active' => $request->has('active') ? 1 : 0,
                'academy_id' => $academyId,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'compensation_type' => $request->input('compensation_type', 'session'),
                'compensation_value' => $request->input('compensation_value', 0.00),
            ]));
            $coach->sports()->attach($request->sport_id);
            DB::commit();

            Log::info('Coach creation completed', [
                'request_id' => $requestId,
                'academy_id' => $academyId,
                'coach_id' => $coach->id,
            ]);

            session()->flash('success', trans('admin.coaches.created_successfully'));
            return to_route('academy.coach');
        } catch (Throwable $exception) {
            DB::rollBack();
            Log::error('Coach creation failed', [
                'request_id' => $requestId,
                'academy_id' => $academyId,
                'message' => $exception->getMessage(),
                'exception' => get_class($exception),
            ]);
            session()->flash('error', $exception->getMessage());
            return back()->withInput($request->all());
        }
    }

    public function edit(Coach $coach)
    {
        $academyId = $this->getAcademyId();
        $sports = $this->sportModel::whereHas('academies', function ($q) use ($academyId) {
            $q->where('academy_id', $academyId);
        })->get(['id', 'name']);

        return view('Academy.pages.coaches.edit', compact('coach', 'sports'));
    }

    public function update(Coach $coach, CoachRequest $request)
    {
        $academyId = $this->getAcademyId();

        try {
            DB::beginTransaction();
            $imageName = $request->hasFile('image') ? $this->upload($request->file('image'), $this->coachModel::PATH, $coach->getRawOriginal('image')) : $coach->getRawOriginal('image');
            $translatable = TranslatableService::generateTranslatableFields($this->coachModel::getTranslatableFields(), $request->validated());

            $coach->update(array_merge($translatable, [
                'image' => $imageName,
                'phone' => $request->phone,
                'active' => $request->has('active') ? 1 : 0,
                'academy_id' => $coach->academy_id ?: $academyId,
                'gender' => $request->gender,
                'birth_date' => $request->birth_date,
                'compensation_type' => $request->input('compensation_type', 'session'),
                'compensation_value' => $request->input('compensation_value', 0.00),
            ]));
            $coach->sports()->sync($request->sport_id);
            DB::commit();

            session()->flash('success', trans('admin.coaches.updated_successfully'));
            return to_route('academy.coach');
        } catch (Throwable $exception) {
            DB::rollBack();
            Log::error('Coach update failed', [
                'coach_id' => $coach->id,
                'academy_id' => $academyId,
                'message' => $exception->getMessage(),
                'exception' => get_class($exception),
            ]);
            session()->flash('error', $exception->getMessage());
            return back()->withInput($request->all());
        }
    }

    public function delete(Request $request)
    {
        try {
            DB::beginTransaction();

            $coach = $this->coachModel->findOrFail($request->id);
            $coachTrainings = $coach->trainings()->exists();

            if ($coachTrainings) {
                session()->flash('error', trans('admin.coaches.error_delete'));
                return back();
            }

            $coach->sports()->detach();
            $coach->delete();
            DB::commit();

            session()->flash('success', trans('admin.coaches.deleted_successfully'));
            return back();
        } catch (Throwable $exception) {
            DB::rollBack();
            Log::error('Coach deletion failed', [
                'coach_id' => $request->id,
                'academy_id' => $this->getAcademyId(),
                'message' => $exception->getMessage(),
                'exception' => get_class($exception),
            ]);
            session()->flash('error', $exception->getMessage());
            return back();
        }
    }

    public function export()
    {
        /** @var \App\Models\PartnerUser $user */
        $user = auth('academy')->user();
        $service = new \App\Services\PartnerAccessService($user);
        $coaches = $service->scopeCoaches(Coach::with(['academy', 'sports']));

        return Excel::download(new CoachExport($coaches), 'coaches_' . date('Y-m-d') . '.xlsx');
    }
}
