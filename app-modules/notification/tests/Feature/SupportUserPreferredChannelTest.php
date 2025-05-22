<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\AnonymousNotifiable;
use Modules\Notification\Contracts\InAppNotification;
use Modules\Notification\Notifications\Concerns\SupportUserPreferredChannel;

uses(RefreshDatabase::class);

test('via method returns mail for anonymous notifiable', function (): void {
    $notification = new class
    {
        use SupportUserPreferredChannel;
    };

    $notifiable = new AnonymousNotifiable();

    expect($notification->via($notifiable))->toBe(['mail']);
});

test('via method returns database by default for user', function (): void {
    $notification = new class
    {
        use SupportUserPreferredChannel;
    };

    $user = new User();

    expect($notification->via($user))->toBe(['database']);
});

test('via method returns user preferred channels when set', function (): void {
    $notificationClass = new class
    {
        use SupportUserPreferredChannel;
    };

    $className = get_class($notificationClass);

    $user = User::factory()->create([
        'preferred_notification_channels' => [
            $className => ['mail', 'database'],
        ],
    ]);

    expect($notificationClass->via($user))->toBe(['mail', 'database']);
});

test('toDatabase method adds url and title for InAppNotification', function (): void {
    $notification = new class implements InAppNotification
    {
        use SupportUserPreferredChannel;

        public static function getDescription(): string
        {
            return 'Test Description';
        }

        public static function getGroup(): string
        {
            return 'Test Group';
        }

        public function toArray(object $notifiable): array
        {
            return ['data' => 'test'];
        }

        public function getUrl(): ?string
        {
            return 'https://example.com';
        }

        public function getTitle(): string
        {
            return 'Test Title';
        }
    };

    $user = User::factory()->create();

    $result = $notification->toDatabase($user);

    expect($result)->toBe([
        'data' => 'test',
        'url' => 'https://example.com',
        'title' => 'Test Title',
    ]);
});

test('toDatabase method does not add url and title for non-InAppNotification', function (): void {
    $notification = new class
    {
        use SupportUserPreferredChannel;

        public function toArray(object $notifiable): array
        {
            return ['data' => 'test'];
        }
    };

    $user = User::factory()->create();

    $result = $notification->toDatabase($user);

    expect($result)->toBe(['data' => 'test']);
});
