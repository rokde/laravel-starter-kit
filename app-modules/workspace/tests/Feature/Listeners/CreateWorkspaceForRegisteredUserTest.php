<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it creates a workspace when user is registered', function (): void {
    $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::query()->where('email', 'test@example.com')->first();

    $this->assertNotNull($user->workspace_id);
    $this->assertDatabaseHas('workspaces', [
        'name' => 'My workspace',
        'user_id' => $user->id,
    ]);

    $this->assertCount(1, $user->ownedWorkspaces);
});
