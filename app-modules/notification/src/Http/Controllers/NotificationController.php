<?php

declare(strict_types=1);

namespace Modules\Notification\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class NotificationController
{
    public function index(): Response
    {
        return Inertia::render('notification::Notifications');
    }

    public function markAsRead(Request $request, $id): RedirectResponse
    {
        /** @var \Illuminate\Notifications\DatabaseNotification $notification */
        $notification = $request->user()->notifications()->find($id);

        if (! $notification) {
            return redirect()->back();
        }

        $notification->markAsRead();

        return redirect()->back()->with('message', 'Notification marked as read.');
    }
}
