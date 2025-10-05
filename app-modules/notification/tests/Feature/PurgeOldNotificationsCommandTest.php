<?php

declare(strict_types=1);

use Illuminate\Console\Command;
use Illuminate\Notifications\DatabaseNotification;
use Modules\Notification\Console\Commands\PurgeOldNotificationsCommand;
use Modules\Notification\Database\Factories\DatabaseNotificationFactory;

test('purge old notifications command can be instantiated', function (): void {
    $command = new PurgeOldNotificationsCommand();

    expect($command)->toBeInstanceOf(PurgeOldNotificationsCommand::class)
        ->and($command)->toBeInstanceOf(Command::class);
});

test('purge old notifications command deletes read notifications older than specified age', function (): void {
    // Create old read notification
    $oldReadNotification = DatabaseNotificationFactory::new()->create([
        'created_at' => now()->subDays(70),
        'read_at' => now()->subDays(65),
    ]);

    // Create recent read notification
    $recentReadNotification = DatabaseNotificationFactory::new()->create([
        'created_at' => now()->subDays(30),
        'read_at' => now()->subDays(25),
    ]);

    // Create old unread notification
    $oldUnreadNotification = DatabaseNotificationFactory::new()->create([
        'created_at' => now()->subDays(70),
        'read_at' => null,
    ]);

    // Run command with default age (60 days)
    $this->artisan('notifications:purge')
        ->expectsOutput('Purging all read notifications older than 60 days...')
        ->assertSuccessful();

    // Assert old read notification was deleted
    expect(DatabaseNotification::query()->find($oldReadNotification->id))->toBeNull();

    // Assert recent read notification was not deleted
    expect(DatabaseNotification::query()->find($recentReadNotification->id))->not->toBeNull();

    // Assert old unread notification was not deleted
    expect(DatabaseNotification::query()->find($oldUnreadNotification->id))->not->toBeNull();
});

test('purge old notifications command deletes all notifications older than specified age when include-unread option is used', function (): void {
    // Create old read notification
    $oldReadNotification = DatabaseNotificationFactory::new()->create([
        'created_at' => now()->subDays(70),
        'read_at' => now()->subDays(65),
    ]);

    // Create old unread notification
    $oldUnreadNotification = DatabaseNotificationFactory::new()->create([
        'created_at' => now()->subDays(70),
        'read_at' => null,
    ]);

    // Create recent unread notification
    $recentUnreadNotification = DatabaseNotificationFactory::new()->create([
        'created_at' => now()->subDays(30),
        'read_at' => null,
    ]);

    // Run command with include-unread option
    $this->artisan('notifications:purge --include-unread')
        ->expectsOutput('Purging all read notifications older than 60 days...')
        ->assertSuccessful();

    // Assert old read notification was deleted
    expect(DatabaseNotification::query()->find($oldReadNotification->id))->toBeNull();

    // Assert old unread notification was deleted
    expect(DatabaseNotification::query()->find($oldUnreadNotification->id))->toBeNull();

    // Assert recent unread notification was not deleted
    expect(DatabaseNotification::query()->find($recentUnreadNotification->id))->not->toBeNull();
});

test('purge old notifications command respects custom age parameter', function (): void {
    // Create notification older than 30 days
    $olderThan30Days = DatabaseNotificationFactory::new()->create([
        'created_at' => now()->subDays(40),
        'read_at' => now()->subDays(35),
    ]);

    // Create notification newer than 30 days
    $newerThan30Days = DatabaseNotificationFactory::new()->create([
        'created_at' => now()->subDays(20),
        'read_at' => now()->subDays(15),
    ]);

    // Run command with custom age (30 days)
    $this->artisan('notifications:purge --age=30')
        ->expectsOutput('Purging all read notifications older than 30 days...')
        ->assertSuccessful();

    // Assert notification older than 30 days was deleted
    expect(DatabaseNotification::query()->find($olderThan30Days->id))->toBeNull();

    // Assert notification newer than 30 days was not deleted
    expect(DatabaseNotification::query()->find($newerThan30Days->id))->not->toBeNull();
});

uses(Illuminate\Foundation\Testing\RefreshDatabase::class);
