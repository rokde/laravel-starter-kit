<script setup lang="ts">
defineProps<{
    description?: string;
}>();
</script>

<template>
    <div v-show="description">
        <p class="text-muted-foreground text-sm">
            {{ description }}
        </p>
    </div>
</template>
