<?php

declare(strict_types=1);

use Modules\Todo\Notifications\TodoAssignedNotification;
use Modules\Todo\Notifications\TodoIsDueTodayNotification;
use Modules\Workspace\Notifications\MemberAcceptedNotification;

/**
 * @return array{
 *   notifications: class-string<\Modules\Notification\Contracts\InAppNotification>[]
 * }
 */
return [
    'notifications' => [
        // workspace
        MemberAcceptedNotification::class,
        // todo
        TodoAssignedNotification::class,
        TodoIsDueTodayNotification::class,
    ],
];
