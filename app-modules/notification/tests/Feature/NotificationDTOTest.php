<?php

declare(strict_types=1);

use Modules\Notification\DataTransferObjects\Notification;

test('notification DTO can be instantiated with correct properties', function (): void {
    $notification = new Notification(
        id: 'test-id',
        type: 'test-type',
        group: 'test-group',
        title: 'Test Title',
        url: 'https://example.com',
        data: ['key' => 'value'],
        read: true,
        created_at: '2023-01-01 12:00:00',
    );

    expect($notification)->toBeInstanceOf(Notification::class)
        ->and($notification->id)->toBe('test-id')
        ->and($notification->type)->toBe('test-type')
        ->and($notification->title)->toBe('Test Title')
        ->and($notification->url)->toBe('https://example.com')
        ->and($notification->data)->toBe(['key' => 'value'])
        ->and($notification->read)->toBeTrue()
        ->and($notification->created_at)->toBe('2023-01-01 12:00:00');
});

test('notification DTO can be instantiated with null url', function (): void {
    $notification = new Notification(
        id: 'test-id',
        type: 'test-type',
        group: 'test-group',
        title: 'Test Title',
        url: null,
        data: ['key' => 'value'],
        read: false,
        created_at: '2023-01-01 12:00:00',
    );

    expect($notification)->toBeInstanceOf(Notification::class)
        ->and($notification->url)->toBeNull();
});

test('notification DTO is readonly', function (): void {
    $notification = new Notification(
        id: 'test-id',
        type: 'test-type',
        group: 'test-group',
        title: 'Test Title',
        url: 'https://example.com',
        data: ['key' => 'value'],
        read: true,
        created_at: '2023-01-01 12:00:00',
    );

    // Verify the class is marked as readonly
    $reflectionClass = new ReflectionClass($notification);
    expect($reflectionClass->isReadOnly())->toBeTrue();
});
