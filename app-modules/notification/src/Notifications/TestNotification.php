<?php

declare(strict_types=1);

namespace Modules\Notification\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Notification\Contracts\InAppNotification;
use Modules\Notification\Notifications\Concerns\SupportUserPreferredChannel;

class TestNotification extends Notification implements InAppNotification
{
    use Queueable, SupportUserPreferredChannel;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    public static function getDescription(): string
    {
        return 'This is a test notification.';
    }

    public static function getGroup(): string
    {
        return 'Test';
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }

    public function getUrl(): ?string
    {
        return route('dashboard');
    }

    public function getTitle(): string
    {
        return __('This is a test translation: :word.', ['word' => fake()->word]);
    }
}
