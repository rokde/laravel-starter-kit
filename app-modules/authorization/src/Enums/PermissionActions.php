<?php

declare(strict_types=1);

namespace Modules\Authorization\Enums;

enum PermissionActions: string
{
    case ADD = 'add';
    case CREATE = 'create';
    case DELETE = 'delete';
    case EDIT = 'edit';
    case EXECUTE = 'execute';
    case UPDATE = 'update';
    case VIEW = 'view';
}
