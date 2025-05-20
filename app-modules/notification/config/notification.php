<?php

declare(strict_types=1);

use Modules\Notification\Notifications\TestNotification;

/**
 * @return array{
 *   notifications: class-string<\Modules\Notification\Contracts\InAppNotification>[]
 * }
 */
return [
    'notifications' => [
        TestNotification::class,
    ],
];
