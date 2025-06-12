<?php

declare(strict_types=1);

namespace Modules\Todo\Notifications;

use App\DataTransferObjects\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Notification\Contracts\InAppNotification;
use Modules\Notification\Notifications\Concerns\SupportUserPreferredChannel;
use Modules\Todo\Models\Todo;

class TodoAssignedNotification extends Notification implements InAppNotification
{
    use Queueable, SupportUserPreferredChannel;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly Todo $todo, public readonly User $user)
    {
        //
    }

    public static function getDescription(): string
    {
        return 'Get notified when someone assigns you a todo.';
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
            ->subject('A user assigned you a task')
            ->line("{$this->user->name} assigned you the task {$this->todo->title}.")
            ->action('Open your todos', $this->getUrl())
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
            'user' => $this->user,
        ];
    }

    public function getUrl(): ?string
    {
        return route('workspaces.make-current', ['id' => $this->todo->workspace_id, 'to' => route('todos.index')]);
    }

    public function getTitle(): string
    {
        return __(':user has assigned you the task :task', [
            'task' => $this->todo->title,
            'user' => $this->user->name,
        ]);
    }
}
