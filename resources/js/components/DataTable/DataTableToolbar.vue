<script setup lang="ts" generic="TData">
import DataTableFacetedFilter from '@/components/DataTable/DataTableFacetedFilter.vue';
import DataTableViewOptions from '@/components/DataTable/DataTableViewOptions.vue';
import { IQuery, ITableFacetFilterOption, ITableOptions } from '@/components/DataTable/types';
import { createDebounce } from '@/components/DataTable/utils';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { SearchIcon, XIcon } from 'lucide-vue-next';
import { ref } from 'vue';

interface DataTableViewOptionsProps {
    options: ITableOptions<TData>;
    hiddenColumns: string[];
    query?: IQuery;
    facetFilters?: ITableFacetFilterOption<TData>[];
}

const props = defineProps<DataTableViewOptionsProps>();

const emits = defineEmits<{
    (e: 'toggleVisibility', column: string): void;
    (e: 'update:term', term: string | undefined): void;
    (e: 'update:facet', facet: { key: string; selected: string[] | undefined }): void;
}>();

const term = ref<string>(props.query?.filter.term as string);

const onTermInput = (input: string): void => {
    if (input === '') {
        emits('update:term', undefined);
        isFiltered.value = false;
    } else {
        emits('update:term', input);
        isFiltered.value = true;
    }
};

const debounce = createDebounce();

const isFiltered = ref<boolean>(!!term.value);

const resetFilters = (): void => {
    term.value = '';
    onTermInput(term.value);
};
</script>

<template>
    <div class="flex items-center justify-between">
        <div class="flex flex-1 items-center space-x-2">
            <div v-if="props.options.withTermSearch" class="relative items-center">
                <Input
                    :placeholder="$t('Search') + '...'"
                    :model-value="term"
                    class="h-8 w-[150px] pr-10 pl-8 lg:w-[250px]"
                    @input="debounce(() => onTermInput($event.target.value), 500)"
                />
                <span class="absolute inset-y-0 start-0 flex items-center justify-center px-2">
                    <SearchIcon class="text-muted-foreground size-4" />
                </span>
                <span v-if="isFiltered" class="absolute inset-y-0 end-0 flex items-center justify-center">
                    <Button type="button" variant="ghost" class="text-muted-foreground h-8 px-2 lg:px-3" @click="resetFilters">
                        <XIcon class="size-4" />
                    </Button>
                </span>
            </div>

            <DataTableFacetedFilter
                v-for="facetFilter in props.facetFilters"
                :key="facetFilter.key"
                :definition="facetFilter"
                :selected="props.query?.filter.facets?.[facetFilter.key as string] ?? []"
                @update:facet="emits('update:facet', $event)"
            />
        </div>
        <DataTableViewOptions :options="props.options" :hidden-columns="props.hiddenColumns" @toggle-visibility="emits('toggleVisibility', $event)" />
    </div>
</template>
