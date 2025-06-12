<?php

declare(strict_types=1);

namespace Modules\Todo\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Notification\Contracts\InAppNotification;
use Modules\Notification\Notifications\Concerns\SupportUserPreferredChannel;
use Modules\Todo\Models\Todo;

class TodoIsDueTodayNotification extends Notification implements InAppNotification
{
    use Queueable, SupportUserPreferredChannel;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly Todo $todo)
    {
        //
    }

    public static function getDescription(): string
    {
        return 'Get notified when a todo is due today.';
    }

    public static function getGroup(): string
    {
        return 'Todo';
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('Your task is due today')
            ->action('Open your due todos', $this->getUrl())
            ->line('')
            ->line('You got this email because you have enabled notifications for this type of event.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'todo' => $this->todo,
        ];
    }

    public function getUrl(): ?string
    {
        return route('workspaces.make-current', ['id' => $this->todo->workspace_id, 'to' => route('todos.index', ['filter[due_date]' => 'past'])]);
    }

    public function getTitle(): string
    {
        return __('The todo :todo is due today', [
            'todo' => $this->todo->title,
        ]);
    }
}
