<script setup lang="ts">
import AppContent from '@/components/AppContent.vue';
import AppHeader from '@/components/AppHeader.vue';
import AppShell from '@/components/AppShell.vue';
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
    <AppShell class="flex-col">
        <AppHeader :breadcrumbs="breadcrumbs" :page-actions="pageActions" />
        <AppContent>
            <slot />
        </AppContent>
    </AppShell>
</template>
