<?php

declare(strict_types=1);

namespace Modules\Workspace\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Workspace\Models\RoleRegistry;

class WorkspaceMembersController
{
    public function index(Request $request): Response
    {
        /** @var \Modules\Workspace\Models\Workspace */
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);

        return Inertia::render('workspace::members/Index', [
            'workspace' => $workspace->only('id', 'name'),
            'owner' => $workspace->owner,
            'members' => $workspace->users,
            'roles' => RoleRegistry::$roles,
        ]);
    }
}
