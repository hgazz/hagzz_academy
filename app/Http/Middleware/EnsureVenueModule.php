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
        if ($academy) {
            $subscription = $academy->currentSubscription()->with('plan')->first();
            if ($subscription && $subscription->plan) {
                if (in_array($subscription->status, ['expired', 'suspended', 'cancelled'], true)) {
                    abort(403, trans('admin.venues.subscription_inactive') ?: 'عفواً، باقة الاشتراك الحالية غير نشطة.');
                }
            }
        }

        return $next($request);
    }
}
