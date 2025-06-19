<script setup lang="ts">
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import CustomPropertiesManager from '@customProperties/components/CustomPropertiesManager.vue';
import { Head } from '@inertiajs/vue3';
import WorkspaceSettingsLayout from '@workspace/layouts/WorkspaceSettingsLayout.vue';
import { Workspace } from '@workspace/types';

const { t } = getI18n();

interface Props {
    workspace: Workspace;
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
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="$t('Todo settings')" />

        <WorkspaceSettingsLayout>
            <CustomPropertiesManager :definable="{ id: props.workspace.id, type: 'Modules\\Workspace\\Models\\Workspace' }" />
        </WorkspaceSettingsLayout>
    </AppLayout>
</template>
