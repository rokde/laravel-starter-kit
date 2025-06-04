<?php

declare(strict_types=1);

use Modules\Todo\Notifications\TodoAssignedNotification;
use Modules\Workspace\Notifications\MemberAcceptedNotification;

/**
 * @return array{
 *   notifications: class-string<\Modules\Notification\Contracts\InAppNotification>[]
 * }
 */
return [
    'notifications' => [
        MemberAcceptedNotification::class,
        TodoAssignedNotification::class,
    ],
];
