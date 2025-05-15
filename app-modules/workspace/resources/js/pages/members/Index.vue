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
import { Role, Workspace } from '@workspace/types';
import MemberManager from './components/MemberManager.vue';

const { t } = getI18n();

interface Props {
    workspace: Workspace;
    owner: User;
    members: Array<User & { role: string }>;
    roles: { [key: string]: Role };
    abilities: {
        'members.update': boolean;
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
                    <MemberManager v-else :members="props.members" :roles="props.roles" :disabled="!props.abilities['members.update']" />
                </div>
            </div>

            <Separator />

            <InviteMemberForm :roles="props.roles" />
        </WorkspaceSettingsLayout>
    </AppLayout>
</template>
