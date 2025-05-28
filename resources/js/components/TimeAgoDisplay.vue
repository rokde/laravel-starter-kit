<script setup lang="ts">
import { getI18n } from '@/i18n';
import { localeDate, timeAgo } from '@/lib/date-functions';
import { computed } from 'vue';

const { t } = getI18n();

interface Props {
    date: string | Date | null;
    nullableLabel?: string;
}

const props = defineProps<Props>();

const valueLabel = computed<string>(() => {
    if (!props.date) {
        return '';
    }

    const ago = timeAgo(props.date);

    return t('{interval} {unit} ago', { interval: ago.interval, unit: t('units.' + ago.unit) });
});
</script>

<template>
    <time v-if="props.date" :datetime="localeDate(props.date, 'ISO8601')" :title="localeDate(props.date, 'datetime')">{{ valueLabel }}</time>
    <span v-else>{{ props.nullableLabel ?? '' }}</span>
</template>
