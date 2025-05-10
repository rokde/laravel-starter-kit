<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Workspace\Http\Controllers\WorkspaceController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('workspaces')
    ->name('workspaces.')
    ->group(function (): void {
        Route::post('/', [WorkspaceController::class, 'store'])
            ->name('store');

        Route::put('/current', [WorkspaceController::class, 'setCurrent'])
            ->name('set-current');
    });

// use Modules\Workspace\Http\Controllers\WorkspaceController;

// Route::get('/workspaces', [WorkspaceController::class, 'index'])->name('workspaces.index');
// Route::get('/workspaces/create', [WorkspaceController::class, 'create'])->name('workspaces.create');
// Route::post('/workspaces', [WorkspaceController::class, 'store'])->name('workspaces.store');
// Route::get('/workspaces/{workspace}', [WorkspaceController::class, 'show'])->name('workspaces.show');
// Route::get('/workspaces/{workspace}/edit', [WorkspaceController::class, 'edit'])->name('workspaces.edit');
// Route::put('/workspaces/{workspace}', [WorkspaceController::class, 'update'])->name('workspaces.update');
// Route::delete('/workspaces/{workspace}', [WorkspaceController::class, 'destroy'])->name('workspaces.destroy');
