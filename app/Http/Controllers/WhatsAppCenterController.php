<?php

namespace App\Http\Controllers;

use App\Models\AcademyStudent;
use App\Models\Coach;
use App\Models\WhatsAppCampaign;
use App\Models\WhatsAppChannel;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use App\Services\WhatsAppCloudService;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Throwable;

class WhatsAppCenterController extends Controller
{
    public function index(Request $request)
    {
        $academyId = auth('academy')->id();
        $search = trim((string) $request->input('search'));
        $conversations = WhatsAppConversation::where('academy_id', $academyId)
            ->with(['messages' => fn ($q) => $q->latest()->limit(1)])
            ->when($search, fn ($q) => $q->where(fn ($q) => $q->where('contact_name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")))
            ->latest('last_message_at')->paginate(24)->withQueryString();
        $campaigns = WhatsAppCampaign::where('academy_id', $academyId)->latest()->limit(8)->get();
        $channel = WhatsAppChannel::where('academy_id', $academyId)->first();

        return view('Academy.pages.whatsapp.index', compact('conversations', 'campaigns', 'channel'));
    }

    public function show(WhatsAppConversation $conversation)
    {
        abort_unless($conversation->academy_id === auth('academy')->id(), 404);
        $conversation->update(['unread_count' => 0]);
        $messages = $conversation->messages()->latest()->limit(60)->get()->reverse()->values();
        $channel = WhatsAppChannel::where('academy_id', auth('academy')->id())->first();
        return view('Academy.pages.whatsapp.show', compact('conversation', 'messages', 'channel'));
    }

    public function compose(Request $request, WhatsAppCloudService $whatsApp)
    {
        $academyId = auth('academy')->id();
        $selected = collect((array) $request->input('recipients'))->filter()->unique()->values();

        if ($selected->count() === 1) {
            $recipient = $this->resolveRecipients($selected->all(), $academyId)->first();
            $countryCode = WhatsAppChannel::where('academy_id', $academyId)->value('default_country_code') ?: '20';
            $phone = $recipient ? $whatsApp->normalizePhone($recipient['phone'], $countryCode) : null;
            if ($phone) {
                $academy = auth('academy')->user();
                $academyName = (string) ($academy->commercial_name ?: $academy->name ?: 'Hagzz');
                $text = app()->getLocale() === 'ar'
                    ? "مرحبًا {$recipient['name']}، معك فريق {$academyName}. كيف يمكننا مساعدتك؟"
                    : "Hello {$recipient['name']}, this is the {$academyName} team. How can we help you?";

                return redirect()->away('https://wa.me/' . $phone . '?text=' . rawurlencode($text));
            }
        }

        $recipientStudents = AcademyStudent::where('academy_id', $academyId)->where(fn ($q) => $q->whereNotNull('phone')->orWhereNotNull('guardian_phone'))->orderBy('name')->get();
        $recipientCoaches = Coach::where('academy_id', $academyId)->whereNotNull('phone')->orderBy('id')->get();
        $selectedRecipients = $selected->all();
        return view('Academy.pages.whatsapp.compose', compact('recipientStudents', 'recipientCoaches', 'selectedRecipients'));
    }

    public function send(Request $request, WhatsAppCloudService $whatsApp)
    {
        $validated = $request->validate([
            'recipients' => ['required', 'array', 'min:1', 'max:50'],
            'recipients.*' => ['required', 'regex:/^(student|coach):\d+$/'],
            'message_type' => ['required', 'in:text,template'],
            'body' => ['nullable', 'required_if:message_type,text', 'string', 'max:4096'],
            'template_name' => ['nullable', 'required_if:message_type,template', 'regex:/^[a-z0-9_]+$/', 'max:512'],
            'template_language' => ['nullable', 'required_if:message_type,template', 'string', 'max:12'],
            'template_parameters' => ['nullable', 'string', 'max:4000'],
            'campaign_name' => ['nullable', 'string', 'max:150'],
            'purpose' => ['required', 'in:notification,marketing'],
            'marketing_opt_in' => ['nullable', 'accepted_if:purpose,marketing'],
        ]);

        if ($validated['purpose'] === 'marketing' && $validated['message_type'] !== 'template') {
            return back()->withInput()->withErrors(['message_type' => app()->getLocale() === 'ar' ? 'الرسائل التسويقية يجب أن تستخدم قالب Meta معتمدًا.' : 'Marketing messages must use an approved Meta template.']);
        }

        $academyId = auth('academy')->id();
        $channel = WhatsAppChannel::where('academy_id', $academyId)->first();
        if (!$channel?->isReady()) return back()->withInput()->withErrors(['channel' => app()->getLocale() === 'ar' ? 'اربط حساب WhatsApp Business أولًا.' : 'Connect WhatsApp Business first.']);

        $recipients = $this->resolveRecipients($validated['recipients'], $academyId)
            ->map(function ($recipient) use ($whatsApp, $channel) {
                $recipient['normalized_phone'] = $whatsApp->normalizePhone($recipient['phone'], $channel->default_country_code);
                return $recipient;
            })
            ->filter(fn ($recipient) => filled($recipient['normalized_phone']))
            ->unique('normalized_phone')
            ->values();
        if ($recipients->isEmpty()) {
            return back()->withInput()->withErrors(['recipients' => app()->getLocale() === 'ar' ? 'لا يوجد رقم هاتف صالح ضمن الحسابات المحددة.' : 'No valid phone number was found for the selected accounts.']);
        }
        $templateParameters = collect(preg_split('/\R/', (string) ($validated['template_parameters'] ?? '')))->map(fn ($value) => trim($value))->filter()->values()->all();
        $campaign = WhatsAppCampaign::create([
            'academy_id' => $academyId,
            'name' => $validated['campaign_name'] ?: (app()->getLocale() === 'ar' ? 'إرسال مباشر' : 'Direct send'),
            'audience' => $recipients->count() > 1 ? 'custom_bulk' : 'individual',
            'purpose' => $validated['purpose'],
            'message_type' => $validated['message_type'],
            'template_name' => $validated['template_name'] ?? null,
            'template_language' => $validated['template_language'] ?? null,
            'template_parameters' => $templateParameters,
            'body' => $validated['body'] ?? null,
            'status' => 'sending', 'total_recipients' => $recipients->count(), 'started_at' => now(),
        ]);

        $sent = 0; $failed = 0;
        foreach ($recipients as $recipient) {
            $phone = $recipient['normalized_phone'];
            $personalize = fn ($value) => str_replace(['{name}', '{phone}'], [$recipient['name'], $phone], (string) $value);
            $personalizedBody = isset($validated['body']) ? $personalize($validated['body']) : null;
            $personalizedParameters = array_map($personalize, $templateParameters);
            $conversation = WhatsAppConversation::firstOrCreate(
                ['academy_id' => $academyId, 'phone' => $phone],
                ['contact_type' => $recipient['type'], 'contact_id' => $recipient['id'], 'contact_name' => $recipient['name']]
            );
            $message = WhatsAppMessage::create([
                'academy_id' => $academyId, 'conversation_id' => $conversation->id, 'campaign_id' => $campaign->id,
                'direction' => 'outbound', 'message_type' => $validated['message_type'],
                'body' => $personalizedBody ?: ('Template: ' . ($validated['template_name'] ?? '')),
                'template_name' => $validated['template_name'] ?? null, 'status' => 'queued',
            ]);
            try {
                $response = $validated['message_type'] === 'template'
                    ? $whatsApp->sendTemplate($channel, $phone, $validated['template_name'], $validated['template_language'], $personalizedParameters)
                    : $whatsApp->sendText($channel, $phone, $personalizedBody);
                $message->update(['provider_message_id' => data_get($response, 'messages.0.id'), 'status' => 'sent', 'sent_at' => now(), 'payload' => $response]);
                $conversation->update(['last_message_at' => now()]);
                $sent++;
            } catch (Throwable $exception) {
                $message->update(['status' => 'failed', 'error_message' => $exception->getMessage()]);
                $failed++;
            }
        }
        $campaign->update(['status' => $failed && !$sent ? 'failed' : 'completed', 'sent_count' => $sent, 'failed_count' => $failed, 'completed_at' => now()]);

        return redirect()->route('academy.whatsapp.index')->with('success', (app()->getLocale() === 'ar' ? "تم إرسال {$sent} رسالة، وفشل {$failed}." : "Sent {$sent} messages; {$failed} failed."));
    }

    public function reply(Request $request, WhatsAppConversation $conversation, WhatsAppCloudService $whatsApp)
    {
        abort_unless($conversation->academy_id === auth('academy')->id(), 404);
        $validated = $request->validate(['body' => ['required', 'string', 'max:4096']]);
        if (!$conversation->serviceWindowIsOpen()) return back()->withErrors(['body' => app()->getLocale() === 'ar' ? 'انتهت نافذة المحادثة؛ استخدم رسالة قالب معتمدة.' : 'The service window is closed; use an approved template.']);
        $channel = WhatsAppChannel::where('academy_id', auth('academy')->id())->firstOrFail();
        $message = WhatsAppMessage::create(['academy_id' => $conversation->academy_id, 'conversation_id' => $conversation->id, 'direction' => 'outbound', 'message_type' => 'text', 'body' => $validated['body'], 'status' => 'queued']);
        try {
            $response = $whatsApp->sendText($channel, $conversation->phone, $validated['body']);
            $message->update(['provider_message_id' => data_get($response, 'messages.0.id'), 'status' => 'sent', 'sent_at' => now(), 'payload' => $response]);
            $conversation->update(['last_message_at' => now()]);
        } catch (Throwable $exception) {
            $message->update(['status' => 'failed', 'error_message' => $exception->getMessage()]);
            return back()->withErrors(['body' => $exception->getMessage()]);
        }
        return back();
    }

    private function resolveRecipients(array $keys, int $academyId): Collection
    {
        return collect($keys)->unique()->map(function ($key) use ($academyId) {
            [$type, $id] = explode(':', $key, 2);
            if ($type === 'student') {
                $student = AcademyStudent::where('academy_id', $academyId)->find($id);
                if (!$student) return null;
                return ['type' => 'student', 'id' => $student->id, 'name' => $student->name, 'phone' => $student->phone ?: $student->guardian_phone];
            }
            $coach = Coach::where('academy_id', $academyId)->find($id);
            return $coach ? ['type' => 'coach', 'id' => $coach->id, 'name' => $coach->name, 'phone' => $coach->phone] : null;
        })->filter(fn ($recipient) => $recipient && filled($recipient['phone']))->values();
    }
}
