<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import TimeAgoDisplay from '@/components/TimeAgoDisplay.vue';
import { Button } from '@/components/ui/button';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { useNotifications } from '@notification/composables/useNotifications';
import { Notification } from '@notification/types';
import { ArrowRight, Check, Mail } from 'lucide-vue-next';

const { t } = getI18n();

const notifications = useNotifications();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: t('Notifications'),
        href: route('notifications.index'),
    },
];

const markAsRead = (notification: Notification) => {
    router.patch(
        route('notifications.mark-as-read', notification.id),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                setTimeout(() => location.reload(), 10);
            },
        },
    );
};
const markAsUnread = (notification: Notification) => {
    router.patch(
        route('notifications.mark-as-unread', notification.id),
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                setTimeout(() => location.reload(), 10);
            },
        },
    );
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="$t('Notifications')" />

        <div class="mx-auto my-8 flex w-full max-w-5xl flex-col">
            <Heading :title="$t('Notifications')" />

            <div class="flex flex-col gap-y-4">
                <div class="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-32">
                                    {{ $t('Date') }}
                                </TableHead>
                                <TableHead class="w-fit">
                                    {{ $t('Message') }}
                                </TableHead>
                                <TableHead class="w-8">
                                    <span class="sr-only">{{ $t('Actions') }}</span>
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="notification in notifications" :key="notification.id">
                                <TableCell>
                                    <TimeAgoDisplay :date="notification.created_at" :class="{ 'text-muted-foreground': notification.read }" />
                                </TableCell>
                                <TableCell>
                                    <Link v-if="notification.url" :href="notification.url" :class="{ 'text-muted-foreground': notification.read }">{{
                                        notification.title
                                    }}</Link>
                                    <span v-else :class="{ 'text-muted-foreground': notification.read }">{{ notification.title }}</span>
                                </TableCell>
                                <TableCell class="text-right">
                                    <Button v-if="notification.url" type="button" variant="ghost" :title="$t('Follow the link')" as-child>
                                        <Link :href="notification.url"><ArrowRight /></Link>
                                    </Button>
                                    <Button
                                        v-if="!notification.read"
                                        type="button"
                                        variant="ghost"
                                        @click="markAsRead(notification)"
                                        :title="$t('Mark notification as read')"
                                    >
                                        <Check />
                                    </Button>
                                    <Button
                                        v-if="notification.read"
                                        type="button"
                                        variant="ghost"
                                        @click="markAsUnread(notification)"
                                        :title="$t('Mark notification as unread')"
                                    >
                                        <Mail />
                                    </Button>
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="notifications.length <= 0">
                                <TableCell colspan="3">
                                    {{ $t('No notifications yet.') }}
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
