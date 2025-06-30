<?php

namespace Modules\Authorization\Enums;

enum PermissionActions: string
{
    case VIEW = 'view';
    case EDIT = 'edit';
    case DELETE = 'delete';
}
