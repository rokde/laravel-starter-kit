<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Workspace\Http\Controllers\WorkspaceController;
use Modules\Workspace\Http\Controllers\WorkspaceInvitationsController;
use Modules\Workspace\Http\Controllers\WorkspaceMembersController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('workspaces')
    ->name('workspaces.')
    ->group(function (): void {
        Route::get('/new', [WorkspaceController::class, 'create'])
            ->name('create');

        Route::post('/', [WorkspaceController::class, 'store'])
            ->name('store');

        Route::get('/current', [WorkspaceController::class, 'show'])
            ->name('show');
        Route::patch('/current', [WorkspaceController::class, 'update'])
            ->name('update');

        Route::put('/set-current', [WorkspaceController::class, 'setCurrent'])
            ->name('set-current');
        Route::get('/{id}', [WorkspaceController::class, 'makeCurrent'])
            ->whereNumber('id')
            ->name('make-current');

        Route::prefix('current/members')
            ->name('members.')
            ->group(function (): void {
                Route::get('/', [WorkspaceMembersController::class, 'index'])
                    ->name('index');

                Route::patch('/', [WorkspaceMembersController::class, 'update'])
                    ->name('update');

                Route::delete('/{member}', [WorkspaceMembersController::class, 'destroy'])
                    ->whereNumber('member')
                    ->name('delete');
            });

        Route::prefix('current/invitations')
            ->name('invitations.')
            ->group(function (): void {
                Route::get('/', [WorkspaceInvitationsController::class, 'index'])
                    ->name('index');

                Route::post('/', [WorkspaceInvitationsController::class, 'store'])
                    ->name('store');

                Route::delete('/{invitation}', [WorkspaceInvitationsController::class, 'destroy'])
                    ->whereNumber('invitation')
                    ->name('revoke');
            });
    });

Route::middleware(['web'])
    ->prefix('link')
    ->name('public.api.')
    ->group(function (): void {
        Route::get('/{invitation}/accept', [WorkspaceInvitationsController::class, 'acceptInvitation'])
            ->whereNumber('invitation')
            ->name('invitations.accept');
    });
