<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Data\Timezones;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Show the registration page.
     */
    public function create(): Response
    {
        return Inertia::render('auth/Register', [
            'appliedRules' => Password::default()->appliedRules(),
        ]);
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $timezone = $locale = null;
        $requestLocale = $request->string('locale');
        if (in_array($requestLocale, config('app.locales', [config('app.fallback_locale', 'en')]))) {
            $locale = $requestLocale;
        }
        $requestTimezone = $request->string('timezone');
        if (in_array($requestTimezone, Timezones::identifiers())) {
            $timezone = $requestTimezone;
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'locale' => $locale ?? config('app.locale', 'en'),
            'timezone' => $timezone ?? 'UTC',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return to_route('dashboard');
    }
}
