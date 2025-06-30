<?php

declare(strict_types=1);

namespace Modules\Workspace\Http\Controllers;

use App\Models\User;
use App\ValueObjects\Id;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Authorization\DataTransferObjects\Role as RoleDto;
use Modules\Workspace\Actions\RemoveMember;
use Modules\Workspace\Actions\TransferWorkspaceOwnership;
use Modules\Workspace\Actions\UpdateMember;
use Modules\Workspace\DataTransferObjects\Owner;
use Modules\Workspace\Models\OwnerRole;
use Spatie\Permission\Models\Role;

class WorkspaceMembersController
{
    public function index(Request $request): Response
    {
        /** @var User $user */
        $user = $request->user();

        /** @var \Modules\Workspace\Models\Workspace */
        $workspace = $user->currentWorkspace;
        abort_if($workspace === null, 404);

        $roles = Role::query()
            ->get()
            ->map(fn(Role $role): RoleDto => RoleDto::fromModel($role))
            ->values();

        return Inertia::render('workspace::members/Index', [
            'workspace' => $workspace->only('id', 'name'),
            'owner' => new Owner(
                id: $workspace->owner->id,
                name: $workspace->owner->name,
                email: $workspace->owner->email,
            ),
            'members' => $workspace->users,
            'roles' => $roles,
            'abilities' => [
                'workspace.transferOwnership' => $user->can('transferOwnership', $workspace),
                'members.create' => $user->can('addMember', $workspace),
                'members.update' => $user->can('updateMember', $workspace),
                'members.remove' => $user->can('removeMember', $workspace),
            ],
        ]);
    }

    public function update(
        Request $request,
        UpdateMember $updateMember,
        TransferWorkspaceOwnership $transferWorkspaceOwnership,
    ): RedirectResponse {
        /** @var \Modules\Workspace\Models\Workspace */
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);

        $roleKey = (string) $request->string('role');
        if ($roleKey === OwnerRole::ROLE_KEY) {
            $transferWorkspaceOwnership->handle($workspace, $request->user(), new Id($request->integer('id')));
        } else {
            $updateMember->handle($workspace, new Id($request->integer('id')), (string) $request->string('role'));
        }

        return redirect()
            ->back()
            ->with('message', __('Member updated.'));
    }

    public function destroy(
        Request $request,
        User $member,
        RemoveMember $removeMember,
    ): RedirectResponse {
        /** @var \Modules\Workspace\Models\Workspace */
        $workspace = $request->user()->currentWorkspace;
        abort_if($workspace === null, 404);

        $removeMember->handle($workspace, $member);

        return redirect()
            ->back()
            ->with('message', __('Member removed.'));
    }
}
