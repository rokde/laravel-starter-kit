<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Passkey\Http\Controllers\PasskeysController;

Route::middleware(['web', 'auth', 'verified'])
    ->group(function (): void {
        Route::get('settings/passkeys', [PasskeysController::class, 'index'])
            ->name('settings.passkeys.edit');

        Route::post('settings/passkeys', [PasskeysController::class, 'store'])
            ->name('settings.passkeys.store');

        Route::delete('settings/passkeys/{passkey}', [PasskeysController::class, 'destroy'])
            ->whereNumber('passkey')
            ->name('settings.passkeys.destroy');
    });

Route::middleware(['web', 'guest'])
    ->group(function (): void {
        Route::passkeys();
    });
