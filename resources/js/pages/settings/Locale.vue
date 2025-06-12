<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import LocaleTabs from '@/components/LocaleTabs.vue';
import { Button } from '@/components/ui/button';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList } from '@/components/ui/command';
import { Label } from '@/components/ui/label';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { cn } from '@/lib/utils';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Check, ChevronsUpDown } from 'lucide-vue-next';
import { ref } from 'vue';

const { t } = getI18n();

type Timezone = {
    value: string;
    label: string;
    offset: string;
};
type ContinentTimezones = {
    [continent: string]: Timezone[];
};

interface Props {
    locale: string;
    locales: string[];
    timezone: string;
    timezones: ContinentTimezones;
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
            setTimeout(() => location.reload(), 10);
        },
    });
};

const open = ref(false);

const timezoneLabel = (key: string): string => {
    for (const continent in props.timezones) {
        if (Object.prototype.hasOwnProperty.call(props.timezones, continent)) {
            const timezones = props.timezones[continent];
            for (const timezone of timezones) {
                if (timezone.value === key) {
                    return `${timezone.label} (${continent})`;
                }
            }
        }
    }
    return key;
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="$t('Locale settings')" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall :title="$t('Locale settings')" :description="$t('Update your account\'s locale settings')" />
                <Label>{{ $t('Language') }}</Label>
                <LocaleTabs v-model="form.locale" :locales="props.locales" />
            </div>
            <div class="space-y-6">
                <Label for="timezone">{{ $t('Timezone') }}</Label>
                <Popover v-model:open="open">
                    <PopoverTrigger as-child>
                        <Button variant="outline" role="combobox" :aria-expanded="open" class="w-[200px] justify-between">
                            {{ form.timezone ? timezoneLabel(form.timezone) : $t('Search timezone...') }}

                            <ChevronsUpDown class="ml-2 h-4 w-4 shrink-0 opacity-50" />
                        </Button>
                    </PopoverTrigger>
                    <PopoverContent class="w-[300px] p-0">
                        <Command v-model="form.timezone">
                            <CommandInput :placeholder="$t('Search timezone...')" />
                            <CommandEmpty>{{ $t('No timezone found.') }}</CommandEmpty>
                            <CommandList>
                                <template v-for="(timezones, continent) of props.timezones" :key="continent">
                                    <CommandGroup :heading="continent as string">
                                        <CommandItem
                                            v-for="timezone in timezones"
                                            :key="timezone.value"
                                            :value="timezone.value"
                                            @select="open = false"
                                        >
                                            <Check :class="cn('mr-2 h-4 w-4', form.timezone === timezone.value ? 'opacity-100' : 'opacity-0')" />
                                            <div class="flex w-full justify-between">
                                                {{ timezone.label }}
                                                <span class="text-muted-foreground">({{ timezone.offset }})</span>
                                            </div>
                                        </CommandItem>
                                    </CommandGroup>
                                </template>
                            </CommandList>
                        </Command>
                    </PopoverContent>
                </Popover>
            </div>
            <div class="space-y-6">
                <div class="flex items-center gap-4">
                    <Button type="button" @click="submit" :disabled="!form.isDirty || form.processing">{{ $t('Save') }}</Button>

                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">{{ $t('Saved.') }}</p>
                    </Transition>
                </div>
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
