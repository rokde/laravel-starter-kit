<?php

declare(strict_types=1);

use App\DataTransferObjects\User as UserDto;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it can be instantiated using factory', function (): void {
    $user = User::factory()->unverified()->create();

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->id)->toBeInt()
        ->and($user->name)->toBeString()
        ->and($user->email)->toBeString()
        ->and($user->email_verified_at)->toBeNull();
});

test('it can be instantiated with verified email', function (): void {
    $user = User::factory()->create();

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->hasVerifiedEmail())->toBeTrue();
});

test('it returns preferred locale', function (): void {
    // User with specified locale
    $user = User::factory()->create(['locale' => 'fr']);
    expect($user->preferredLocale())->toBe('fr');

    // User with stringable locale
    $user = User::factory()->make();
    $user->locale = new \Illuminate\Support\Stringable('de');
    expect($user->preferredLocale())->toBe('de');
});

test('it can not set users locale to null', function (): void {
    $this->expectException(\Illuminate\Database\QueryException::class);
    // User with null locale (should use fallback)
    $user = User::factory()->create(['locale' => null]);
});

test('it can be converted to DTO', function (): void {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'locale' => 'es',
    ]);

    $dto = $user->toDto();

    expect($dto)->toBeInstanceOf(UserDto::class)
        ->and($dto->id)->toBe($user->id)
        ->and($dto->name)->toBe('Test User')
        ->and($dto->email)->toBe('test@example.com')
        ->and($dto->verified)->toBeTrue()
        ->and($dto->locale)->toBe('es');
});

test('it has the correct casts', function (): void {
    $user = User::factory()->create();

    expect($user->getAttributes())->toHaveKeys(['id', 'name', 'email', 'password', 'created_at', 'updated_at'])
        ->and($user->getCasts())->toHaveKeys(['email_verified_at', 'password', 'preferred_notification_channels']);
});
