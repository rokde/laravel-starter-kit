<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Todo\Http\Controllers\TodoController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('todos')
    ->name('todos.')
    ->group(function (): void {
        Route::get('/', [TodoController::class, 'index'])
            ->name('index');

        Route::get('/create', [TodoController::class, 'create'])
            ->name('create');

        Route::post('/', [TodoController::class, 'store'])
            ->name('store');

        Route::patch('/{todo}', [TodoController::class, 'update'])
            ->whereNumber('todo')
            ->name('update');

        Route::patch('/{todo}/toggle-complete', [TodoController::class, 'toggleComplete'])
            ->whereNumber('todo')
            ->name('toggle-complete');

        Route::delete('/{todo}', [TodoController::class, 'destroy'])
            ->whereNumber('todo')
            ->name('destroy');
    });
