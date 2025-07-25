<?php

declare(strict_types=1);

namespace Modules\Notification\Repositories;

use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Modules\Notification\Contracts\NotificationRepository as NotificationRepositoryContract;
use Modules\Notification\DataTransferObjects\Notification;

class NotificationRepository implements NotificationRepositoryContract
{
    public function __construct(private readonly ?User $user) {}

    public function all(): Collection
    {
        if (! $this->user instanceof User) {
            return collect();
        }

        return $this->user->notifications
            ->map(function (DatabaseNotification $notification): Notification {
                $data = $notification->data;

                return new Notification(
                    id: $notification->id,
                    type: $notification->type,
                    group: $notification->type::getGroup(),
                    title: $data['title'] ?? '',
                    url: $data['url'] ?? null,
                    data: Arr::except($data, ['url', 'title']),
                    read: $notification->read(),
                    created_at: $notification->created_at->inUserTimezone()->toDateTimeString(),
                );
            });
    }
}
