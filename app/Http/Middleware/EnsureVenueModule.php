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
        abort_unless($academy?->hasVenueModule($subscription), 403);
        return $next($request);
    }
}
