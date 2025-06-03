<script setup lang="ts" generic="TData">
import { ISortEntry, ITableColumnOption } from '@/components/DataTable/types';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { cn } from '@/lib/utils';
import { ArrowDownAzIcon, ArrowDownUpIcon, ArrowUpZaIcon, XIcon } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    column: ITableColumnOption<TData>;
    sortEntries?: ISortEntry[];
}

const props = defineProps<Props>();

defineOptions({ inheritAttrs: false });

const emits = defineEmits<{
    (e: 'update:sort', field: { name: string; direction: undefined | 'asc' | 'desc' }): void;
}>();

const columnSorted = computed<undefined | 'asc' | 'desc'>((): undefined | 'asc' | 'desc' => {
    const sortableColumnQuery = props.sortEntries?.filter((sort: ISortEntry) => sort.field === props.column.key);
    if (!sortableColumnQuery || !sortableColumnQuery.length) {
        return undefined;
    }

    return sortableColumnQuery[0].direction;
});

const sort = (direction: undefined | 'asc' | 'desc'): void => {
    emits('update:sort', { name: props.column.key as string, direction: direction });
};
</script>

<template>
    <div v-if="props.column.sortable" :class="cn('flex items-center space-x-2', $attrs.class ?? '')">
        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button variant="ghost" size="sm" class="data-[state=open]:bg-accent -ml-3 h-8 text-sm">
                    <span>{{ props.column.label }}</span>
                    <ArrowUpZaIcon v-if="columnSorted === 'desc'" class="ml-2 size-4" />
                    <ArrowDownAzIcon v-else-if="columnSorted === 'asc'" class="ml-2 size-4" />
                    <ArrowDownUpIcon v-else class="text-muted-foreground ml-2 size-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="start">
                <DropdownMenuItem @click="sort('asc')">
                    <ArrowDownAzIcon class="text-muted-foreground/70 mr-2 h-3.5 w-3.5" />
                    {{ $t('Ascending') }}
                </DropdownMenuItem>
                <DropdownMenuItem @click="sort('desc')">
                    <ArrowUpZaIcon class="text-muted-foreground/70 mr-2 h-3.5 w-3.5" />
                    {{ $t('Descending') }}
                </DropdownMenuItem>
                <DropdownMenuItem @click="sort(undefined)">
                    <XIcon class="text-muted-foreground/70 mr-2 h-3.5 w-3.5" />
                    {{ $t('Clear sorting') }}
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>
    </div>
    <div v-else class="inline-flex items-center justify-center gap-2 whitespace-nowrap">
        <span>{{ props.column.label }}</span>
        <ArrowUpZaIcon v-if="columnSorted === 'desc'" class="ml-2 size-4" />
        <ArrowDownAzIcon v-else-if="columnSorted === 'asc'" class="ml-2 size-4" />
    </div>
</template>
