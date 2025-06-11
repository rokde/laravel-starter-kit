<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import LocaleTabs from '@/components/LocaleTabs.vue';
import { Select, SelectContent, SelectGroup, SelectItem, SelectLabel, SelectSeparator, SelectTrigger, SelectValue } from '@/components/ui/select';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';

const { t } = getI18n();

interface Props {
    locale: string;
    locales: string[];
    timezone: string;
    timezones: {
        [key: string]: {
            value: string;
            label: string;
        };
    };
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
    timezone: props.timezone,
});

const submit = () => {
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
                <LocaleTabs
                    :locales="props.locales"
                    :currentLocale="props.locale"
                    @selected="
                        ($event) => {
                            form.locale = $event;
                            submit();
                        }
                    "
                />
            </div>
            <div class="space-y-6">
                <Label for="timezone">{{ $t('Timezone') }}</Label>
                <Select
                    id="timezone"
                    :model-value="form.timezone"
                    @update:model-value="
                        (v: string) => {
                            form.timezone = v;
                            submit();
                        }
                    "
                >
                    <SelectTrigger>
                        <SelectValue :placeholder="$t('Timezone')" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectGroup>
                            <SelectLabel>Generic</SelectLabel>
                            <SelectItem value="UTC"> UTC </SelectItem>
                        </SelectGroup>
                        <SelectSeparator />

                        <template v-for="(timezones, group) of props.timezones" :key="group">
                            <SelectGroup>
                                <SelectLabel>{{ group }}</SelectLabel>
                                <SelectItem v-for="timezone in timezones" :key="timezone.value" :value="timezone.value">
                                    {{ timezone.label }}
                                </SelectItem>
                            </SelectGroup>
                            <SelectSeparator />
                        </template>
                    </SelectContent>
                </Select>
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
