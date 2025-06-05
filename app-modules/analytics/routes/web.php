<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Modules\Analytics\Http\Controllers\AnalyticsController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('analytics')
    ->name('analytics.')
    ->group(function (): void {
        Route::get('/', AnalyticsController::class)
            ->name('index');
    });
