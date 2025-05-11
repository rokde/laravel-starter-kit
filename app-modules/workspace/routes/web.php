<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Workspace\Http\Controllers\Api\InternalApiWorkspaceController;
use Modules\Workspace\Http\Controllers\WorkspaceController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('workspaces')
    ->name('workspaces.')
    ->group(function (): void {
        Route::post('/', [WorkspaceController::class, 'store'])
            ->name('store');

        Route::get('/current', [WorkspaceController::class, 'show'])
            ->name('show');
        Route::patch('/current', [WorkspaceController::class, 'update'])
            ->name('update');

        Route::put('/set-current', [WorkspaceController::class, 'setCurrent'])
            ->name('set-current');
    });

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('internal-api/workspaces')
    ->name('internal.api.workspaces.')
    ->group(function (): void {
        Route::get('/', [InternalApiWorkspaceController::class, 'index'])
            ->name('index');
    });
