<?php

declare(strict_types=1);

namespace Modules\Passkey\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Passkey\Http\Requests\StorePasskeyRequest;
use Spatie\LaravelPasskeys\Actions\GeneratePasskeyRegisterOptionsAction;
use Spatie\LaravelPasskeys\Actions\StorePasskeyAction;
use Spatie\LaravelPasskeys\Models\Passkey;
use Spatie\LaravelPasskeys\Support\Config;
use Throwable;

class PasskeysController
{
    private string $sessionKey = 'passkey-registration-options';

    public function index(Request $request): Response
    {
        return Inertia::render('passkey::Passkeys', [
            'passkeys' => $request->user()
                ->passkeys()
                ->get(['id', 'name', 'last_used_at'])
                ->map(fn (Passkey $passkey) =>
                    new \Modules\Passkey\DataTransferObjects\Passkey(
                        id: $passkey->id,
                        name: $passkey->name,
                        last_used_at: $passkey->last_used_at?->toDateTimeString(),
                    ))
                ->all(),
            'passkeyOptions' => json_decode($this->generatePasskeyOptions($request->user())),
        ]);
    }

    public function store(StorePasskeyRequest $request): RedirectResponse
    {
        $storePasskeyAction = Config::getAction('store_passkey', StorePasskeyAction::class);

        try {
            $storePasskeyAction->execute(
                $request->user(),
                $request->validated('passkey'),
                $this->previouslyGeneratedPasskeyOptions(),
                $request->getHost(),
                ['name' => $request->validated('name')]
            );
        } catch (Throwable $e) {
            throw ValidationException::withMessages([
                'name' => __('passkeys::passkeys.error_something_went_wrong_generating_the_passkey'),
            ]);
        }

        return redirect()
            ->back()
            ->with('message', 'Passkey created');
    }

    public function destroy(Request $request, Passkey $passkey): RedirectResponse
    {
        $request->user()
            ->passkeys()
            ->where('id', $passkey->id)
            ->delete();

        return redirect()
            ->back()
            ->with('message', 'Passkey removed');
    }

    private function generatePasskeyOptions(User $user): string
    {
        $generatePassKeyOptionsAction = Config::getAction('generate_passkey_register_options', GeneratePasskeyRegisterOptionsAction::class);

        $options = $generatePassKeyOptionsAction->execute($user);

        session()->put($this->sessionKey, $options);

        return $options;
    }

    private function previouslyGeneratedPasskeyOptions(): ?string
    {
        return session()->pull($this->sessionKey);
    }
}
