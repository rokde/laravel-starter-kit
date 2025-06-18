<?php

namespace Modules\CustomProperties\Models;

enum CustomPropertyType: string
{
    case TEXT = 'text';
    case NUMBER = 'number';
    case DATE = 'date';
    case BOOLEAN = 'boolean';
    case SELECT = 'select';
}
