<script setup lang="ts" generic="TData">
import { ITableFacetFilterOption, ITableFacetFilterOptionItem } from '@/components/DataTable/types';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Command, CommandEmpty, CommandGroup, CommandInput, CommandItem, CommandList, CommandSeparator } from '@/components/ui/command';
import { Popover, PopoverContent, PopoverTrigger } from '@/components/ui/popover';
import { Separator } from '@/components/ui/separator';
import { cn } from '@/lib/utils';
import { CheckIcon, CirclePlusIcon } from 'lucide-vue-next';
import { ref } from 'vue';

interface DataTableFacetedFilter {
    definition: ITableFacetFilterOption<TData> | undefined;
    selected?: string[] | number[];
}

const props = defineProps<DataTableFacetedFilter>();

const emits = defineEmits<{
    (e: 'update:facet', facet: { key: string; selected: string[] | undefined }): void;
}>();

const selectedValues = ref<Set<string>>(new Set(props.selected as string[]));

const toggleSelectedValue = (option: ITableFacetFilterOptionItem): void => {
    const isSelected = selectedValues.value.has(option.value as string);
    if (isSelected) {
        selectedValues.value.delete(option.value as string);
    } else {
        selectedValues.value.add(option.value as string);
    }

    emitUpdates();
};

const resetSelectedValue = (): void => {
    selectedValues.value.clear();

    emitUpdates();
};

const emitUpdates = (): void => {
    if (selectedValues.value.size < 1) {
        emits('update:facet', { key: props.definition.key, selected: undefined });
    } else {
        emits('update:facet', { key: props.definition.key, selected: Array.from<string>(selectedValues.value) });
    }
};

const filterFunction = (list: ITableFacetFilterOptionItem[], term: string) =>
    list.filter((i: ITableFacetFilterOptionItem) => i.label.toLowerCase()?.includes(term));

const groupedOptions = ref<Partial<Record<string, ITableFacetFilterOptionItem[]>>>(
    Object.groupBy(props.definition?.options ?? [], ({ group }: ITableFacetFilterOptionItem) => group ?? 'default'),
);

const groupKeys = ref<number>(Object.keys(groupedOptions.value).length);
</script>

<template>
    <Popover v-if="props.definition.label && props.definition.options?.length > 1">
        <PopoverTrigger as-child>
            <Button variant="outline" size="sm" class="h-8 border-dashed">
                <CirclePlusIcon class="mr-2 size-4" />
                {{ props.definition.label }}
                <template v-if="selectedValues.size > 0">
                    <Separator orientation="vertical" class="mx-2 h-4" />
                    <Badge variant="secondary" class="rounded-sm px-1 font-normal lg:hidden">
                        {{ selectedValues.size }}
                    </Badge>
                    <div class="hidden space-x-1 lg:flex">
                        <Badge
                            v-if="selectedValues.size > (props.definition.displaySelectedItems ?? 2)"
                            variant="secondary"
                            class="rounded-sm px-1 font-normal"
                        >
                            {{ $t('{count} selected', selectedValues.size) }}
                        </Badge>

                        <template v-else>
                            <Badge
                                v-for="option in props.definition.options.filter((option: ITableFacetFilterOptionItem) =>
                                    selectedValues.has(option.value as string),
                                )"
                                :key="option.label"
                                variant="secondary"
                                class="rounded-sm px-1 font-normal"
                            >
                                {{ option.label }}
                            </Badge>
                        </template>
                    </div>
                </template>
            </Button>
        </PopoverTrigger>
        <PopoverContent class="w-[200px] p-0" align="start">
            <Command :filter-function="filterFunction">
                <CommandInput :placeholder="props.definition.label" />
                <CommandList>
                    <CommandEmpty>{{ $t('No results found.') }}</CommandEmpty>
                    <CommandGroup
                        v-for="(options, group) in groupedOptions"
                        :key="group"
                        :heading="groupKeys > 1 && group !== 'default' ? group : undefined"
                    >
                        <CommandItem
                            v-for="option in options"
                            :key="option.label"
                            :value="option"
                            @select="(e: CustomEvent) => toggleSelectedValue(e.detail.value)"
                        >
                            <div
                                :class="
                                    cn(
                                        'border-primary mr-2 flex size-4 items-center justify-center rounded-sm border',
                                        selectedValues.has(option.value as string)
                                            ? 'bg-primary text-primary-foreground'
                                            : 'opacity-50 [&_svg]:invisible',
                                    )
                                "
                            >
                                <CheckIcon :class="cn('size-4')" />
                            </div>
                            <component v-if="option.icon" :is="option.icon" class="text-muted-foreground mr-2 size-4" />
                            <span>{{ option.label }}</span>
                            <span
                                v-if="option.hasOwnProperty('count')"
                                class="ml-auto flex size-4 items-center justify-center font-mono text-xs"
                                :class="{ 'text-muted-foreground': option.count === 0 }"
                            >
                                {{ option.count }}
                            </span>
                        </CommandItem>
                    </CommandGroup>

                    <template v-if="selectedValues.size > 0">
                        <CommandSeparator />
                        <CommandGroup>
                            <CommandItem :value="{ label: $t('Clear filters') }" class="justify-center text-center" @select="resetSelectedValue">
                                {{ $t('Clear filters') }}
                            </CommandItem>
                        </CommandGroup>
                    </template>
                </CommandList>
            </Command>
        </PopoverContent>
    </Popover>
</template>
