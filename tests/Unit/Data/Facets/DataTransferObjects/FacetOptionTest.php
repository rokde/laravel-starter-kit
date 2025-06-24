<?php

declare(strict_types=1);

use App\Data\Facets\DataTransferObjects\FacetOption;

test('it can be instantiated with required properties', function (): void {
    $option = new FacetOption(
        label: 'Test Label',
        value: 'test_value',
        count: 5
    );

    expect($option)->toBeInstanceOf(FacetOption::class)
        ->and($option->label)->toBe('Test Label')
        ->and($option->value)->toBe('test_value')
        ->and($option->count)->toBe(5);
});

test('it can be instantiated with default count', function (): void {
    $option = new FacetOption(
        label: 'Test Label',
        value: 'test_value'
    );

    expect($option)->toBeInstanceOf(FacetOption::class)
        ->and($option->label)->toBe('Test Label')
        ->and($option->value)->toBe('test_value')
        ->and($option->count)->toBe(0);
});

test('it has readonly properties', function (): void {
    $reflection = new ReflectionClass(FacetOption::class);

    expect($reflection->isReadOnly())->toBeTrue();
});
