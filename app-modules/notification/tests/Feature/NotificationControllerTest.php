<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Notification\Database\Factories\DatabaseNotificationFactory;

uses(RefreshDatabase::class);

test('index method renders the notifications page', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get('/notifications');

    $response->assertStatus(200);
});

test('markAsRead method marks a notification as read', function (): void {
    $user = User::factory()->create();

    $notification = DatabaseNotificationFactory::new()->create([
        'notifiable_type' => get_class($user),
        'notifiable_id' => $user->id,
        'read_at' => null,
    ]);

    $response = $this->actingAs($user)
        ->patch("/notifications/{$notification->id}");

    $response->assertRedirect()
        ->assertSessionHas('message', 'Notification marked as read.');

    $this->assertNotNull($notification->fresh()->read_at);
});

test('markAsRead method handles non-existent notification', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/notifications/non-existent-id/mark-as-read');

    $response->assertNotFound();
    $response->assertSessionMissing('message');
});
