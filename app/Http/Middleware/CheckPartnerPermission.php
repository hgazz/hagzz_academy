<?php

namespace App\Http\Middleware;

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

        if ($user->is_owner || $user->hasPermissionTo($permission)) {
            return $next($request);
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'غير مصرح لك بالوصول لهذه الوظيفة بحسابك الحالي.'], 403);
        }

        abort(403, 'غير مصرح لك بالوصول لهذه الصفحة بحسابك الحالي (حساب موظف).');
    }
}
