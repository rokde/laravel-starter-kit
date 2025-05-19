<?php

namespace Modules\Notification\Notifications\Concerns;

use App\Models\User;
use Illuminate\Notifications\AnonymousNotifiable;
use Modules\Notification\Contracts\InAppNotification;

trait SupportUserPreferredChannel
{
    /**
     * Get the notification's delivery channels.
     *
     * @param \Illuminate\Notifications\AnonymousNotifiable|\App\Models\User $notifiable
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        if ($notifiable instanceof AnonymousNotifiable) {
            return ['mail'];
        }

        // always add 'database' as first notification channel
        $preferredNotificationChannel = ['database'];
        if ($notifiable instanceof User) {
//            if ($notifiable->preferred_notification_channels && $notifiable->preferred_notification_channels !== 'database') {
//                $preferredNotificationChannel[] = $notifiable->preferred_notification_channels;
//            }
        }

        return $preferredNotificationChannel;
    }

    /**
     * Get the databse representation of the notification.
     *
     * @param \Illuminate\Notifications\AnonymousNotifiable|\App\Models\User $notifiable
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
