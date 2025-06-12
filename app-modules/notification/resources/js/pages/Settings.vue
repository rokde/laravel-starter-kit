<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Label } from '@/components/ui/label';
import { Separator } from '@/components/ui/separator';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

const { t } = getI18n();

interface Props {
    channels: {
        label: string;
        value: string;
    }[];
    notifications: {
        [key: string]: {
            class: string;
            description: string;
            group: string;
        }[];
    };
    preferred_notification_channels: {
        [key: string]: string[];
    };
}

const props = defineProps<Props>();

const formData = {};

Object.keys(props.notifications).forEach((group: string) => {
    props.notifications[group].forEach((notification: { class: string; description: string; group: string }) => {
        formData[notification.class] = (props.preferred_notification_channels as object).hasOwnProperty(notification.class)
            ? props.preferred_notification_channels[notification.class]
            : ['database'];
    });
});

const form = useForm(formData);

const setValue = (notificationClass: string, channel: string, value: boolean) => {
    let currentChannels = form[notificationClass] ?? [];
    if (value) {
        currentChannels.push(channel);
    } else {
        currentChannels = currentChannels.filter((c) => c !== channel);
    }
    form[notificationClass] = currentChannels;
};

const submit = () => {
    form.put(route('settings.notifications.update'), {
        preserveScroll: true,
    });
};

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: t('Notification settings'),
        href: route('settings.notifications.edit'),
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="$t('Notification settings')" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall :title="$t('Notification settings')" :description="$t('Update your account\'s notification settings')" />

                <form @submit.prevent="submit" class="space-y-6">
                    <div v-for="(notifications, group) of props.notifications" :key="group">
                        <h3 class="text-md font-bold">{{ $t(group as string) }}</h3>
                        <Separator class="my-6" />
                        <div
                            v-for="notification in notifications"
                            :key="notification.class"
                            class="mb-4 flex justify-between border-b pb-4 last:border-none"
                        >
                            <p>{{ $t(notification.description) }}</p>
                            <div class="flex flex-col gap-2">
                                <Label v-for="channel in props.channels" :key="channel.value" class="text-nowrap">
                                    <Checkbox
                                        :model-value="form[notification.class].includes(channel.value)"
                                        :value="channel.value"
                                        @update:modelValue="setValue(notification.class, channel.value, $event)"
                                    />
                                    {{ channel.label }}
                                </Label>
                                <Label class="text-nowrap">
                                    <Checkbox
                                        :model-value="form[notification.class].length === 0"
                                        :value="null"
                                        @update:modelValue="form[notification.class] = []"
                                    />
                                    {{ $t('Off') }}
                                </Label>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">{{ $t('Save') }}</Button>
                        <Transition
                            enter-active-class="transition ease-in-out"
                            enter-from-class="opacity-0"
                            leave-active-class="transition ease-in-out"
                            leave-to-class="opacity-0"
                        >
                            <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">{{ $t('Saved.') }}</p>
                        </Transition>
                    </div>
                </form>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
