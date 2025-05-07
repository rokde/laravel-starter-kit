<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Number;
use Symfony\Component\HttpFoundation\Response;

final readonly class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $locale = config('app.locale', config('app.fallback_locale', 'en'));
        if ($request->user() instanceof HasLocalePreference) {
            $locale = $request->user()->preferredLocale();
        }

        app()->setLocale($locale);
        Lang::setLocale($locale);
        Date::setLocale($locale);
        Number::useLocale($locale);

        return $next($request);
    }
}
