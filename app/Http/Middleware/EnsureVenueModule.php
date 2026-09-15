<?php

namespace App\Http\Middleware;

use App\Models\PartnerUser;
use Closure;
use Illuminate\Http\Request;

class EnsureVenueModule
{
    public function handle(Request $request, Closure $next)
    {
        $user = auth('academy')->user();
        if (!$user) {
            return redirect()->route('academy.loginPage');
        }

        $academy = $user instanceof PartnerUser ? $user->academy : $user;
        if (!$academy || !$academy->hasVenueModule()) {
            abort(403, trans('admin.venues.subscription_inactive') ?: 'عفواً، ميزة إدارة الملاعب غير مفعلة لنوع نشاط حسابك أو باقة اشتراكك.');
        }

        return $next($request);
    }
}
