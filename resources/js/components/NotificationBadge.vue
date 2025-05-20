<script setup lang="ts">
import { computed } from 'vue';

interface Props {
    label?: string | number;
    title?: string;
    color?: 'blue' | 'red' | 'green';
    type?: 'label' | 'pulse';
    maxNumber?: number;
}
const props = withDefaults(defineProps<Props>(), {
    color: 'red',
    maxNumber: 9,
    type: 'label',
});

const colorVariants = {
    blue: 'bg-blue-50/10 text-blue-600',
    red: 'bg-red-50/10 text-red-600',
    green: 'bg-green-50/10 text-green-600',
};
const pulseColorVariantsRing = {
    blue: 'bg-blue-400',
    red: 'bg-red-400',
    green: 'bg-green-400',
};
const pulseColorVariantsDot = {
    blue: 'bg-blue-500',
    red: 'bg-red-500',
    green: 'bg-green-500',
};

const displayText = computed<string>(() => {
    if (typeof props.label === 'number') {
        if (props.label <= 0) return '';

        return props.label > props.maxNumber ? props.maxNumber.toString(10) + '+' : props.label.toString(10);
    }

    return props.label;
});
</script>

<template>
    <span class="relative inline-flex" :title="props.title">
        <slot />
        <div
            v-if="props.type === 'label' && props.label"
            class="absolute -top-2 -right-2 size-4 overflow-hidden rounded-full text-center text-xs"
            :class="colorVariants[props.color]"
        >
            {{ displayText }}
        </div>
        <span v-if="props.type === 'pulse'" class="absolute -top-1 -right-1 -mt-1 -mr-1 flex size-3">
            <span class="absolute inline-flex h-full w-full animate-ping rounded-full opacity-75" :class="pulseColorVariantsRing[props.color]"></span
            ><span class="relative inline-flex size-3 rounded-full" :class="pulseColorVariantsDot[props.color]"></span>
        </span>
    </span>
</template>
