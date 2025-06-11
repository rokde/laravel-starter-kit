<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Data\Timezones;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\LocaleUpdateRequest;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LocaleController extends Controller
{
    public function edit(Request $request, Timezones $timezones): Response
    {
        abort_unless($request->user() instanceof HasLocalePreference, 404);

        return Inertia::render('settings/Locale', [
            'locale' => $request->user()->preferredLocale(),
            'locales' => config('app.locales', [config('app.fallback_locale', 'en')]),
            'timezone' => $request->user()->timezone,
            'timezones' => $timezones->selectableList(),
        ]);
    }

    public function update(LocaleUpdateRequest $request): RedirectResponse
    {
        abort_unless($request->user() instanceof HasLocalePreference, 404);

        $request->user()->fill($request->validated());
        $request->user()->save();

        return to_route('locale.edit');
    }
}
