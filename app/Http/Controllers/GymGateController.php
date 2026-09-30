<?php

namespace App\Http\Controllers;

use App\Models\AcademyStudent;
use App\Models\AcademyStudentSubscription;
use App\Models\GymGateEntry;
use App\Models\PartnerUser;
use App\Support\MembershipCode;
use Illuminate\Http\Request;

/**
 * GymGateController — ماسح بوابة الجيم والمركز الصحي
 *
 * للمنشآت التي لا تعتمد على نظام الحصص (gym / health_center / hybrid):
 * يُسجَّل دخول العضو وخروجه عبر مسح QR أو الباركود مباشرةً دون ربطه بحصة معينة.
 *
 * المزايا:
 *  - يتحقق من صلاحية الاشتراك
 *  - يُعيد آخر سجل دخول مفتوح (بدون خروج) ويُغلقه عند المسح الثاني
 *  - يُرجع JSON لواجهة الماسح
 *  - يعرض تقرير الدخول اليومي
 */
class GymGateController extends Controller
{
    private function getAcademyId(): int
    {
        $user = auth('academy')->user();
        if ($user instanceof PartnerUser) {
            return (int) $user->academy_id;
        }
        return (int) ($user?->id ?? auth('academy')->id());
    }

    // ── واجهة الماسح ────────────────────────────────────────────────────

    public function scanner()
    {
        $academyId = $this->getAcademyId();

        // إحصائيات اليوم
        $todayStats = [
            'entries' => GymGateEntry::where('academy_id', $academyId)
                ->whereDate('entered_at', today())
                ->count(),
            'inside'  => GymGateEntry::where('academy_id', $academyId)
                ->whereDate('entered_at', today())
                ->whereNull('exited_at')
                ->count(),
        ];

        return view('Academy.pages.gym_gate.scanner', compact('todayStats'));
    }

    // ── مسح الكارت (AJAX) ───────────────────────────────────────────────

