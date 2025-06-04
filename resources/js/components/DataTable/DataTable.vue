<script setup lang="ts" generic="TData extends object">
import DataTableColumnHeader from '@/components/DataTable/DataTableColumnHeader.vue';
import DataTableToolbar from '@/components/DataTable/DataTableToolbar.vue';
import SimplePaginator from '@/components/DataTable/SimplePaginator.vue';
import {
    IFilterFacetSelected,
    IPaginatedMeta,
    IQuery,
    ITableColumnOption,
    ITableFacetFilterOption,
    ITableOptions,
    ValueRetriever,
} from '@/components/DataTable/types';
import { Checkbox } from '@/components/ui/checkbox';
import { Table, TableBody, TableCaption, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { computed, ref, useSlots } from 'vue';

interface Props {
    rows: TData[];
    meta?: IPaginatedMeta;
    query?: IQuery;
    options: ITableOptions<TData>;
    facetFilters?: ITableFacetFilterOption<TData>[];
    hiddenColumns?: string[];
}

const props = defineProps<Props>();

const emits = defineEmits<{
    (e: 'update:pageSize', size: number): void;
    (e: 'update:page', page: number): void;
    (e: 'update:sort', field: { name: string; direction: undefined | 'asc' | 'desc' }): void;
    (e: 'update:term', term: string | undefined): void;
    (e: 'update:facet', facet: { key: string; selected: string[] | undefined }): void;
    (
        e: 'reload',
        data: { page: number; size: number | undefined; sort: string | undefined; term: string | undefined; filter: Record<string, string> },
    ): void;
}>();

const slots = useSlots();

const slotName = (column: string): string | undefined => {
    return slots[column] ? column : undefined;
};

const hasSlot = (column: string): boolean => {
    return !!slots[column];
};

/**
 * returns row data recursive
 * @param rowData
 * @param path
 */
function value<TData>(rowData: TData, path: ValueRetriever<TData>): any {
    if (typeof path === 'function') {
        return path(rowData);
    }

    return path.split('.').reduce((acc: TData, key: string) => {
        if (acc && typeof acc === 'object' && key in acc) {
            // @ts-expect-error:next-line
            return acc[key];
        }
        return undefined;
    }, rowData);
}

const selectedRows = ref<string[]>([]);

const toggleAllRowsSelection = (): void => {
    if (areAllRowsSelected.value === true) {
        selectedRows.value = [];
        return;
    }

    selectedRows.value = [];
    props.rows.forEach((value: TData, index: number) => {
        selectedRows.value.push(value[props.options.key] ?? index.toString(10));
    });
};

const toggleRowsSelection = (key: string | number): void => {
    if (selectedRows.value.includes(key as string)) {
        selectedRows.value = selectedRows.value.filter((item: string) => item !== key);
        return;
    }

    selectedRows.value.push(key as string);
};

const areAllRowsSelected = computed<boolean | 'indeterminate'>(() => {
    if (selectedRows.value.length < 1) {
        return false;
    }

    if (selectedRows.value.length === props.rows.length) {
        return true;
    }

    return 'indeterminate';
});

const hiddenColumns = ref<string[]>(props.hiddenColumns ?? []);
const toggleHiddenColumn = (key: string): void => {
    if (hiddenColumns.value.includes(key)) {
        hiddenColumns.value = hiddenColumns.value.filter((item: string) => item !== key);
        return;
    }

    hiddenColumns.value.push(key);
};

const visibleColumns = computed<ITableColumnOption<TData>[]>(() => {
    return props.options.columns.filter((column: ITableColumnOption<TData>) => !hiddenColumns.value.includes(column.key));
});

// handle state changes
const sort = ref<string | undefined>(undefined);
const term = ref<string | undefined>(undefined);
const facets = ref<IFilterFacetSelected>({});

const changedPage = (page: number): void => {
    emits('update:page', page);
    assembleReloadParameter(page, props.meta?.per_page);
};
const changedPageSize = (size: number): void => {
    emits('update:pageSize', size);
    assembleReloadParameter(1, size);
};
const changedSort = (field: { name: string; direction: undefined | 'asc' | 'desc' }): void => {
    emits('update:sort', field);
    if (!field.direction) {
        sort.value = undefined;
    } else {
        sort.value = (field.direction === 'desc' ? '-' : '') + field.name;
    }

    assembleReloadParameter(1, props.meta?.per_page);
};
const changedTerm = (t: string | undefined): void => {
    emits('update:term', t);
    term.value = t;
    assembleReloadParameter(1, props.meta?.per_page);
};
const changedFacet = (event: { key: string; selected: string[] | undefined }): void => {
    emits('update:facet', event);
    facets.value[event.key] = event.selected;
    assembleReloadParameter(1, props.meta?.per_page);
};
const assembleReloadParameter = (page: number, size: number | undefined): void => {
    let filter;
    for (const key in facets.value) {
        if (facets.value[key]) {
            if (!filter) {
                filter = {
                    [key]: facets.value[key].join(','),
                };
                continue;
            }

            if (!filter.hasOwnProperty(key)) {
                filter[key] = facets.value?.[key]?.join(',') ?? '';
                continue;
            }

            filter[key] = facets.value?.[key]?.join(',') ?? '';
        }
    }

    emits('reload', {
        page: page,
        size: size,
        sort: sort.value,
        term: term.value,
        filter: filter,
    });
};
</script>

<template>
    <div class="flex flex-col gap-y-4">
        <div v-if="hasSlot('primaryAction') || hasSlot('actions')" class="flex items-center justify-between">
            <div>
                <slot v-if="hasSlot('primaryAction')" name="primaryAction" :selected-rows="selectedRows" />
            </div>
            <div>
                <slot v-if="hasSlot('actions')" name="actions" :selected-rows="selectedRows" />
            </div>
        </div>

        <DataTableToolbar
            v-if="props.options.withToolbar !== false"
            :options="props.options"
            :query="props.query"
            @update:term="changedTerm"
            :facet-filters="props.facetFilters"
            @update:facet="changedFacet"
            :hidden-columns="hiddenColumns"
            @toggle-visibility="toggleHiddenColumn"
        />

        <div class="rounded-md border">
            <Table>
                <TableCaption v-if="props.options.caption">{{ props.options.caption }}</TableCaption>
                <TableHeader>
                    <TableRow>
                        <TableHead v-if="props.options.withRowSelection" class="w-8">
                            <Checkbox :model-value="areAllRowsSelected" @update:model-value="toggleAllRowsSelection" />
                        </TableHead>
                        <TableHead v-for="(column, index) in visibleColumns" :key="index" :class="column?.class">
                            <DataTableColumnHeader
                                :column="column"
                                :sort-entries="props.query?.sort"
                                :class="column?.class"
                                @update:sort="changedSort"
                            />
                        </TableHead>
                        <TableHead v-if="props.options.withRowActions || hasSlot('rowActions')" class="w-8">
                            <span class="sr-only">{{ $t('Actions') }}</span>
                        </TableHead>
                    </TableRow>
                </TableHeader>
                <TableBody>
                    <TableRow v-for="(row, rowIndex) in props.rows" :key="row[props.options.key] ?? rowIndex">
                        <TableCell v-if="props.options.withRowSelection">
                            <Checkbox
                                :model-value="selectedRows.includes(row[props.options.key] ?? rowIndex)"
                                @update:model-value="toggleRowsSelection(row[props.options.key] ?? rowIndex)"
                            />
                        </TableCell>
                        <TableCell
                            v-for="(column, index) in visibleColumns"
                            :key="index"
                            :class="column?.class"
                            data-v-html="value<TData>(row, column.value)"
                        >
                            <slot v-if="hasSlot(column.key)" :name="slotName(column.key)" v-bind="{ row, index: rowIndex }">
                                {{ value(row, column.key) }}
                            </slot>
                            <template v-else>
                                <span v-html="value(row, column.value ?? column.key)"></span>
                            </template>
                        </TableCell>
                        <TableCell v-if="props.options.withRowActions || hasSlot('rowActions')">
                            <slot name="rowActions" v-bind="{ row, index: rowIndex }"></slot>
                        </TableCell>
                    </TableRow>

                    <TableRow v-if="props.rows.length === 0">
                        <TableCell
                            :colspan="visibleColumns.length + (props.options.withRowSelection ? 1 : 0) + (props.options.withRowActions ? 1 : 0)"
                        >
                            <slot name="empty">
                                <div class="text-muted-foreground text-center">{{ $t('No data available') }}</div>
                            </slot>
                        </TableCell>
                    </TableRow>
                </TableBody>
            </Table>
        </div>

        <SimplePaginator
            v-if="props.options.withPagination !== false && props.meta"
            :meta="props.meta"
            :with-selection="props.options.withRowSelection"
            :rows-per-page="props.options.rowsPerPage"
            :selected-rows="selectedRows.length"
            @update-page-size="changedPageSize"
            @set-page="changedPage"
        />
    </div>
</template>
