<?php

declare(strict_types=1);

namespace Modules\Workspace\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Modules\Workspace\Actions\CreateWorkspace;
use Modules\Workspace\Actions\SetCurrentWorkspace;
use Modules\Workspace\Http\Requests\StoreWorkspaceRequest;
use Modules\Workspace\Http\Requests\SwitchWorkspaceRequest;

class WorkspaceController
{
    public function store(
        StoreWorkspaceRequest $request,
        CreateWorkspace $createWorkspace,
        SetCurrentWorkspace $setCurrentWorkspace,
    ): RedirectResponse {
        $workspaceId = $createWorkspace->handle($request->userId(), $request->validated('name'));

        $setCurrentWorkspace->handle($request->userId(), $workspaceId);

        return redirect()
            ->back()
            ->with('message', __('workspace::Workspace created.'));
    }

    public function setCurrent(
        SwitchWorkspaceRequest $request,
        SetCurrentWorkspace $setCurrentWorkspace,
    ): RedirectResponse {
        $setCurrentWorkspace->handle($request->userId(), $request->workspaceId());

        return redirect()
            ->back()
            ->with('message', __('workspace::Current workspace changed.'));
    }
}
