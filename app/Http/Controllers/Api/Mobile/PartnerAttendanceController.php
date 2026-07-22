<?php

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Controllers\Controller;
use App\Models\Academies;
use App\Models\AcademyAttendanceSession;
use App\Models\AcademyStudent;
use App\Support\MembershipCode;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PartnerAttendanceController extends Controller
{
    public function sessions(Request $request): JsonResponse
    {
        $academyId = $this->academyId($request);
        $date = $request->validate(['date' => ['nullable', 'date']])['date'] ?? today()->format('Y-m-d');

        $sessions = AcademyAttendanceSession::query()
            ->with('group:id,name,academy_id')
            ->withCount(['records', 'records as present_count' => fn ($query) => $query->whereIn('status', ['present', 'late'])])
            ->whereDate('session_date', $date)
            ->whereHas('group', fn ($query) => $query->where('academy_id', $academyId))
            ->orderBy('starts_at')
            ->get()
            ->map(fn ($session) => [
                'id' => $session->id,
                'group' => $session->group?->name,
                'date' => $session->session_date?->format('Y-m-d'),
                'starts_at' => $session->starts_at,
                'ends_at' => $session->ends_at,
                'records_count' => $session->records_count,
                'present_count' => $session->present_count,
            ]);

        return response()->json(['data' => $sessions]);
    }

    public function scan(Request $request): JsonResponse
    {
        $academyId = $this->academyId($request);
        $data = $request->validate([
            'code' => ['required', 'string', 'max:120'],
            'session_id' => ['required', 'integer', 'exists:academy_attendance_sessions,id'],
            'mode' => ['nullable', 'in:auto,check_in,check_out'],
        ]);

        $studentId = MembershipCode::studentId($data['code'], $academyId);
        if (!$studentId) {
            return response()->json(['message' => 'This membership card is not valid for this academy.'], 422);
        }

        $student = AcademyStudent::where('academy_id', $academyId)
            ->with(['user', 'subscriptions.payments'])->findOrFail($studentId);
        $session = AcademyAttendanceSession::with('group')->findOrFail($data['session_id']);
        abort_unless($session->group?->academy_id === $academyId, 404);

        if (!$session->group->students()->whereKey($student->id)->exists()) {
            return response()->json(['message' => 'The student is not assigned to this group.'], 422);
        }

        $record = $session->records()->firstOrCreate(['academy_student_id' => $student->id]);
        $action = $data['mode'] ?? 'auto';
        if ($action === 'auto') {
            $action = blank($record->check_in_at) ? 'check_in' : (blank($record->check_out_at) ? 'check_out' : 'duplicate');
        }

        if ($action === 'duplicate') {
            return response()->json($this->response($student, $record, 'duplicate'));
        }

        if ($action === 'check_in') {
            if (filled($record->check_in_at)) {
                return response()->json($this->response($student, $record, 'duplicate'));
            }
            $late = $session->starts_at && now()->format('H:i:s') > date('H:i:s', strtotime($session->starts_at.' +15 minutes'));
            $record->update(['status' => $late ? 'late' : 'present', 'check_in_at' => now()->format('H:i:s')]);
        } else {
            if (blank($record->check_in_at)) {
                return response()->json(['message' => 'Check-in is required first.'], 422);
            }
            if (filled($record->check_out_at)) {
                return response()->json($this->response($student, $record, 'duplicate'));
            }
            $record->update(['check_out_at' => now()->format('H:i:s')]);
        }

        return response()->json($this->response($student, $record->fresh(), $action));
    }

    private function academyId(Request $request): int
    {
        abort_unless($request->user() instanceof Academies, 403);

        return $request->user()->id;
    }

    private function response(AcademyStudent $student, $record, string $action): array
    {
        $subscription = $student->subscriptions->sortByDesc('starts_on')->first();
        $valid = $subscription && $subscription->status === 'active'
            && (!$subscription->ends_on || $subscription->ends_on->isToday() || $subscription->ends_on->isFuture());

        return [
            'action' => $action,
            'student' => ['id' => $student->id, 'name' => $student->name, 'image' => $student->avatarUrl()],
            'record' => ['status' => $record->status, 'check_in' => $record->check_in_at, 'check_out' => $record->check_out_at],
            'subscription' => ['valid' => (bool) $valid, 'ends_on' => $subscription?->ends_on?->format('Y-m-d')],
        ];
    }
}
