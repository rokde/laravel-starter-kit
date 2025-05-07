<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

import LocaleTabs from '@/components/LocaleTabs.vue';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';

const { t } = getI18n();

interface Props {
    locale: string;
    locales: string[];
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: t('Locale settings'),
        href: '/settings/locale',
    },
];

const form = useForm({
    locale: props.locale,
});

const submit = (locale: string) => {
    form.locale = locale;
    form.patch(route('locale.update'), {
        preserveScroll: true,
        onSuccess: () => {
            setTimeout(() => location.reload(), 1000);
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="$t('Locale settings')" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall :title="$t('Locale settings')" :description="$t('Update your account\'s locale settings')" />
                <LocaleTabs :locales="props.locales" :currentLocale="props.locale" @selected="submit" />
            </div>
            <div>
                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">{{ $t('Saved.') }}</p>
                </Transition>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
