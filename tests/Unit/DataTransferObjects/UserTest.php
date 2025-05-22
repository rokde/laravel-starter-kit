<?php

declare(strict_types=1);

use App\DataTransferObjects\User;

test('it can be instantiated with required properties', function (): void {
    $user = new User(
        id: 1,
        name: 'Test User',
        email: 'test@example.com',
        verified: true,
        locale: 'en'
    );

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->id)->toBe(1)
        ->and($user->name)->toBe('Test User')
        ->and($user->email)->toBe('test@example.com')
        ->and($user->verified)->toBeTrue()
        ->and($user->locale)->toBe('en');
});
