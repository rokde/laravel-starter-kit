<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use Modules\Notification\Database\Factories\DatabaseNotificationFactory;
use Modules\Notification\DataTransferObjects\Notification;
use Modules\Notification\Repositories\NotificationRepository;

uses(RefreshDatabase::class);

test('all method returns empty collection when user is null', function (): void {
    $repository = new NotificationRepository(null);

    $result = $repository->all();

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result)->toBeEmpty();
});

test('all method returns collection of notification DTOs', function (): void {
    $user = User::factory()->create();

    // Create notifications for the user
    $notification1 = DatabaseNotificationFactory::new()->create([
        'notifiable_type' => $user::class,
        'notifiable_id' => $user->id,
        'data' => [
            'title' => 'Test Notification 1',
            'url' => 'https://example.com/1',
            'extra' => 'Extra data 1',
        ],
        'read_at' => null,
    ]);

    $notification2 = DatabaseNotificationFactory::new()->create([
        'notifiable_type' => $user::class,
        'notifiable_id' => $user->id,
        'data' => [
            'title' => 'Test Notification 2',
            'url' => 'https://example.com/2',
            'extra' => 'Extra data 2',
        ],
        'read_at' => now(),
    ]);

    $repository = new NotificationRepository($user);

    $result = $repository->all();

    expect($result)->toBeInstanceOf(Collection::class)
        ->and($result)->toHaveCount(2)
        ->and($result->first())->toBeInstanceOf(Notification::class)
        ->and($result->first()->id)->toBe($notification1->id)
        ->and($result->first()->title)->toBe('Test Notification 1')
        ->and($result->first()->url)->toBe('https://example.com/1')
        ->and($result->first()->data)->toBe(['extra' => 'Extra data 1'])
        ->and($result->first()->read)->toBeFalse()
        ->and($result->last()->id)->toBe($notification2->id)
        ->and($result->last()->title)->toBe('Test Notification 2')
        ->and($result->last()->url)->toBe('https://example.com/2')
        ->and($result->last()->data)->toBe(['extra' => 'Extra data 2'])
        ->and($result->last()->read)->toBeTrue();
});
