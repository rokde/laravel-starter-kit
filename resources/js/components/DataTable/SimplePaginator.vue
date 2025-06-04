<script setup lang="ts">
import { IPaginatedMeta } from '@/components/DataTable/types';
import { Button } from '@/components/ui/button';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { ChevronLeftIcon, ChevronRightIcon, ChevronsLeftIcon, ChevronsRightIcon } from 'lucide-vue-next';
import { computed, ComputedRef } from 'vue';

const props = withDefaults(
    defineProps<{
        meta: IPaginatedMeta;
        selectedRows?: number;
        withSelection?: boolean;
        rowsPerPage?: number[];
    }>(),
    {
        selectedRows: 0,
        withSelection: false,
    },
);

const emits = defineEmits<{
    updatePageSize: [size: number];
    setPage: [page: number];
}>();

const pageSize: ComputedRef<number> = computed<number>(() => props.meta.per_page);

const defaultRowsPerPage: number[] = [10, 30, 50, 100];

const rowsPerPage: ComputedRef<number[]> = computed<number[]>(() => {
    const rowsPerPage: number[] = props.rowsPerPage ?? defaultRowsPerPage;
    if (!rowsPerPage.includes(props.meta.per_page)) {
        rowsPerPage.push(props.meta.per_page);
    }

    return rowsPerPage.sort((a: number, b: number): number => {
        if (a < b) return -1;
        if (a > b) return 1;
        return 0;
    });
});

const rowsOnCurrentPage: ComputedRef<number> = computed<number>(() => {
    return props.meta.current_page < props.meta.last_page ? props.meta.per_page : props.meta.total - (props.meta.last_page - 1) * props.meta.per_page;
});
</script>

<template>
    <div class="flex items-center justify-between">
        <div class="flex-1 text-sm" :class="{ 'text-muted-foreground': props.selectedRows < 1 }">
            <template v-if="props.withSelection || props.selectedRows > 0">
                {{
                    $t('{selected} of {rows} row(s) selected', {
                        selected: props.selectedRows,
                        rows: rowsOnCurrentPage,
                    })
                }}
            </template>
        </div>

        <div class="flex items-center space-x-6 lg:space-x-8">
            <div class="flex items-center space-x-2">
                <p class="text-sm font-medium">
                    {{ $t('Rows per page') }}
                </p>
                <Select :model-value="pageSize.toString(10)" @update:model-value="emits('updatePageSize', parseInt($event, 10))">
                    <SelectTrigger class="h-8 w-[70px]">
                        <SelectValue :placeholder="`${pageSize}`" />
                    </SelectTrigger>
                    <SelectContent side="top">
                        <SelectItem v-for="items in rowsPerPage" :key="items" :value="`${items}`">
                            {{ items }}
                        </SelectItem>
                    </SelectContent>
                </Select>
            </div>
            <div class="flex w-[100px] items-center justify-center text-sm font-medium">
                {{ $t('Page {page} of {pages}', { page: props.meta.current_page, pages: props.meta.last_page }) }}
            </div>
            <div class="flex items-center space-x-2">
                <Button variant="outline" class="hidden size-8 p-0 lg:flex" :disabled="props.meta.current_page <= 1" @click="emits('setPage', 1)">
                    <span class="sr-only">{{ $t('Go to first page') }}</span>
                    <ChevronsLeftIcon class="size-4" />
                </Button>
                <Button
                    variant="outline"
                    class="size-8 p-0"
                    :disabled="props.meta.current_page <= 1"
                    @click="emits('setPage', props.meta.current_page - 1)"
                >
                    <span class="sr-only">{{ $t('Go to previous page') }}</span>
                    <ChevronLeftIcon class="size-4" />
                </Button>
                <Button
                    variant="outline"
                    class="size-8 p-0"
                    :disabled="props.meta.current_page >= props.meta.last_page"
                    @click="emits('setPage', props.meta.current_page + 1)"
                >
                    <span class="sr-only">{{ $t('Go to next page') }}</span>
                    <ChevronRightIcon class="size-4" />
                </Button>
                <Button
                    variant="outline"
                    class="hidden size-8 p-0 lg:flex"
                    :disabled="props.meta.current_page >= props.meta.last_page"
                    @click="emits('setPage', props.meta.last_page)"
                >
                    <span class="sr-only">{{ $t('Go to last page') }}</span>
                    <ChevronsRightIcon class="size-4" />
                </Button>
            </div>
        </div>
    </div>
</template>
