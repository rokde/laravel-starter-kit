<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppShell from '@/components/AppShell.vue';
import AppSidebar from '@/components/AppSidebar.vue';
import AppSidebarHeader from '@/components/AppSidebarHeader.vue';
import type { BreadcrumbItemType, PageActionItemType } from '@/types';

interface Props {
    breadcrumbs?: BreadcrumbItemType[];
    pageActions?: Array<PageActionItemType[]>;
}

withDefaults(defineProps<Props>(), {
    breadcrumbs: () => [],
    pageActions: () => [],
});
</script>

<template>
    <AppShell variant="sidebar">
        <AppSidebar />
        <AppContent variant="sidebar">
            <AppSidebarHeader :breadcrumbs="breadcrumbs" :page-actions="pageActions" />
            <slot />
        </AppContent>
    </AppShell>
</template>
