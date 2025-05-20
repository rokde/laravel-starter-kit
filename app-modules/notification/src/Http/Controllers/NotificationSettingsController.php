<?php

declare(strict_types=1);

namespace Modules\Notification\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Notification\Repositories\InAppNotificationsRepository;

class NotificationSettingsController
{
    public function index(Request $request, InAppNotificationsRepository $inAppNotificationsRepository): Response
    {
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
            'notifications' => $inAppNotificationsRepository->all()->groupBy('group'),
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
