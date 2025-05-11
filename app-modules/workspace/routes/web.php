<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Workspace\Http\Controllers\WorkspaceController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('workspaces')
    ->name('workspaces.')
    ->group(function (): void {
        Route::get('/', [WorkspaceController::class, 'index'])
            ->name('index');

        Route::post('/', [WorkspaceController::class, 'store'])
            ->name('store');

        Route::put('/current', [WorkspaceController::class, 'setCurrent'])
            ->name('set-current');
    });
