<?php

declare(strict_types=1);

namespace Modules\Workspace\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Workspace\Actions\CreateWorkspace;
use Modules\Workspace\Actions\SetCurrentWorkspace;
use Modules\Workspace\Actions\UpdateWorkspaceName;
use Modules\Workspace\DataTransferObjects\Owner;
use Modules\Workspace\Http\Requests\ModifyWorkspaceRequest;
use Modules\Workspace\Http\Requests\StoreWorkspaceRequest;
use Modules\Workspace\Http\Requests\SwitchWorkspaceRequest;

class WorkspaceController
{
    public function create(Request $request): Response
    {
        return Inertia::render('workspace::Create', [
            'owner' => new Owner(
                id: $request->user()->id,
                name: $request->user()->name,
                email: $request->user()->email,
            ),
        ]);
    }

    public function store(
        StoreWorkspaceRequest $request,
        CreateWorkspace $createWorkspace,
        SetCurrentWorkspace $setCurrentWorkspace,
    ): RedirectResponse {
        $workspaceId = $createWorkspace->handle($request->userId(), $request->validated('name'));

        $setCurrentWorkspace->handle($request->userId(), $workspaceId);

        return redirect()
            ->route('workspaces.show')
            ->with('message', __('workspace::Workspace created.'));
    }

    public function show(Request $request): Response
    {
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);

        $owner = $workspace->owner;

        return Inertia::render('workspace::Profile', [
            'workspace' => $workspace->only('id', 'name'),
            'owner' => $owner,
            'abilities' => [
                'workspace.update' => $request->user()->can('update', $workspace),
            ],
        ]);
    }

    public function update(
        ModifyWorkspaceRequest $request,
        UpdateWorkspaceName $updateWorkspaceName,
    ): RedirectResponse {
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);

        $updateWorkspaceName->handle($workspace, $request->validated('name'));

        return redirect()
            ->back()
            ->with('message', __('workspace::Workspace updated.'));
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
