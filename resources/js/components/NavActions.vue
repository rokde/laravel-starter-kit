<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Sidebar, SidebarContent, SidebarGroup, SidebarGroupContent, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { PageActionItemType } from '@/types';
import NotificationsPopover from '@notification/components/NotificationsPopover.vue';
import { MoreHorizontal } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    pageActions?: Array<PageActionItemType[]>;
}

const props = withDefaults(defineProps<Props>(), {
    pageActions: () => [],
});

const isOpen = ref(false);
</script>

<template>
    <div class="flex items-center gap-2 text-sm">
        <NotificationsPopover />
        <Popover v-if="props.pageActions.length" v-model:open="isOpen">
            <PopoverTrigger as-child>
                <Button variant="ghost" size="icon" class="data-[state=open]:bg-accent h-7 w-7">
                    <MoreHorizontal />
                </Button>
            </PopoverTrigger>
            <PopoverContent class="w-56 overflow-hidden rounded-lg p-0" align="end">
                <Sidebar collapsible="none" class="bg-transparent">
                    <SidebarContent>
                        <SidebarGroup v-for="(group, index) in props.pageActions" :key="index" class="border-b last:border-none">
                            <SidebarGroupContent class="gap-0">
                                <SidebarMenu>
                                    <SidebarMenuItem v-for="(item, index) in group" :key="index">
                                        <SidebarMenuButton @click="item.onClick" :disabled="item.disabled">
                                            <component :is="item.icon" /> <span>{{ item.label }}</span>
                                        </SidebarMenuButton>
                                    </SidebarMenuItem>
                                </SidebarMenu>
                            </SidebarGroupContent>
                        </SidebarGroup>
                    </SidebarContent>
                </Sidebar>
            </PopoverContent>
        </Popover>
    </div>
</template>
