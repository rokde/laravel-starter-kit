<script setup lang="ts" generic="TData">
import { ITableColumnOption, ITableOptions } from '@/components/DataTable/types';
import { Button } from '@/components/ui/button';
import {
    DropdownMenu,
    DropdownMenuCheckboxItem,
    DropdownMenuContent,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { Settings2Icon } from 'lucide-vue-next';
import { computed } from 'vue';

interface DataTableViewOptionsProps {
    options: ITableOptions<TData>;
    hiddenColumns: string[];
}

const props = defineProps<DataTableViewOptionsProps>();

const emits = defineEmits<{
    (e: 'toggleVisibility', column: string): void;
}>();

const columns = computed<ITableColumnOption<TData>[]>(() => props.options.columns.filter((column: ITableColumnOption<TData>) => column.hideable));
</script>

<template>
    <DropdownMenu v-if="columns.length">
        <DropdownMenuTrigger as-child>
            <Button variant="outline" size="sm" class="ml-auto hidden h-8 lg:flex">
                <Settings2Icon class="mr-2 size-4" />
                {{ $t('View') }}
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end" class="w-[150px]">
            <DropdownMenuLabel>{{ $t('Toggle columns') }}</DropdownMenuLabel>
            <DropdownMenuSeparator />
            <DropdownMenuCheckboxItem
                v-for="column in columns"
                :key="column.key"
                class="capitalize"
                :checked="!props.hiddenColumns.includes(column.key as string)"
                @update:checked="emits('toggleVisibility', column.key as string)"
            >
                {{ column.label }}
            </DropdownMenuCheckboxItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