    public function scan(Request $request)
    {
        $data = $request->validate([
            'code'    => ['required', 'string', 'max:120'],
            'station' => ['nullable', 'string', 'max:60'],
            'method'  => ['nullable', 'in:qr,barcode,manual,nfc'],
        ]);

        $academyId = $this->getAcademyId();
        $ar = app()->getLocale() === 'ar';

        // ── 1. تحديد هوية العضو ─────────────────────────────────────────
        $studentId = MembershipCode::studentId($data['code'], $academyId);
        if (!$studentId) {
            return response()->json([
                'status'  => 'error',
                'message' => $ar ? 'الكارت غير صالح لهذه المنشأة.' : 'Card is not valid for this facility.',
            ], 422);
        }

        $student = AcademyStudent::with(['subscriptions.payments'])
            ->where('academy_id', $academyId)
            ->findOrFail($studentId);

        // ── 2. فحص حالة العضو والاشتراك ──────────────────────────────
        $isSuspended = $student->status === 'suspended';

        $subscription = $student->subscriptions
            ->sortByDesc('starts_on')
            ->first();

        $isFrozen = $subscription && $subscription->status === 'frozen';

        $subscriptionValid = !$isSuspended
            && $subscription
            && $subscription->status === 'active'
            && $subscription->ends_on
            && ($subscription->ends_on->isToday() || $subscription->ends_on->isFuture());

        // ── 3. هل هناك دخول مفتوح (بدون خروج)؟ ──────────────────────
        $openEntry = GymGateEntry::where('academy_id', $academyId)
            ->where('academy_student_id', $student->id)
            ->whereNull('exited_at')
            ->whereDate('entered_at', today())
            ->latest('entered_at')
            ->first();

        if ($openEntry) {
            // أغلق سجل الدخول — هذا خروج
            $openEntry->update(['exited_at' => now()]);
            $duration = (int) $openEntry->entered_at->diffInMinutes(now());

            return response()->json([
                'status'    => 'exit',
                'direction' => 'out',
                'duration'  => $duration,
                'student'   => $this->studentData($student),
                'subscription' => $this->subscriptionData($subscription, $subscriptionValid, $student),
                'message'   => $ar
                    ? "مع السلامة {$student->name} — مدة الجلسة: {$duration} دقيقة"
                    : "Goodbye {$student->name} — Session: {$duration} min",
            ]);
        }

        // ── 4. تسجيل دخول جديد ────────────────────────────────────────
        GymGateEntry::create([
            'academy_id'            => $academyId,
            'academy_student_id'    => $student->id,
            'academy_subscription_id' => $subscription?->id,
            'entered_at'            => now(),
            'direction'             => 'in',
            'scan_method'           => $data['method'] ?? 'qr',
            'station'               => $data['station'] ?? null,
            'subscription_valid'    => $subscriptionValid,
        ]);

        // استهلاك جلسة إذا كان المركز الصحي ويتتبع الجلسات
        if ($subscription && $subscription->sessions_total !== null && $subscriptionValid) {
            $subscription->consumeSession();
        }

        // تحديد نص الرسالة وحالة الدخول
        $resultStatus = 'entry';
        if ($isSuspended) {
            $resultStatus = 'suspended';
            $msg = $ar ? "⚠️ تنبيه: حساب {$student->name} مجمّد / موقوف إدارياً!" : "⚠️ Alert: {$student->name}'s account is suspended!";
        } elseif ($isFrozen) {
            $resultStatus = 'frozen';
            $frozenUntilStr = $subscription->frozen_until ? $subscription->frozen_until->format('Y-m-d') : '';
            $reasonStr = $subscription->freeze_reason ? " (السبب: {$subscription->freeze_reason})" : '';
            $msg = $ar
                ? "❄️ تنبيه: اشتراك {$student->name} مجمد حالياً حتى {$frozenUntilStr}{$reasonStr}"
                : "❄️ Alert: {$student->name}'s subscription is frozen until {$frozenUntilStr}";
        } elseif (!$subscriptionValid) {
            $resultStatus = 'invalid';
            $msg = ($ar ? "أهلاً {$student->name}!" : "Welcome {$student->name}!") . ($ar ? ' ⚠️ تحذير: الاشتراك منتهٍ أو غير نشط!' : ' ⚠️ Warning: subscription expired or inactive!');
        } else {
            $msg = $ar ? "أهلاً بك {$student->name}! تم تسجيل الدخول بنجاح." : "Welcome {$student->name}! Check-in recorded.";
            // تنبيه اقتراب نفاد الجلسات
            if ($subscription && $subscription->sessions_total !== null) {
                $remSess = $subscription->remaining_sessions;
                if ($remSess !== null && $remSess <= 2) {
                    $msg .= $ar ? " (⚠️ متبقي {$remSess} جلسة فقط)" : " (⚠️ Only {$remSess} session(s) left)";
                }
            }
        }

        return response()->json([
            'status'    => $resultStatus,
            'direction' => 'in',
            'student'   => $this->studentData($student),
            'subscription' => $this->subscriptionData($subscription, $subscriptionValid, $student),
            'message'   => $msg,
        ]);
    }

    // ── سجل الدخول اليومي ───────────────────────────────────────────────

    public function log(Request $request)
    {
        $academyId = $this->getAcademyId();
        $date = $request->input('date', today()->toDateString());

        $entries = GymGateEntry::with(['student'])
            ->where('academy_id', $academyId)
            ->whereDate('entered_at', $date)
            ->latest('entered_at')
            ->paginate(50);

        $stats = [
            'total'   => GymGateEntry::where('academy_id', $academyId)->whereDate('entered_at', $date)->count(),
            'inside'  => GymGateEntry::where('academy_id', $academyId)->whereDate('entered_at', $date)->whereNull('exited_at')->count(),
            'invalid' => GymGateEntry::where('academy_id', $academyId)->whereDate('entered_at', $date)->where('subscription_valid', false)->count(),
        ];

        return view('Academy.pages.gym_gate.log', compact('entries', 'stats', 'date'));
    }

