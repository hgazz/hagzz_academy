<?php

namespace App\Http\Controllers;

use App\Models\AcademyGroup;
use App\Models\AcademyStudent;
use App\Models\Coach;
use App\Models\Sport;
use App\Models\Training;
use Illuminate\Http\Request;

class AcademyGroupController extends Controller
{
    public function index()
    {
        $groups = AcademyGroup::withCount('students')
            ->with(['coach', 'training', 'sport'])
            ->where('academy_id', auth('academy')->id())
            ->latest()
            ->paginate(20);

        return view('Academy.pages.groups.index', compact('groups'));
    }

    public function create()
    {
        return view('Academy.pages.groups.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['academy_id'] = auth('academy')->id();

        $group = AcademyGroup::create($data);
        $group->students()->sync($this->studentSyncPayload($request));

        session()->flash('success', 'Group created successfully');
        return to_route('academy.groups.index');
    }

    public function edit(AcademyGroup $group)
    {
        $this->authorizeGroup($group);

        return view('Academy.pages.groups.edit', array_merge($this->formData(), compact('group')));
    }

    public function update(Request $request, AcademyGroup $group)
    {
        $this->authorizeGroup($group);
        $group->update($this->validated($request));
        $group->students()->sync($this->studentSyncPayload($request));

        session()->flash('success', 'Group updated successfully');
        return to_route('academy.groups.index');
    }

    public function destroy(AcademyGroup $group)
    {
        $this->authorizeGroup($group);
        $group->delete();

        session()->flash('success', 'Group deleted successfully');
        return to_route('academy.groups.index');
    }

    private function formData(): array
    {
        $academyId = auth('academy')->id();

        return [
            'trainings' => Training::where('academy_id', $academyId)->orderBy('name')->get(),
            'coaches' => Coach::where('academy_id', $academyId)->orderBy('name')->get(),
            'sports' => Sport::whereHas('academies', fn($query) => $query->where('academy_id', $academyId))->orderBy('name')->get(),
            'students' => AcademyStudent::where('academy_id', $academyId)->where('status', 'active')->orderBy('name')->get(),
            'days' => ['saturday', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday'],
        ];
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'training_id' => ['nullable', 'integer', 'exists:trainings,id'],
            'coach_id' => ['nullable', 'integer', 'exists:coaches,id'],
            'sport_id' => ['nullable', 'integer', 'exists:sports,id'],
            'days' => ['nullable', 'array'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['integer', 'exists:academy_students,id'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_time' => ['nullable', 'date_format:H:i', 'after:start_time'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'status' => ['required', 'in:active,inactive'],
            'notes' => ['nullable', 'string'],
        ]);

        unset($data['student_ids']);

        $this->authorizeOwnedRelations($data);

        return $data;
    }

    private function studentSyncPayload(Request $request): array
    {
        $studentIds = AcademyStudent::where('academy_id', auth('academy')->id())
            ->whereIn('id', $request->input('student_ids', []))
            ->pluck('id');

        return $studentIds
            ->mapWithKeys(fn($studentId) => [
                $studentId => [
                    'joined_at' => now()->toDateString(),
                    'status' => 'active',
                ],
            ])
            ->all();
    }

    private function authorizeOwnedRelations(array $data): void
    {
        $academyId = auth('academy')->id();

        if (! empty($data['training_id'])) {
            abort_unless(Training::where('academy_id', $academyId)->whereKey($data['training_id'])->exists(), 404);
        }

        if (! empty($data['coach_id'])) {
            abort_unless(Coach::where('academy_id', $academyId)->whereKey($data['coach_id'])->exists(), 404);
        }

        if (! empty($data['sport_id'])) {
            abort_unless(
                Sport::whereKey($data['sport_id'])
                    ->whereHas('academies', fn($query) => $query->where('academy_id', $academyId))
                    ->exists(),
                404
            );
        }
    }

    private function authorizeGroup(AcademyGroup $group): void
    {
        abort_unless($group->academy_id === auth('academy')->id(), 404);
    }
}
