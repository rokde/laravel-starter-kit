<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Calendar } from '@/components/ui/calendar';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { getI18n } from '@/i18n';
import { localeDate } from '@/lib/date-functions';
import { cn } from '@/lib/utils';
import { CalendarDate, parseDate } from '@internationalized/date';
import { CalendarIcon } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const { t } = getI18n();

interface Props {
    modelValue: string | null;
    min?: string | null;
    max?: string | null;
    placeholder?: string;
}

const props = withDefaults(defineProps<Props>(), {
    min: null,
    max: null,
    placeholder: t('Pick a date'),
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
</script>

<template>
    <Popover>
        <PopoverTrigger as-child>
            <Button variant="outline" :class="cn('w-[240px] ps-3 text-start font-normal', !value && 'text-muted-foreground')">
                <span>{{ value ? localeDate(value) : props.placeholder }}</span>
                <CalendarIcon class="ms-auto h-4 w-4 opacity-50" />
            </Button>
            <input hidden />
        </PopoverTrigger>
        <PopoverContent class="w-auto p-0">
            <Calendar
                v-model:placeholder="calendarPlaceholder"
                :model-value="value"
                calendar-label="Date of birth"
                initial-focus
                :min-value="minValue"
                :max-value="maxValue"
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
