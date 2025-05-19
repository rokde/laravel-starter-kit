<?php

declare(strict_types=1);

namespace Modules\Notification\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Workspace\Notifications\MemberAcceptedNotification;

class NotificationSettingsController
{
    public function index(Request $request): Response
    {
        $notifications = collect([
            [
                'class' => MemberAcceptedNotification::class,
                'group' => MemberAcceptedNotification::getGroup(),
                'description' => MemberAcceptedNotification::getDescription(),
            ],
        ]);

        return Inertia::render('notification::Settings', [
            'channels' => [
                [
                    'label' => __('In-App'),
                    'value' => 'database',
                ],
                [
                    'label' => __('Email'),
                    'value' => 'mail',
                ],
            ],
            'notifications' => $notifications->groupBy('group'),
            'preferred_notification_channels' => $request->user()->preferred_notification_channels ?? [],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $request->user()->update([
            'preferred_notification_channels' => $request->all(),
        ]);

        return redirect()->back()->with('message', 'Settings saved.');
    }
}
