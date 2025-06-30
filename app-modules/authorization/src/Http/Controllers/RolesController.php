<?php

declare(strict_types=1);

namespace Modules\Authorization\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;
use Modules\Authorization\DataTransferObjects\Role as RoleDto;
use Modules\Authorization\Permissions\PermissionRegistry;
use Spatie\Permission\Exceptions\RoleAlreadyExists;
use Spatie\Permission\Models\Role;

class RolesController
{
    public function index(Request $request, PermissionRegistry $permissionRegistry): Response
    {
        /** @var User $user */
        $user = $request->user();

        /** @var \Modules\Workspace\Models\Workspace */
        $workspace = $user->currentWorkspace;
        abort_if($workspace === null, 404);

        $roles = Role::query()
            ->with('permissions')
            ->where('workspace_id', $workspace->id)
            ->get(['id', 'name'])
            ->map(fn(Role $role) => ['id' => $role['id'], 'name' => $role['name'], 'permissions' => $role->permissions->pluck('name')->toArray()])
            ->values();

        return Inertia::render('authorization::Roles', [
            'workspace' => $workspace->only('id', 'name'),
            'roles' => $roles,
            'permissions' => $permissionRegistry->permissions(),
        ]);
    }

    public function create(Request $request, PermissionRegistry $permissionRegistry): Response
    {
        /** @var User $user */
        $user = $request->user();

        /** @var \Modules\Workspace\Models\Workspace */
        $workspace = $user->currentWorkspace;
        abort_if($workspace === null, 404);

        return Inertia::render('authorization::CreateRole', [
            'workspace' => $workspace->only('id', 'name'),
            'permissions' => $permissionRegistry->permissions()
                ->sortBy('resource')
                ->groupBy('resource'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        /** @var \Modules\Workspace\Models\Workspace */
        $workspace = $user->currentWorkspace;
        abort_if($workspace === null, 404);

        try {
            $role = Role::create([
                'workspace_id' => $workspace->id,
                'name' => $request->input('name'),
            ]);

            $role->givePermissionTo($request->input('permissions'));
        } catch (RoleAlreadyExists) {
            throw ValidationException::withMessages(['name' => 'Role already exists.']);
        }

        return redirect()->route('workspaces.roles.index')
            ->with('success', 'Role created successfully.');
    }

    public function edit(Request $request, Role $role, PermissionRegistry $permissionRegistry): Response
    {
        /** @var User $user */
        $user = $request->user();

        /** @var \Modules\Workspace\Models\Workspace */
        $workspace = $user->currentWorkspace;
        abort_if($workspace === null, 404);

        abort_unless($role->workspace_id === $workspace->id, 403);

        return Inertia::render('authorization::UpdateRole', [
            'workspace' => $workspace->only('id', 'name'),
            'role' => RoleDto::fromModel($role),
            'permissions' => $permissionRegistry->permissions()
                ->sortBy('resource')
                ->groupBy('resource'),
        ]);
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        /** @var \Modules\Workspace\Models\Workspace */
        $workspace = $user->currentWorkspace;
        abort_if($workspace === null, 404);

        abort_unless($role->workspace_id === $workspace->id, 403);

        $role->update(['name' => $request->input('name')]);

        $role->syncPermissions($request->input('permissions'));

        return redirect()->route('workspaces.roles.index')
            ->with('success', 'Role updated successfully.');
    }

    public function destroy(Request $request, Role $role): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        // dd($user->can('roles.delete'));

        /** @var \Modules\Workspace\Models\Workspace */
        $workspace = $user->currentWorkspace;
        abort_if($workspace === null, 404);

        abort_unless($role->workspace_id === $workspace->id, 403);

        $role->delete();

        return redirect()->route('workspaces.roles.index')
            ->with('success', 'Role deleted successfully.');
    }
}
