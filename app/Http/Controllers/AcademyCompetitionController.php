<?php

namespace App\Http\Controllers;

use App\Models\Academies;
use App\Models\AcademyCompetition;
use App\Models\AcademyStudent;
use App\Models\Sport;
use Illuminate\Http\Request;

class AcademyCompetitionController extends Controller
{
    public function index()
    {
        $competitions = AcademyCompetition::with(['sport'])
            ->withCount('students')
            ->where('academy_id', auth('academy')->id())
            ->latest('competition_date')
            ->paginate(20);

        return view('Academy.pages.competitions.index', compact('competitions'));
    }

    public function create()
    {
        return view('Academy.pages.competitions.create', $this->formData());
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);
        $data['academy_id'] = auth('academy')->id();

        $competition = AcademyCompetition::create($data);
        $competition->students()->sync($this->studentSyncPayload($request));

        session()->flash('success', trans('admin.student_management.competition_created'));
        return to_route('academy.competitions.show', $competition);
    }

    public function show(AcademyCompetition $competition)
    {
        $this->authorizeCompetition($competition);
        $competition->load(['sport', 'students']);

        return view('Academy.pages.competitions.show', compact('competition'));
    }

    public function edit(AcademyCompetition $competition)
    {
        $this->authorizeCompetition($competition);

        return view('Academy.pages.competitions.edit', array_merge($this->formData(), compact('competition')));
    }

    public function update(Request $request, AcademyCompetition $competition)
    {
        $this->authorizeCompetition($competition);
        $competition->update($this->validated($request));
        $competition->students()->sync($this->studentSyncPayload($request));

        session()->flash('success', trans('admin.student_management.competition_updated'));
        return to_route('academy.competitions.show', $competition);
    }

    public function destroy(AcademyCompetition $competition)
    {
        $this->authorizeCompetition($competition);
        $competition->delete();

        session()->flash('success', trans('admin.student_management.competition_deleted'));
        return to_route('academy.competitions.index');
    }

    public function result(Request $request, AcademyCompetition $competition)
    {
        $this->authorizeCompetition($competition);

        $data = $request->validate([
            'home_score' => ['required', 'integer', 'min:0', 'max:999'],
            'opponent_score' => ['required', 'integer', 'min:0', 'max:999'],
            'result_notes' => ['nullable', 'string'],
        ]);

        $competition->update(array_merge($data, ['status' => 'completed']));

        session()->flash('success', trans('admin.student_management.competition_result_updated'));
        return back();
    }

    public function print(AcademyCompetition $competition)
    {
        $this->authorizeCompetition($competition);
        $competition->load(['academy', 'sport', 'students']);

        return view('Academy.pages.competitions.print', compact('competition'));
    }

    private function formData(): array
    {
        $academyId = auth('academy')->id();
        $academy = Academies::find($academyId);

        return [
            'competitionAcademy' => $academy,
            'competitionSports' => Sport::whereHas('academies', fn($query) => $query->where('academy_id', $academyId))
                ->orderBy('name')
                ->get(),
            'competitionStudents' => AcademyStudent::where('academy_id', $academyId)
                ->where('status', 'active')
                ->orderBy('name')
                ->get(),
            'defaultTeamName' => $this->localizedValue($academy?->getRawOriginal('commercial_name')) ?: ($academy?->name ?: 'Hagzz Academy'),
        ];
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'home_team_name' => ['required', 'string', 'max:255'],
            'opponent_name' => ['required', 'string', 'max:255'],
            'sport_id' => ['nullable', 'integer', 'exists:sports,id'],
            'competition_date' => ['required', 'date'],
            'starts_at' => ['nullable', 'date_format:H:i'],
            'venue' => ['nullable', 'string', 'max:255'],
            'status' => ['required', 'in:scheduled,completed,cancelled'],
            'home_score' => ['nullable', 'integer', 'min:0', 'max:999'],
            'opponent_score' => ['nullable', 'integer', 'min:0', 'max:999'],
            'result_notes' => ['nullable', 'string'],
            'notes' => ['nullable', 'string'],
            'student_ids' => ['nullable', 'array'],
            'student_ids.*' => ['integer', 'exists:academy_students,id'],
        ]);

        unset($data['student_ids']);
        $this->authorizeOwnedRelations($data);

        if ($data['status'] !== 'completed') {
            $data['home_score'] = null;
            $data['opponent_score'] = null;
            $data['result_notes'] = null;
        }

        return $data;
    }

    private function studentSyncPayload(Request $request): array
    {
        return AcademyStudent::where('academy_id', auth('academy')->id())
            ->whereIn('id', $request->input('student_ids', []))
            ->pluck('id')
            ->mapWithKeys(fn($studentId) => [$studentId => []])
            ->all();
    }

    private function authorizeOwnedRelations(array $data): void
    {
        if (! empty($data['sport_id'])) {
            abort_unless(
                Sport::whereKey($data['sport_id'])
                    ->whereHas('academies', fn($query) => $query->where('academy_id', auth('academy')->id()))
                    ->exists(),
                404
            );
        }
    }

    private function authorizeCompetition(AcademyCompetition $competition): void
    {
        abort_unless($competition->academy_id === auth('academy')->id(), 404);
    }

    private function localizedValue(mixed $value): string
    {
        if (is_array($value)) {
            return $value[app()->getLocale()] ?? $value['en'] ?? reset($value) ?: '';
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);

            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                return $decoded[app()->getLocale()] ?? $decoded['en'] ?? reset($decoded) ?: '';
            }
        }

        return (string) ($value ?? '');
    }
}
