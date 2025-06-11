<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { localeDate } from '@/lib/date-functions';
import { cn } from '@/lib/utils';
import { CalendarDate, parseDate } from '@internationalized/date';
import { CalendarIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';

interface Props {
    modelValue: string | null;
    min?: string | null;
    max?: string | null;
    placeholder?: string;
    label?: string;
    presets?: { value: string; label: string }[];
}

const props = withDefaults(defineProps<Props>(), {
    min: null,
    max: null,
    placeholder: 'Pick a date',
    presets: () => [],
});

const emits = defineEmits<{
    (e: 'update:modelValue', date: string | undefined): void;
}>();

const value = computed<CalendarDate | undefined>({
    get: () => (props.modelValue ? parseDate(props.modelValue) : undefined),
    set: (val) => val,
});

const calendarPlaceholder = ref();

const minValue = computed<CalendarDate | undefined>(() => (props.min ? parseDate(props.min) : undefined));
const maxValue = computed<CalendarDate | undefined>(() => (props.max ? parseDate(props.max) : undefined));

const currentLocale = window.locale;
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button variant="outline" :class="cn('w-[240px] ps-3 text-start font-normal', !value && 'text-muted-foreground')">
                <span>{{ value ? localeDate(value) : props.placeholder ? $t(props.placeholder) : '' }}</span>
                <CalendarIcon class="ms-auto h-4 w-4 opacity-50" />
            </Button>
            <input hidden />
        </PopoverTrigger>
        <PopoverContent :class="{ 'w-auto p-0': props.presets.length <= 0, 'flex w-auto flex-col gap-y-2 p-2': props.presets.length > 0 }">
            <Select v-if="props.presets.length > 0" :model-value="value?.toString()" @update:model-value="emits('update:modelValue', $event)">
                <SelectTrigger class="w-full">
                    <SelectValue :placeholder="$t('Presets')" class="p-2" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="preset in props.presets" :key="preset.value" :value="preset.value">
                        {{ preset.label }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <Calendar
                v-model:placeholder="calendarPlaceholder"
                :model-value="value"
                :calendar-label="props.label"
                initial-focus
                :min-value="minValue"
                :max-value="maxValue"
                :locale="currentLocale"
                @update:model-value="
                    (v) => {
                        if (v) {
                            emits('update:modelValue', v.toString());
                        } else {
                            emits('update:modelValue', undefined);
                        }
                    }
                "
            />
        </PopoverContent>
    </Popover>
</template>
