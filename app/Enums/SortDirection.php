<?php

namespace App\Enums;

enum SortDirection: string
{
    case ASC = 'asc';
    case DESC = 'desc';

    public static function fromString(string $direction): SortDirection
    {
        return str_starts_with($direction, '-') ? SortDirection::DESC : SortDirection::ASC;
    }
}
