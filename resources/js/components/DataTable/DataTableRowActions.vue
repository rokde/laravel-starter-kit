<script setup lang="ts" generic="TData">
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuShortcut, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { EllipsisIcon } from 'lucide-vue-next';

interface Props {
    row: TData;
}

const props = defineProps<Props>();

const emits = defineEmits<{
    (e: 'action', action: { type: string; row: TData }): void;
}>();

const selectAction = (type: string): void => {
    emits('action', { type: type, row: props.row });
};
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <slot name="trigger">
                <Button variant="ghost" class="data-[state=open]:bg-muted flex size-8 p-0">
                    <EllipsisIcon class="size-4" />
                    <span class="sr-only">{{ $t('Open menu') }}</span>
                </Button>
            </slot>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" class="w-[160px]">
            <slot v-bind="{ selectAction: selectAction }">
                <DropdownMenuItem @click="selectAction('delete')">
                    {{ $t('Delete') }}
                    <DropdownMenuShortcut>⌘⌫</DropdownMenuShortcut>
                </DropdownMenuItem>
            </slot>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
