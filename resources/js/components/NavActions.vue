<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import {
    Sidebar,
    SidebarContent,
    SidebarFooter,
    SidebarGroup,
    SidebarGroupContent,
    SidebarMenu,
    SidebarMenuAction,
    SidebarMenuButton,
    SidebarMenuItem,
} from '@/components/ui/sidebar';
import { PageActionItemType } from '@/types';
import { Bell, BellOff, MoreHorizontal } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    pageActions?: Array<PageActionItemType[]>;
}

const props = withDefaults(defineProps<Props>(), {
    pageActions: () => [],
});

const isOpen = ref(false);
const notificationsOpen = ref(false);

const openNotification = () => {
    console.log('open');
};

const muteNotification = () => {
    console.log('mute');
};
</script>

<template>
    <div class="flex items-center gap-2 text-sm">
        <Popover v-model:open="notificationsOpen">
            <PopoverTrigger as-child>
                <Button variant="ghost" size="icon">
                    <Bell />
                </Button>
            </PopoverTrigger>
            <PopoverContent class="w-56 overflow-hidden rounded-lg p-0" align="end">
                <Sidebar collapsible="none" class="bg-transparent">
                    <SidebarContent>
                        <SidebarGroup class="border-b">
                            <SidebarGroupContent class="gap-0">
                                <SidebarMenu>
                                    <SidebarMenuItem>
                                        <SidebarMenuButton @click="openNotification">
                                            <span class=""> sd </span>
                                        </SidebarMenuButton>
                                        <SidebarMenuAction @click="muteNotification"><BellOff /></SidebarMenuAction>
                                    </SidebarMenuItem>
                                </SidebarMenu>
                            </SidebarGroupContent>
                        </SidebarGroup>
                    </SidebarContent>
                    <SidebarFooter>
                        <SidebarMenu>
                            <SidebarMenuItem>
                                <SidebarMenuButton>
                                    <span>All notifications</span>
                                </SidebarMenuButton>
                            </SidebarMenuItem>
                        </SidebarMenu>
                    </SidebarFooter>
                </Sidebar>
            </PopoverContent>
        </Popover>
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
