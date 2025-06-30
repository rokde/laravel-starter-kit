<?php

declare(strict_types=1);

namespace Modules\Authorization\Enums;

enum PermissionActions: string
{
    case VIEW = 'view';
    case EDIT = 'edit';
    case DELETE = 'delete';
}
