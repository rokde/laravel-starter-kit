<script setup lang="ts">
import { localeDate } from '@/lib/date-functions';
import { Invitation, Role } from '@workspace/types';

interface Props {
    invitations: Invitation[];
    roles: { [key: string]: Role };
}

const props = defineProps<Props>();
</script>

<template>
    <div v-for="invitation of props.invitations" :key="invitation.id" class="flex items-center space-x-2">
        <div class="flex flex-1 items-center gap-2 text-left leading-tight">
            <span class="truncate">{{ invitation.email }}</span>
            <span class="text-muted-foreground text-sm" :title="props.roles[invitation.role]?.description"
                >({{ props.roles[invitation.role]?.name }})</span
            >
        </div>
        <span>{{ localeDate(invitation.created_at) }}</span>
    </div>
</template>
