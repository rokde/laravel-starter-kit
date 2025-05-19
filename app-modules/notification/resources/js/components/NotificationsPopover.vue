<script setup lang="ts">
import NotificationBadge from '@/components/NotificationBadge.vue';
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
import { Link, router } from '@inertiajs/vue3';
import { useNotifications } from '@notification/composables/useNotifications';
import { Notification } from '@notification/types';
import { Bell, BellOff } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const notifications = useNotifications();

const isOpen = ref(false);
const unreadNotifications = computed<number>(() => notifications.filter((n) => !n.read).length);

const openNotification = (notification: Notification) => {
    const url = notification.url ?? route('notifications.index');
    router.get(url);
};

const muteNotification = (notification: Notification) => {
    router.patch(
        route('notifications.mark-as-read', notification.id),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                isOpen.value = false;
                setTimeout(() => location.reload(), 10);
            },
        },
    );
};
</script>

<template>
    <Popover v-model:open="isOpen">
        <PopoverTrigger as-child>
            <Button variant="ghost" size="icon">
                <NotificationBadge :label="unreadNotifications">
                    <Bell :fill="unreadNotifications > 0 ? 'black' : 'white'" />
                </NotificationBadge>
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-80 overflow-hidden rounded-lg p-0" align="end">
            <Sidebar collapsible="none" class="bg-transparent">
                <SidebarContent v-if="notifications.length">
                    <SidebarGroup class="border-b last:border-none">
                        <SidebarGroupContent class="gap-0">
                            <SidebarMenu>
                                <SidebarMenuItem v-for="notification in notifications" :key="notification.id">
                                    <SidebarMenuButton @click="openNotification(notification)">
                                        <span :class="{ 'text-muted-foreground': notification.read }">{{ notification.title }}</span>
                                    </SidebarMenuButton>
                                    <SidebarMenuAction
                                        v-if="!notification.read"
                                        @click="muteNotification(notification)"
                                        :title="$t('Mute notification')"
                                    >
                                        <BellOff />
                                    </SidebarMenuAction>
                                </SidebarMenuItem>
                            </SidebarMenu>
                        </SidebarGroupContent>
                    </SidebarGroup>
                </SidebarContent>
                <SidebarFooter>
                    <SidebarMenu>
                        <SidebarMenuItem>
                            <SidebarMenuButton as-child>
                                <Link :href="route('notifications.index')">{{ $t('All notifications') }}</Link>
                            </SidebarMenuButton>
                        </SidebarMenuItem>
                    </SidebarMenu>
                </SidebarFooter>
            </Sidebar>
        </PopoverContent>
    </Popover>
</template>
