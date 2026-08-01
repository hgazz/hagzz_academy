<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', session('laravel_localization_locale', 'ar'));
        if (in_array($locale, ['ar', 'en'])) {
            app()->setLocale($locale);
            try {
                LaravelLocalization::setLocale($locale);
            } catch (\Throwable $e) {
                // Fallback gracefully
            }
        }

        return $next($request);
    }
}
