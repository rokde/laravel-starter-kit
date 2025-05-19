<script setup lang="ts">
import AppLayout from '@/layouts/app/AppSidebarLayout.vue';
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
    <AppLayout :breadcrumbs="breadcrumbs" :page-actions="pageActions">
        <slot />
    </AppLayout>
</template>
