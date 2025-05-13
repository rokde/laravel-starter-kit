<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import type { User } from '@/types';

interface Props {
    members: Array<User & { role: string }>;
}

const props = defineProps<Props>();
</script>

<template>
    <div v-for="member of props.members" :key="member.id" class="flex space-x-2">
        <UserInfo :user="member" show-email />
        <span class="text-muted-foreground">{{ member.role }}</span>
    </div>
</template>
