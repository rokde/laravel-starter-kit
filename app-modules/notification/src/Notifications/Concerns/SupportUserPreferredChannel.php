<?php

declare(strict_types=1);

namespace Modules\Notification\Notifications\Concerns;

use App\Models\User;
use Illuminate\Notifications\AnonymousNotifiable;
use Modules\Notification\Contracts\InAppNotification;

trait SupportUserPreferredChannel
{
    /**
     * Get the notification's delivery channels.
     *
     * @param  AnonymousNotifiable|User  $notifiable
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if ($notifiable instanceof AnonymousNotifiable) {
            return ['mail'];
        }

        if ($notifiable instanceof User) {
            return $notifiable->preferred_notification_channels[static::class] ?? ['database'];
        }

        return ['database'];
    }

    /**
     * Get the databse representation of the notification.
     *
     * @param  AnonymousNotifiable|User  $notifiable
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $data = $this->toArray($notifiable);

        if ($this instanceof InAppNotification) {
            $data['url'] = $this->getUrl();
            $data['title'] = $this->getTitle();
        }

        return $data;
    }
}
