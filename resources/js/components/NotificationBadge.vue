<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    label?: string | number;
    color?: 'blue' | 'red' | 'green';
}
const props = withDefaults(defineProps<Props>(), {
    color: 'red',
});

const colorVariants = {
    blue: 'bg-blue-50/10 text-blue-600',
    red: 'bg-red-50/10 text-red-600',
    green: 'bg-green-50/10 text-green-600',
};

const displayText = computed<string>(() => {
    if (typeof props.label === 'number') {
        if (props.label <= 0) return '';

        return props.label > 9 ? '9+' : props.label.toString(10);
    }

    return props.label;
});
</script>

<template>
    <div class="relative inline-flex">
        <slot />
        <div
            v-if="props.label"
            class="absolute -top-2 -right-2 size-4 overflow-hidden rounded-full text-center text-xs"
            :class="colorVariants[props.color]"
        >
            {{ displayText }}
        </div>
    </div>
</template>
