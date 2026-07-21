<?php

namespace App\Http\Controllers;

use App\Models\WhatsAppChannel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class WhatsAppSettingsController extends Controller
{
    public function edit()
    {
        $channel = WhatsAppChannel::firstOrCreate(
            ['academy_id' => auth('academy')->id()],
            ['verify_token' => Str::random(48), 'default_country_code' => '20']
        );

        return view('Academy.pages.whatsapp.settings', compact('channel'));
    }

    public function update(Request $request)
    {
        $channel = WhatsAppChannel::firstOrNew(['academy_id' => auth('academy')->id()]);
        $validated = $request->validate([
            'business_account_id' => ['required', 'string', 'max:100'],
            'phone_number_id' => ['required', 'string', 'max:100', Rule::unique('whatsapp_channels', 'phone_number_id')->ignore($channel->id)],
            'display_phone_number' => ['nullable', 'string', 'max:30'],
            'access_token' => ['nullable', 'string', 'min:20'],
            'app_secret' => ['nullable', 'string', 'min:20'],
            'default_country_code' => ['required', 'digits_between:1,5'],
        ]);

        if (!$channel->verify_token) $channel->verify_token = Str::random(48);
        if (blank($validated['access_token'] ?? null)) unset($validated['access_token']);
        if (blank($validated['app_secret'] ?? null)) unset($validated['app_secret']);
        $channel->fill($validated);
        $channel->status = filled($channel->access_token) && filled($channel->app_secret) ? 'active' : 'pending';
        $channel->connected_at = $channel->status === 'active' ? now() : null;
        $channel->save();

        return back()->with('success', app()->getLocale() === 'ar' ? 'تم حفظ إعدادات WhatsApp.' : 'WhatsApp settings saved.');
    }
}
