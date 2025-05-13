<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import { localeDate } from '@/lib/date-functions';
import { Invitation, Role } from '@workspace/types';

interface Props {
    invitations: Invitation[];
    roles: { [key: string]: Role };
}

const props = defineProps<Props>();
</script>

<template>
    <div v-for="invitation of props.invitations" :key="invitation.id" class="flex space-x-2">
        <UserInfo :user="invitation" show-email />
        <span class="text-muted-foreground" :title="props.roles[invitation.role]?.description">{{ props.roles[invitation.role]?.name }}</span>
        <span>{{ localeDate(invitation.created_at) }}</span>
    </div>
</template>
