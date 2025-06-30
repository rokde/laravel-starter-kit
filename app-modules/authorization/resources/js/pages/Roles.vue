<script setup lang="ts">
import ConfirmButton from '@/components/ConfirmButton.vue';
import DataTable from '@/components/DataTable/DataTable.vue';
import { ITableOptions } from '@/components/DataTable/types';
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Permission, Role } from '@authorization/types';
import { Head, Link, router } from '@inertiajs/vue3';
import WorkspaceSettingsLayout from '@workspace/layouts/WorkspaceSettingsLayout.vue';
import { Workspace } from '@workspace/types';
import { Edit } from 'lucide-vue-next';

const { t } = getI18n();

interface Props {
    workspace: Workspace;
    roles: Role[];
    permissions: Permission[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Workspace settings'),
        href: '/workspaces/current',
    },
    {
        title: props.workspace.name,
        href: '/workspaces/current',
    },
    {
        title: t('Roles'),
        href: '/workspaces/current/roles',
    },
];

const tableOptions: ITableOptions<Role> = {
    key: 'id',
    withRowActions: true,
    columns: [
        {
            key: 'name',
            label: t('Name'),
        },
        {
            key: 'permissions',
            label: t('Permissions'),
        },
    ],
};

const getPermissionById = (id: string): Permission | undefined => {
    return props.permissions.filter((p) => p.id === id)[0];
};

const deleteRole = (role: Role) => {
    router.delete(route('workspaces.roles.destroy', { role: role.id }));
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="$t('Role settings')" />

        <WorkspaceSettingsLayout>
            <HeadingSmall
                :title="$t('Roles')"
                :description="
                    $t(
                        'These roles exist within the current workspace. You can assign permissions to roles to control access to the workspace. Roles can be assigned to users to grant them access to the workspace.',
                    )
                "
            />

            <DataTable :rows="props.roles" :options="tableOptions">
                <template #primaryAction>
                    <Link :href="route('workspaces.roles.create')">
                        <Button>{{ $t('Create role') }}</Button>
                    </Link>
                </template>

                <template #permissions="{ row: role }">
                    <div v-for="permission of role.permissions" :key="permission" class="flex w-full space-x-2">
                        <kbd class="text-muted-foreground">{{ permission }}</kbd>
                        <p>{{ getPermissionById(permission)?.description }}</p>
                    </div>
                </template>

                <template #rowActions="{ row: role }">
                    <Link as="button" :href="route('workspaces.roles.edit', { role: role.id })">
                        <Edit class="size-4" />
                    </Link>
                    <ConfirmButton
                        as="icon"
                        :title="$t('Delete role')"
                        :confirmation="
                            $t(
                                'Are you sure you want to delete this role? This can not be undone. All users with this role will lose access to the appropriate permissions.',
                            )
                        "
                        @confirmed="deleteRole(role)"
                    />
                </template>
            </DataTable>
        </WorkspaceSettingsLayout>
    </AppLayout>
</template>
