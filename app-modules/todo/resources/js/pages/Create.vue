<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/content/ContentLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import TodoForm from '@todo/components/TodoForm.vue';

const { t } = getI18n();

interface User {
    id: number;
    name: string;
    email: string;
}

interface Props {
    workspace: {
        id: number;
        name: string;
    };
    workspaceUsers: User[];
    presets: Array<{ value: string; label: string }>;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Todos'),
        href: '/todos',
    },
    {
        title: t('Create Todo'),
        href: '/todos/create',
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="$t('Create Todo')" />

        <ContentLayout type="form">
            <Heading :title="$t('Create Todo')" />

            <TodoForm :workspace-users="props.workspaceUsers" :presets="props.presets" />
        </ContentLayout>
    </AppLayout>
</template>
