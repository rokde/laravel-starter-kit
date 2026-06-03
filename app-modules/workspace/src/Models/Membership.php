<?php

declare(strict_types=1);

namespace Modules\Workspace\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Override;

class Membership extends Pivot
{
    /** @uses HasFactory<\Database\Factories\MembershipFactory> */
    use HasFactory;

    #[Override]
    public $table = 'workspace_members';

    #[Override]
    public $incrementing = true;
}
