<?php

namespace App\Http\Controllers;

use App\Models\AcademyStudent;
use App\Models\Coach;
use App\Models\WhatsAppChannel;
use App\Models\WhatsAppConversation;
use App\Models\WhatsAppMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class WhatsAppWebhookController extends Controller
{
    public function verify(Request $request)
    {
        if ($request->input('hub_mode') !== 'subscribe' && $request->input('hub.mode') !== 'subscribe') abort(403);
        $token = (string) ($request->input('hub_verify_token') ?: $request->input('hub.verify_token'));
        $challenge = $request->input('hub_challenge') ?: $request->input('hub.challenge');
        $matched = WhatsAppChannel::all()->contains(fn ($channel) => $channel->verify_token && hash_equals($channel->verify_token, $token));
        abort_unless($matched, 403);
        return response((string) $challenge, 200)->header('Content-Type', 'text/plain');
    }

    public function receive(Request $request)
    {
        $signature = (string) $request->header('X-Hub-Signature-256');
        $validSignature = WhatsAppChannel::all()->contains(function ($channel) use ($request, $signature) {
            if (!$channel->app_secret || !$signature) return false;
            $expected = 'sha256=' . hash_hmac('sha256', $request->getContent(), $channel->app_secret);
            return hash_equals($expected, $signature);
        });
        abort_unless($validSignature, 403);

        foreach ((array) $request->input('entry', []) as $entry) {
            foreach ((array) data_get($entry, 'changes', []) as $change) {
                $value = (array) data_get($change, 'value', []);
                $phoneNumberId = data_get($value, 'metadata.phone_number_id');
                $channel = $phoneNumberId ? WhatsAppChannel::where('phone_number_id', $phoneNumberId)->first() : null;
                if (!$channel) continue;
                $channel->update(['last_webhook_at' => now()]);
                $this->handleStatuses($channel, (array) data_get($value, 'statuses', []));
                $this->handleInbound($channel, (array) data_get($value, 'messages', []), (array) data_get($value, 'contacts', []));
            }
        }
        return response()->json(['received' => true]);
    }

    private function handleStatuses(WhatsAppChannel $channel, array $statuses): void
    {
        foreach ($statuses as $status) {
            $message = WhatsAppMessage::where('academy_id', $channel->academy_id)->where('provider_message_id', data_get($status, 'id'))->first();
            if (!$message) continue;
            $name = (string) data_get($status, 'status', 'sent');
            $changes = ['status' => $name, 'payload' => $status];
            if ($name === 'sent') $changes['sent_at'] = $message->sent_at ?: now();
            if ($name === 'delivered') $changes['delivered_at'] = now();
            if ($name === 'read') $changes['read_at'] = now();
            if ($name === 'failed') $changes['error_message'] = data_get($status, 'errors.0.title') ?: data_get($status, 'errors.0.message');
            $message->update($changes);
        }
    }

    private function handleInbound(WhatsAppChannel $channel, array $messages, array $contacts): void
    {
        foreach ($messages as $incoming) {
            $phone = preg_replace('/\D+/', '', (string) data_get($incoming, 'from'));
            if (!$phone) continue;
            [$type, $id, $name] = $this->resolveContact($channel->academy_id, $phone, data_get($contacts, '0.profile.name'));
            $conversation = WhatsAppConversation::firstOrCreate(
                ['academy_id' => $channel->academy_id, 'phone' => $phone],
                ['contact_type' => $type, 'contact_id' => $id, 'contact_name' => $name]
            );
            $timestamp = Carbon::createFromTimestamp((int) data_get($incoming, 'timestamp', now()->timestamp));
            $providerId = data_get($incoming, 'id') ?: 'inbound:' . sha1(json_encode($incoming));
            WhatsAppMessage::firstOrCreate(
                ['provider_message_id' => $providerId],
                ['academy_id' => $channel->academy_id, 'conversation_id' => $conversation->id, 'direction' => 'inbound', 'message_type' => data_get($incoming, 'type', 'text'), 'body' => $this->messageBody($incoming), 'status' => 'received', 'payload' => $incoming, 'sent_at' => $timestamp]
            );
            $conversation->update([
                'contact_type' => $conversation->contact_type ?: $type, 'contact_id' => $conversation->contact_id ?: $id,
                'contact_name' => $conversation->contact_name ?: $name, 'last_message_at' => $timestamp,
                'last_inbound_at' => $timestamp, 'service_window_expires_at' => $timestamp->copy()->addHours(24),
                'unread_count' => $conversation->unread_count + 1,
            ]);
        }
    }

    private function resolveContact(int $academyId, string $phone, ?string $fallback): array
    {
        $local = str_starts_with($phone, '20') ? '0' . substr($phone, 2) : $phone;
        $student = AcademyStudent::where('academy_id', $academyId)->where(fn ($q) => $q->whereIn('phone', [$phone, '+' . $phone, $local])->orWhereIn('guardian_phone', [$phone, '+' . $phone, $local]))->first();
        if ($student) return ['student', $student->id, $student->name];
        $coach = Coach::where('academy_id', $academyId)->whereIn('phone', [$phone, '+' . $phone, $local])->first();
        if ($coach) return ['coach', $coach->id, $coach->name];
        return [null, null, $fallback ?: $phone];
    }

    private function messageBody(array $message): string
    {
        return (string) (data_get($message, 'text.body')
            ?: data_get($message, 'button.text')
            ?: data_get($message, 'interactive.button_reply.title')
            ?: data_get($message, 'interactive.list_reply.title')
            ?: '[' . data_get($message, 'type', 'message') . ']');
    }
}
