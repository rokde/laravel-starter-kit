<?php

declare(strict_types=1);

namespace Modules\Workspace\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Notification\Contracts\InAppNotification;
use Modules\Notification\Notifications\Concerns\SupportUserPreferredChannel;
use Modules\Workspace\DataTransferObjects\Workspace;

class WelcomeNewWorkspaceOwnerNotification extends Notification implements InAppNotification
{
    use Queueable, SupportUserPreferredChannel;

    public function __construct(public readonly Workspace $workspace) {}

    public static function getDescription(): string
    {
        return 'Get notified when you were made the workspace owner.';
    }

    public static function getGroup(): string
    {
        return 'Workspace';
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line($this->getTitle())
            ->action('See workspace settings', $this->getUrl())
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
            'workspace' => $this->workspace,
        ];
    }

    public function getUrl(): ?string
    {
        return route('workspaces.make-current', ['id' => $this->workspace->id, 'to' => route('workspaces.members.index')]);
    }

    public function getTitle(): string
    {
        return __('You were made to the owner of workspace :workspace.', [
            'workspace' => $this->workspace->name,
        ]);
    }
}
