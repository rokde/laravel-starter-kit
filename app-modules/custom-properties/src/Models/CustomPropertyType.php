<?php

declare(strict_types=1);

namespace Modules\CustomProperties\Models;

use Illuminate\Support\Arr;

enum CustomPropertyType: string
{
    case TEXT = 'text';
    case NUMBER = 'number';
    case COLOR = 'color';
    case DATE = 'date';
    case BOOLEAN = 'boolean';
    case SELECT = 'select';

    public function defaultRules(): array
    {
        return match ($this) {
            self::TEXT => ['string'],
            self::NUMBER => ['numeric'],
            self::COLOR => ['hex_color'],
            self::DATE => ['date'],
            self::BOOLEAN => ['boolean'],
            self::SELECT => ['string'],
            default => [],
        };
    }

    public function resolvePropertyOptions(?array $propertyOptions): ?array
    {
        return match ($this) {
            self::NUMBER => Arr::only($propertyOptions, ['decimal_places', 'suffix']),
            self::SELECT => Arr::only($propertyOptions, 'sort'),
            default => null,
        };
    }

    public function resolveDisplayOptions(?array $displayOptions): ?array
    {
        return match ($this) {
            self::NUMBER => Arr::only($displayOptions, ['display']),
            default => null,
        };
    }
}