    // ── تسجيل دخول مرافق / ضيف بصحبة العضو ────────────────────────────────
    public function registerGuest(Request $request): JsonResponse
    {
        $academyId = $this->getAcademyId();
        $studentId = $request->input('student_id');
        $guestName = trim((string) $request->input('guest_name', ''));
        $station   = $request->input('station');

        $student = AcademyStudent::where('academy_id', $academyId)->findOrFail($studentId);

        $subscription = AcademyStudentSubscription::where('academy_student_id', $student->id)
            ->where('status', 'active')
            ->where(function ($q) {
                $q->whereNull('ends_on')->orWhere('ends_on', '>=', today());
            })
            ->latest('id')
            ->first();

        if (!$subscription) {
            return response()->json([
                'status'  => 'error',
                'message' => 'لا يوجد اشتراك سارٍ للعضو لتسجيل مرافق.',
            ], 422);
        }

        if ($subscription->remaining_guest_visits <= 0) {
            return response()->json([
                'status'  => 'error',
                'message' => 'عفواً، لقد استنفذ العضو كامل رصيد زيارات المرافقين المتاحة في باقته.',
            ], 422);
        }

        // استهلاك زيارة ضيف واحدة
        $subscription->consumeGuestVisit();

        // تسجيل الدخول في حركة البوابة
        $guestNote = 'مرافق / ضيف بصحبة العضو: ' . $student->name . ($guestName !== '' ? " ({$guestName})" : '');
        $entry = GymGateEntry::create([
            'academy_id'              => $academyId,
            'academy_student_id'      => $student->id,
            'academy_subscription_id' => $subscription->id,
            'entered_at'              => now(),
            'direction'               => 'in',
            'scan_method'             => 'guest_pass',
            'station'                 => $station,
            'subscription_valid'      => true,
            'is_guest'                => true,
            'notes'                   => $guestNote,
        ]);

        return response()->json([
            'status'                 => 'success',
            'message'                => "تم تسجيل دخول المرافق بنجاح! متبقي للعضو {$subscription->remaining_guest_visits} زيارة ضيف.",
            'guest_visits_remaining' => $subscription->remaining_guest_visits,
            'guest_visits_total'     => $subscription->guest_visits_total,
            'guest_visits_used'      => $subscription->guest_visits_used,
            'guest_note'             => $guestNote,
            'student_name'           => $student->name,
        ]);
    }

    // ── بيانات JSON مساعدة ──────────────────────────────────────────────

    private function studentData(AcademyStudent $student): array
    {
        return [
            'id'       => $student->id,
            'name'     => $student->name,
            'phone'    => $student->phone,
            'gender'   => $student->gender,
            'image'    => $student->avatarUrl(),
            'fallback' => $student->defaultImageUrl(),
        ];
    }

    private function subscriptionData(?AcademyStudentSubscription $sub, bool $valid, ?AcademyStudent $student = null): array
    {
        return [
            'valid'                  => $valid,
            'status'                 => $sub?->status,
            'ends_on'                => $sub?->ends_on?->format('Y-m-d'),
            'remaining_amount'       => $sub?->remaining_amount ?? 0,
            'sessions_remaining'     => $sub?->remaining_sessions,
            'guest_visits_total'     => $sub?->guest_visits_total ?? 0,
            'guest_visits_used'      => $sub?->guest_visits_used ?? 0,
            'guest_visits_remaining' => $sub?->remaining_guest_visits ?? 0,
            'is_frozen'              => $sub?->status === 'frozen',
            'frozen_until'           => $sub?->frozen_until?->format('Y-m-d'),
            'freeze_reason'          => $sub?->freeze_reason,
            'is_suspended'           => $student?->status === 'suspended',
        ];
    }
}
