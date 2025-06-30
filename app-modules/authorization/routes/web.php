<?php

declare(strict_types=1);

use Modules\Authorization\Http\Controllers\RolesController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('workspaces/current/roles')
    ->name('workspaces.roles.')
    ->group(function (): void {
        Route::get('/', [RolesController::class, 'index'])
            ->name('index');

        Route::get('/create', [RolesController::class, 'create'])
            ->name('create');

        Route::post('/', [RolesController::class, 'store'])
            ->name('store');

        Route::get('/{role}', [RolesController::class, 'edit'])
            ->whereNumber('role')
            ->name('edit');

        Route::put('/{role}', [RolesController::class, 'update'])
            ->whereNumber('role')
            ->name('update');

        Route::delete('/{role}', [RolesController::class, 'destroy'])
            ->whereNumber('role')
            ->name('destroy');
    });
