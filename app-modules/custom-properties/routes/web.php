<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\CustomProperties\Http\Controllers\CustomPropertyDefinitionController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('custom-properties')
    ->name('custom-properties.')
    ->group(function (): void {
        Route::get('/', [CustomPropertyDefinitionController::class, 'index'])
            ->name('index');
        Route::post('/', [CustomPropertyDefinitionController::class, 'store'])
            ->name('store');
        Route::delete('/{definition}', [CustomPropertyDefinitionController::class, 'destroy'])
            ->whereNumber('definition')
            ->name('destroy');
    });
