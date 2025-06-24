<?php

declare(strict_types=1);

use Modules\Todo\Notifications\TodoAssignedNotification;
use Modules\Todo\Notifications\TodoIsDueTodayNotification;
use Modules\Workspace\Notifications\GotWorkspaceInvitationNotification;
use Modules\Workspace\Notifications\MemberAcceptedNotification;
use Modules\Workspace\Notifications\WelcomeNewWorkspaceOwnerNotification;
use Modules\Workspace\Notifications\WorkspaceTransferredNotification;

/**
 * @return array{
 *   notifications: class-string<\Modules\Notification\Contracts\InAppNotification>[]
 * }
 */
return [
    'notifications' => [
        // workspace
        MemberAcceptedNotification::class,
        GotWorkspaceInvitationNotification::class,
        WelcomeNewWorkspaceOwnerNotification::class,
        WorkspaceTransferredNotification::class,
        // todo
        TodoAssignedNotification::class,
        TodoIsDueTodayNotification::class,
    ],
];
