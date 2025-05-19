<script setup lang="ts">
import Breadcrumbs from '@/components/Breadcrumbs.vue';
import NavActions from '@/components/NavActions.vue';
import { SidebarTrigger } from '@/components/ui/sidebar';
import { BreadcrumbItemType, PageActionItemType } from '@/types';

withDefaults(
    defineProps<{
        breadcrumbs?: BreadcrumbItemType[];
        pageActions?: Array<PageActionItemType[]>;
    }>(),
    {
        breadcrumbs: () => [],
        pageActions: () => [],
    },
);
</script>

<template>
    <header
        class="border-sidebar-border/70 flex h-16 shrink-0 items-center gap-2 border-b px-6 transition-[width,height] ease-linear group-has-data-[collapsible=icon]/sidebar-wrapper:h-12 md:px-4"
    >
        <div class="flex items-center gap-2">
            <SidebarTrigger class="-ml-1" />
            <template v-if="breadcrumbs && breadcrumbs.length > 0">
                <Breadcrumbs :breadcrumbs="breadcrumbs" />
            </template>
        </div>
        <div class="ml-auto pl-3">
            <NavActions :page-actions="pageActions" />
        </div>
    </header>
</template>
