<?php

declare(strict_types=1);

use Illuminate\Support\Collection;
use Modules\Notification\Contracts\InAppNotification;
use Modules\Notification\Repositories\InAppNotificationsRepository;

class TestNotification implements InAppNotification
{
    public static function getDescription(): string
    {
        return 'Test Description';
    }

    public static function getGroup(): string
    {
        return 'Test Group';
    }

    public function getUrl(): ?string
    {
        return 'https://example.com';
    }

    public function getTitle(): string
    {
        return 'Test Title';
    }
}

test('all method returns collection of notification classes from config', function (): void {
    // Create a mock notification class that implements InAppNotification
    $mockNotificationClass = new TestNotification();

    $mockNotificationClassName = $mockNotificationClass::class;

    // Mock the config
    config(['notification.notifications' => [
        $mockNotificationClassName,
        'TestNotification',
    ]]);

    $repository = new InAppNotificationsRepository();

    $result = $repository->all();

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result)->toHaveCount(2)
        ->and($result[0])->toBe([
            'class' => $mockNotificationClassName,
            'group' => 'Test Group',
            'description' => 'Test Description',
        ])
        ->and($result[1]['class'])->toBe('TestNotification')
        ->and($result[1]['group'])->toBe('Test Group');
});

test('all method returns empty collection when no notification classes are configured', function (): void {
    // Mock the config with empty array
    config(['notification.notifications' => []]);

    $repository = new InAppNotificationsRepository();

    $result = $repository->all();

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result)->toBeEmpty();
});
