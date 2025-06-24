<?php

declare(strict_types=1);

use App\Data\Facets\FilterValueEnum;

test('enum has expected cases with correct values', function (): void {
    // Test NULL case
    expect(FilterValueEnum::NULL->value)->toBe('none');

    // Test TRUE case
    expect(FilterValueEnum::TRUE->value)->toBe('true');

    // Test FALSE case
    expect(FilterValueEnum::FALSE->value)->toBe('false');
});

test('transformFilterValue transforms "none" to null', function (): void {
    $result = FilterValueEnum::transformFilterValue('none');

    expect($result)->toBeNull();
});

test('transformFilterValue transforms "true" to boolean true', function (): void {
    $result = FilterValueEnum::transformFilterValue('true');

    expect($result)->toBeTrue();
});

test('transformFilterValue transforms "false" to boolean false', function (): void {
    $result = FilterValueEnum::transformFilterValue('false');

    expect($result)->toBeFalse();
});

test('transformFilterValue returns other values unchanged', function (): void {
    $testValues = [
        'test' => 'test',
        '123' => '123',
        'some value' => 'some value',
    ];

    foreach ($testValues as $input => $expected) {
        $result = FilterValueEnum::transformFilterValue((string)$input);
        expect($result)->toBe($expected);
    }
});
