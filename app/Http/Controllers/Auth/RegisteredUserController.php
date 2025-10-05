<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Actions\CreateNewUser;
use App\Http\Requests\Auth\StoreNewUserRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

final class RegisteredUserController
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
    public function store(StoreNewUserRequest $request, CreateNewUser $createNewUser): RedirectResponse
    {
        $user = $createNewUser->handle($request->toDto(), $request->password);

        Auth::login($user);

        return redirect()->intended('dashboard');
    }
}
