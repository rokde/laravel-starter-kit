<script setup lang="ts">
import { Button, type ButtonVariants } from '@/components/ui/button';
import { ScrollArea } from '@/components/ui/scroll-area';
import { Sheet, SheetClose, SheetContent, SheetDescription, SheetFooter, SheetHeader, SheetTitle, SheetTrigger } from '@/components/ui/sheet';
import { PanelRight } from 'lucide-vue-next';

const props = withDefaults(
    defineProps<{
        label?: string;
        title?: string;
        description?: string;
        submitLabel?: string;
        as?: 'button';
        disabled?: boolean;
        withFooter?: boolean;
        variant?: ButtonVariants['variant'];
    }>(),
    {
        label: 'Open',
        as: 'button',
        submitLabel: 'Save',
        disabled: false,
        withFooter: false,
        variant: 'default',
    },
);

const open = defineModel('open');
</script>

<template>
    <Sheet v-model:open="open">
        <SheetTrigger as-child>
            <Button v-if="props.as === 'button'" :variant="props.variant" :disabled="props.disabled"
                >{{ props.label }}
                <PanelRight class="size-4" />
            </Button>
        </SheetTrigger>
        <SheetContent>
            <SheetHeader>
                <slot name="header">
                    <SheetTitle>{{ props.title ?? props.label }}</SheetTitle>
                    <SheetDescription>{{ props.description }}</SheetDescription>
                </slot>
            </SheetHeader>
            <ScrollArea class="h-[calc(100vh-5.75rem)]">
                <slot />
            </ScrollArea>
            <SheetFooter v-if="props.withFooter">
                <slot name="footer">
                    <SheetClose as-child>
                        <Button type="submit">{{ props.submitLabel }}</Button>
                    </SheetClose>
                </slot>
            </SheetFooter>
        </SheetContent>
    </Sheet>
</template>
