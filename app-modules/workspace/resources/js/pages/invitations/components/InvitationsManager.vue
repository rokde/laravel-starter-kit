<script setup lang="ts">
import ConfirmButton from '@/components/ConfirmButton.vue';
import CopyToClipboardButton from '@/components/CopyToClipboardButton.vue';
import TimeAgoDisplay from '@/components/TimeAgoDisplay.vue';
import { router } from '@inertiajs/vue3';
import { Invitation, Role } from '@workspace/types';

interface Props {
    invitations: Invitation[];
    roles: { [key: string]: Role };
    revoke: boolean;
}

const props = defineProps<Props>();

const revokeInvitation = (invitation: Invitation) => {
    router.delete(route('workspaces.invitations.revoke', invitation.id), {
        preserveScroll: true,
        onSuccess: () => {
            router.reload();
        },
    });
};
</script>

<template>
    <div v-for="invitation of props.invitations" :key="invitation.id" class="flex items-center space-x-2">
        <div class="flex flex-1 items-center gap-2 text-left leading-tight">
            <span class="truncate">{{ invitation.email }}</span>
            <span class="text-muted-foreground text-sm" :title="props.roles[invitation.role]?.description"
                >({{ $t(`roles.${props.roles[invitation.role]?.key}.name`) }})</span
            >
        </div>
        <TimeAgoDisplay :date="invitation.created_at" />
        <CopyToClipboardButton v-if="invitation.link" :text="invitation.link" :title="$t('Copy invitation link to clipboard')" />
        <ConfirmButton
            as="icon"
            :title="$t('Revoke invitation?')"
            :confirmation="$t('If you revoke the invitation, the user will not be able to access the workspace.')"
            @confirmed="revokeInvitation(invitation)"
            :disabled="!props.revoke"
        />
    </div>
</template>
