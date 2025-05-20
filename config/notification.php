<?php

declare(strict_types=1);

use Modules\Notification\Notifications\TestNotification;
use Modules\Workspace\Notifications\MemberAcceptedNotification;

return [
    'notifications' => [
        MemberAcceptedNotification::class,
        TestNotification::class,
    ],
];
