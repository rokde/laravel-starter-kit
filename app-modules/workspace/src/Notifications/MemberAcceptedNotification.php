<?php

declare(strict_types=1);

namespace Modules\Workspace\Notifications;

use App\DataTransferObjects\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Notification\Contracts\InAppNotification;
use Modules\Notification\Notifications\Concerns\SupportUserPreferredChannel;
use Modules\Workspace\DataTransferObjects\Workspace;

class MemberAcceptedNotification extends Notification implements InAppNotification
{
    use Queueable, SupportUserPreferredChannel;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly Workspace $workspace, public readonly User $member) {}

    public static function getDescription(): string
    {
        return 'Get notified when a member accepts the invitation to any of your workspaces.';
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
            ->subject('Member accepted the invitation to the workspace')
            ->line("{$this->member->name} accepted the invitation to the workspace {$this->workspace->name}.}")
            ->action('Open your members overview', $this->getUrl())
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
            'member' => $this->member,
        ];
    }

    public function getUrl(): ?string
    {
        return route('workspaces.make-current', ['id' => $this->workspace->id, 'to' => route('workspaces.members.index')]);
    }

    public function getTitle(): string
    {
        return __(':member accepted the invitation to the workspace :workspace.', [
            'member' => $this->member->name,
            'workspace' => $this->workspace->name,
        ]);
    }
}
