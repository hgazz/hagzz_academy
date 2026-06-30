<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureVenueModule
{
    public function handle(Request $request, Closure $next)
    {
        $academy = auth('academy')->user();
        $subscription = $academy?->currentSubscription()->with('plan')->first();
        $activeSubscription = $subscription
            && in_array($subscription->status, ['active', 'trial'], true)
            && (!$subscription->ends_at || $subscription->ends_at->isToday() || $subscription->ends_at->isFuture())
            && $subscription->plan?->active;

        abort_unless($academy?->hasVenueModule() && $activeSubscription, 403);
        return $next($request);
    }
}
