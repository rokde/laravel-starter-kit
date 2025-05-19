<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import { localeDate } from '@/lib/date-functions';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { useNotifications } from '@notification/composables/useNotifications';
import { Notification } from '@notification/types';
import { Eye } from 'lucide-vue-next';

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
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="$t('Notifications')" />

        <div class="px-4 py-6">
            <Heading :title="$t('Notifications')" />

            <div v-if="notifications.length" v-for="notification in notifications" :key="notification.id" class="mb-4 flex items-center gap-4">
                <time :datetime="notification.created_at">{{ localeDate(notification.created_at, 'datetime') }}</time>
                <Link v-if="notification.url" :href="notification.url">{{ notification.title }}</Link>
                <span v-else :class="{ 'text-muted-foreground': notification.read }">{{ notification.title }}</span>
                <Button
                    v-if="!notification.read"
                    type="button"
                    variant="outline"
                    @click="markAsRead(notification)"
                    :title="$t('Mark notification as read')"
                >
                    <Eye />
                    {{ $t('Mark notification as read') }}
                </Button>
            </div>
            <div v-else>
                {{ $t('No notifications yet.') }}
            </div>
        </div>
    </AppLayout>
</template>
