<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import UserInfo from '@/components/UserInfo.vue';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, User } from '@/types';
import { Head } from '@inertiajs/vue3';
import WorkspaceSettingsLayout from '@workspace/layouts/WorkspaceSettingsLayout.vue';
import InviteMemberForm from '@workspace/pages/members/components/InviteMemberForm.vue';
import { Role, Workspace, WorkspaceOwner } from '@workspace/types';
import MemberManager from './components/MemberManager.vue';

const { t } = getI18n();

interface Props {
    workspace: Workspace;
    owner: WorkspaceOwner;
    members: Array<User & { role: string }>;
    roles: { [key: string]: Role };
    ownerRoleKey: string;
    abilities: {
        'members.create': boolean;
        'members.update': boolean;
        'members.remove': boolean;
    };
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
        title: t('Members'),
        href: '/workspace/current/members',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="$t('Workspace members')" />

        <WorkspaceSettingsLayout>
            <HeadingSmall :title="props.workspace.name" :description="$t('All members of the workspace.')" />

            <div class="space-y-6">
                <div class="grid gap-2">
                    <Label>{{ $t('Owner') }}</Label>

                    <div class="flex space-x-2">
                        <UserInfo :user="props.owner" show-email />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label>{{ $t('Members') }}</Label>

                    <div v-if="!props.members.length">
                        {{ $t('No members yet.') }}
                    </div>
                    <MemberManager
                        v-else
                        :members="props.members"
                        :roles="props.roles"
                        :remove="props.abilities['members.remove']"
                        :readonly="!props.abilities['members.update']"
                        :owner-role-key="props.ownerRoleKey"
                    />
                </div>
            </div>

            <template v-if="props.abilities['members.create']">
                <Separator />

                <InviteMemberForm :roles="props.roles" />
            </template>
        </WorkspaceSettingsLayout>
    </AppLayout>
</template>
