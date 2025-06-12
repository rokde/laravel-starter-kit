<?php

declare(strict_types=1);

namespace Modules\Workspace\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Modules\Notification\Contracts\InAppNotification;
use Modules\Notification\Notifications\Concerns\SupportUserPreferredChannel;
use Modules\Workspace\Models\WorkspaceInvitation;

class GotWorkspaceInvitationNotification extends Notification implements InAppNotification
{
    use Queueable, SupportUserPreferredChannel;

    /**
     * Create a new notification instance.
     */
    public function __construct(public readonly WorkspaceInvitation $invitation, public readonly string $workspace)
    {
        //
    }

    public static function getDescription(): string
    {
        return 'Get notified when you got an invitation to another workspace.';
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
            ->line('You were invited to workspace '.$this->workspace.' as '.$this->invitation->role.'.')
            ->action('Accept invitation', $this->getUrl())
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
            'invitation' => $this->invitation,
            'workspace' => $this->workspace,
        ];
    }

    public function getUrl(): ?string
    {
        return $this->invitation->getAcceptUrl();
    }

    public function getTitle(): string
    {
        return __("You've got an invitation to work as :role in :workspace", [
            'role' => $this->invitation->role,
            'workspace' => $this->workspace,
        ]);
    }
}
