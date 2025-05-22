<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Notification\Repositories\InAppNotificationsRepository;

uses(RefreshDatabase::class);

test('index method renders the settings page with correct data', function (): void {
    $user = User::factory()->create();

    // Mock the InAppNotificationsRepository
    $mockRepository = Mockery::mock(InAppNotificationsRepository::class);
    $mockRepository->shouldReceive('all')->once()->andReturn(collect([
        ['class' => 'TestNotification', 'group' => 'Test', 'description' => 'Test Notification']
    ]));

    $this->app->instance(InAppNotificationsRepository::class, $mockRepository);

    $response = $this->actingAs($user)
        ->get('/settings/notifications');

    $response->assertStatus(200);
});

test('update method updates user notification preferences', function (): void {
    $user = User::factory()->create([
        'preferred_notification_channels' => [],
    ]);

    $preferences = [
        'TestNotification' => ['database', 'mail'],
    ];

    $response = $this->actingAs($user)
        ->put('/settings/notifications', $preferences);

    $response->assertRedirect()
        ->assertSessionHas('message', 'Settings saved.');

    $this->assertEquals($preferences, $user->fresh()->preferred_notification_channels->toArray());
});

afterEach(function (): void {
    Mockery::close();
});
