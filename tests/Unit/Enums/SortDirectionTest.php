<?php

declare(strict_types=1);

use App\Enums\SortDirection;

test('enum has expected cases with correct values', function (): void {
    // Test ASC case
    expect(SortDirection::ASC->value)->toBe('asc');

    // Test DESC case
    expect(SortDirection::DESC->value)->toBe('desc');
});

test('fromString returns ASC when string does not start with hyphen', function (): void {
    // Test with regular string
    expect(SortDirection::fromString('name'))->toBe(SortDirection::ASC);

    // Test with empty string
    expect(SortDirection::fromString(''))->toBe(SortDirection::ASC);

    // Test with string that contains hyphen but doesn't start with it
    expect(SortDirection::fromString('name-field'))->toBe(SortDirection::ASC);
});

test('fromString returns DESC when string starts with hyphen', function (): void {
    // Test with hyphen prefix
    expect(SortDirection::fromString('-name'))->toBe(SortDirection::DESC);

    // Test with just a hyphen
    expect(SortDirection::fromString('-'))->toBe(SortDirection::DESC);
});
