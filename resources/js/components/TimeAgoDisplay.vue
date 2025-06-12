<script setup lang="ts">
import { getI18n } from '@/i18n';
import { humanReadable, localeDate } from '@/lib/date-functions';
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

    const ago = humanReadable(props.date);

    if (ago.direction === 'now') {
        return t('now');
    }

    if (ago.direction === 'from now') {
        return t('{interval} {unit} from now', {
            interval: ago.interval,
            unit: t('units.' + ago.unit),
        });
    }

    return t('{interval} {unit} ago', {
        interval: ago.interval,
        unit: t('units.' + ago.unit),
    });
});
</script>

<template>
    <time v-if="props.date" :datetime="localeDate(props.date, 'ISO8601')" :title="localeDate(props.date, 'datetime')">{{ valueLabel }}</time>
    <span v-else>{{ props.nullableLabel ?? '' }}</span>
</template>
