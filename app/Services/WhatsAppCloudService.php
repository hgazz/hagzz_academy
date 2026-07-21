<?php

namespace App\Services;

use App\Models\WhatsAppChannel;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class WhatsAppCloudService
{
    public function normalizePhone(?string $phone, string $countryCode = '20'): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phone);
        if (!$digits) return null;
        if (str_starts_with($digits, '00')) $digits = substr($digits, 2);
        if (str_starts_with($digits, '0')) $digits = $countryCode . substr($digits, 1);
        if (!str_starts_with($digits, $countryCode) && strlen($digits) <= 10) $digits = $countryCode . $digits;
        return $digits;
    }

    public function sendText(WhatsAppChannel $channel, string $to, string $body): array
    {
        return $this->send($channel, [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'text',
            'text' => ['preview_url' => false, 'body' => $body],
        ]);
    }

    public function sendTemplate(WhatsAppChannel $channel, string $to, string $templateName, string $language = 'ar', array $parameters = []): array
    {
        $template = ['name' => $templateName, 'language' => ['code' => $language]];
        if ($parameters) {
            $template['components'] = [[
                'type' => 'body',
                'parameters' => array_map(fn ($value) => ['type' => 'text', 'text' => (string) $value], $parameters),
            ]];
        }

        return $this->send($channel, [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => $to,
            'type' => 'template',
            'template' => $template,
        ]);
    }

    private function send(WhatsAppChannel $channel, array $payload): array
    {
        if (!$channel->isReady()) throw new RuntimeException('WhatsApp channel is not connected.');

        $response = $this->client($channel)->post($this->messagesUrl($channel), $payload);
        if ($response->failed()) {
            $message = $response->json('error.message') ?: 'WhatsApp rejected the message.';
            throw new RuntimeException($message);
        }

        return $response->json();
    }

    private function client(WhatsAppChannel $channel): PendingRequest
    {
        return Http::withToken($channel->access_token)->acceptJson()->asJson()->timeout(25);
    }

    private function messagesUrl(WhatsAppChannel $channel): string
    {
        return rtrim(config('whatsapp.graph_url'), '/') . '/' . config('whatsapp.graph_version') . '/' . $channel->phone_number_id . '/messages';
    }
}
