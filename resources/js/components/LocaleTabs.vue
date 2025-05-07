<script setup lang="ts">
import { Check, Globe } from 'lucide-vue-next';

interface Props {
    currentLocale: string;
    locales: string[];
}

const props = defineProps<Props>();

const emits = defineEmits<{
    (e: 'selected', locale: string): void;
}>();
</script>

<template>
    <div class="inline-flex gap-1 rounded-lg bg-neutral-100 p-1 dark:bg-neutral-800">
        <button
            v-for="locale in props.locales"
            :key="locale"
            @click="emits('selected', locale)"
            :class="[
                'flex items-center rounded-md px-3.5 py-1.5 transition-colors',
                props.currentLocale === locale
                    ? 'bg-white shadow-xs dark:bg-neutral-700 dark:text-neutral-100'
                    : 'text-neutral-500 hover:bg-neutral-200/60 hover:text-black dark:text-neutral-400 dark:hover:bg-neutral-700/60',
            ]"
        >
            <component :is="locale === props.currentLocale ? Check : Globe" class="-ml-1 size-4" />
            <span class="ml-1.5 text-sm">{{ $t('locales.' + locale) }}</span>
        </button>
    </div>
</template>
