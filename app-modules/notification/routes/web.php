<?php

declare(strict_types=1);

use Modules\Notification\Http\Controllers\NotificationController;
use Modules\Notification\Http\Controllers\NotificationSettingsController;

Route::middleware(['web', 'auth', 'verified'])
    ->prefix('notifications')
    ->name('notifications.')
    ->group(function (): void {
        Route::get('/', [NotificationController::class, 'index'])
            ->name('index');

        Route::patch('/{notification}', [NotificationController::class, 'markAsRead'])
            ->whereUuid('notification')
            ->name('mark-as-read');
        Route::patch('/{notification}/unread', [NotificationController::class, 'markAsUnread'])
            ->whereUuid('notification')
            ->name('mark-as-unread');
    });

Route::middleware(['web', 'auth', 'verified'])->group(function (): void {
    Route::get('settings/notifications', [NotificationSettingsController::class, 'index'])
        ->name('settings.notifications.edit');

    Route::put('settings/notifications', [NotificationSettingsController::class, 'update'])
        ->name('settings.notifications.update');
});
