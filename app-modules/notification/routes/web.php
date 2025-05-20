<?php

declare(strict_types=1);

use Illuminate\Http\Request;
use Modules\Notification\Http\Controllers\NotificationController;
use Modules\Notification\Http\Controllers\NotificationSettingsController;
use Modules\Notification\Notifications\TestNotification;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('notifications')
    ->name('notifications.')
    ->group(function (): void {
        Route::get('/', [NotificationController::class, 'index'])
            ->name('index');

        Route::patch('/{notification}', [NotificationController::class, 'markAsRead'])
            ->whereUuid('notification')
            ->name('mark-as-read');
    });

Route::middleware(['web', 'auth', 'verified'])->group(function (): void {
    Route::get('settings/notifications', [NotificationSettingsController::class, 'index'])
        ->name('settings.notifications.edit');

    Route::put('settings/notifications', [NotificationSettingsController::class, 'update'])
        ->name('settings.notifications.update');
});
