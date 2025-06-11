<?php

declare(strict_types=1);

namespace App\Data\Facets;

enum FilterValueEnum: string
{
    case NULL = 'none';
    case TRUE = 'true';
    case FALSE = 'false';

    public static function transformFilterValue(string $value): mixed
    {
        return match ($value) {
            self::NULL->value => null,
            self::TRUE->value => true,
            self::FALSE->value => false,
            default => $value,
        };
    }
}
