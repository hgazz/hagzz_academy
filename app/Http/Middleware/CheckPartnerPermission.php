<?php

namespace App\Http\Middleware;

use App\Models\Academies;
use App\Models\PartnerUser;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPartnerPermission
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        $user = auth('academy')->user();
        if (!$user) {
            return redirect()->route('academy.loginPage');
        }

        // If logged in user is the main Academy owner account or has owner flag
        if ($user instanceof Academies || !($user instanceof PartnerUser) || !empty($user->is_owner)) {
            return $next($request);
        }

        // For staff (PartnerUser), check specific permission
        if (method_exists($user, 'hasPermissionTo') && $user->hasPermissionTo($permission)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'غير مصرح لك بالوصول لهذه الوظيفة بحسابك الحالي.'], 403);
        }

        abort(403, 'غير مصرح لك بالوصول لهذه الصفحة بحسابك الحالي (حساب موظف).');
    }
}
