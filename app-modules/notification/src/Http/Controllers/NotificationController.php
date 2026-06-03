<?php

declare(strict_types=1);

namespace Modules\Notification\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
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
        /** @var DatabaseNotification $notification */
        $notification = $request->user()->notifications()->find($id);

        if (! $notification) {
            return back();
        }

        $notification->markAsRead();

        return back()->with('message', 'Notification marked as read.');
    }

    public function markAsUnread(Request $request, $id): RedirectResponse
    {
        /** @var DatabaseNotification $notification */
        $notification = $request->user()->notifications()->find($id);

        if (! $notification) {
            return back();
        }

        $notification->markAsUnread();

        return back()->with('message', 'Notification marked as unread.');
    }

    public function destroy(Request $request, $id): RedirectResponse
    {
        /** @var DatabaseNotification $notification */
        $notification = $request->user()->notifications()->find($id);

        if (! $notification) {
            return back();
        }

        $notification->delete();

        return back()->with('message', 'Notification deleted.');
    }
}
