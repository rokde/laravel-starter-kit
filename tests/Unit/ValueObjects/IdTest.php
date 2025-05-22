<?php

declare(strict_types=1);

use App\ValueObjects\Id;

test('it can be instantiated with an integer', function (): void {
    $id = new Id(123);

    expect($id)->toBeInstanceOf(Id::class)
        ->and($id->value())->toBe(123);
});

test('it can be instantiated with a string', function (): void {
    $id = new Id('456');

    expect($id)->toBeInstanceOf(Id::class)
        ->and($id->value())->toBe(456);
});

test('it throws an exception when instantiated with null', function (): void {
    expect(fn () => new Id(null))->toThrow(InvalidArgumentException::class, 'Id cannot be null');
});

test('it can be converted to a string', function (): void {
    $id = new Id(789);

    expect((string) $id)->toBe('789');
});

test('it can be serialized to JSON', function (): void {
    $id = new Id(123);

    $json = json_encode($id);

    expect($json)->toBe('{"id":123}');
});

test('it can be serialized and unserialized', function (): void {
    $id = new Id(123);

    $serialized = serialize($id);
    $unserialized = unserialize($serialized);

    expect($unserialized)->toBeInstanceOf(Id::class)
        ->and($unserialized->value())->toBe(123);
});

test('it can be serialized and unserialized using __serialize and __unserialize', function (): void {
    $id = new Id(123);

    $serialized = serialize($id);
    $unserialized = unserialize($serialized);

    expect($unserialized)->toBeInstanceOf(Id::class)
        ->and($unserialized->value())->toBe(123);
});
