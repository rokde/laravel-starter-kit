<script setup lang="ts">
import { Separator } from '@/components/ui/separator';
</script>

<template>
    <div class="flex flex-col space-y-8 md:space-y-0 lg:flex-row lg:space-y-0 lg:space-x-12">
        <aside class="w-full max-w-xl lg:w-48">
            <slot name="sidebar" />
        </aside>

        <Separator class="my-6 md:hidden" />

        <div class="flex-1 md:max-w-2xl">
            <section class="max-w-xl space-y-12">
                <slot />
            </section>
        </div>
    </div>
</template>
