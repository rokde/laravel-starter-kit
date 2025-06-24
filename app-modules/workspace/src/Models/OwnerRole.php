<?php

declare(strict_types=1);

namespace Modules\Workspace\Models;

class OwnerRole extends Role
{
    public const ROLE_KEY = 'owner';

    public function __construct()
    {
        parent::__construct(self::ROLE_KEY, 'Owner', 'The owner of the workspace.');
    }
}
