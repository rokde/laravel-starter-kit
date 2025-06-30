<?php

declare(strict_types=1);

namespace Modules\Authorization\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class SetWorkspaceForPermissions
{
    /**
     * @param Request $request
     */
    public function handle($request, Closure $next)
    {
        if (config('permission.teams', false) && !empty($request->user())) {
            $workspaceId = $request->user()->workspace_id;
            setPermissionsTeamId($workspaceId);

            Role::addGlobalScope(function (Builder $query) use ($workspaceId) {
                $query->where(config('permission.column_names.team_foreign_key', 'workspace_id'), $workspaceId);
            });
        }

        return $next($request);
    }
}
