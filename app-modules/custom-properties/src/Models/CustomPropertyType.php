<?php

declare(strict_types=1);

namespace Modules\CustomProperties\Models;

enum CustomPropertyType: string
{
    case TEXT = 'text';
    case NUMBER = 'number';
    case COLOR = 'color';
    case DATE = 'date';
    case BOOLEAN = 'boolean';
    case SELECT = 'select';
}
