<?php

declare(strict_types=1);

namespace Modules\Workspace\Models;

class OwnerRole extends Role
{
    public function __construct()
    {
        parent::__construct('owner', 'Owner', 'The owner of the workspace.');
    }
}
