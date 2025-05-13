<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, User } from '@/types';
import { Head } from '@inertiajs/vue3';
import WorkspaceSettingsLayout from '@workspace/layouts/WorkspaceSettingsLayout.vue';
import InvitationsManager from '@workspace/pages/invitations/components/InvitationsManager.vue';
import InviteMemberForm from '@workspace/pages/members/components/InviteMemberForm.vue';
import { Invitation, Role, Workspace } from '@workspace/types';

const { t } = getI18n();

interface Props {
    workspace: Workspace;
    owner: User;
    invitations: Invitation[];
    roles: { [key: string]: Role };
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
        title: t('Invitations'),
        href: '/workspace/current/invitations',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="$t('Workspace invitations')" />

        <WorkspaceSettingsLayout>
            <HeadingSmall :title="props.workspace.name" :description="$t('All invitations of the workspace.')" />

            <div class="space-y-6">
                <div class="grid gap-2">
                    <Label>{{ $t('Invitations') }}</Label>

                    <div v-if="!props.invitations.length">
                        {{ $t('No invitations yet.') }}
                    </div>
                    <InvitationsManager v-else :invitations="props.invitations" :roles="props.roles" />
                </div>
            </div>

            <Separator />

            <InviteMemberForm :roles="props.roles" />
        </WorkspaceSettingsLayout>
    </AppLayout>
</template>
