<?php

declare(strict_types=1);

namespace Modules\Workspace\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class Membership extends Pivot
{
    /** @uses HasFactory<\Database\Factories\MembershipFactory> */
    use HasFactory;

    public $table = 'workspace_members';

    public $incrementing = true;
}
