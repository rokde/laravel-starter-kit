<script setup lang="ts">
interface Props {
    type?: 'centered' | 'full-width';
}

const props = withDefaults(defineProps<Props>(), {
    type: 'centered',
});
</script>

<template>
    <div
        :class="{
            'mx-auto my-8 flex w-full max-w-5xl flex-col': props.type === 'centered',
            'flex h-full flex-1 flex-col gap-4 rounded-xl p-4': props.type === 'full-width',
        }"
    >
        <slot />
    </div>
</template>
