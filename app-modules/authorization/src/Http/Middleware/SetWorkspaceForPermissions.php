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
            setPermissionsTeamId($request->user()->workspace_id);

            Role::addGlobalScope(function (Builder $query) use ($request): void {
                $query->where(config('permission.column_names.team_foreign_key', 'workspace_id'), $request->user()->workspace_id);
            });
        }

        return $next($request);
    }
}
