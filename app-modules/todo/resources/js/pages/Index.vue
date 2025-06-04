<script setup lang="ts">
import ConfirmButton from '@/components/ConfirmButton.vue';
import DataTable from '@/components/DataTable/DataTable.vue';
import DataTableRowActions from '@/components/DataTable/DataTableRowActions.vue';
import { IPaginatedMeta, IQuery, ITableFacetFilterOption, ITableOptions } from '@/components/DataTable/types';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { DropdownMenuItem } from '@/components/ui/dropdown-menu';
import { Label } from '@/components/ui/label';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Todo } from '../types';

const { t } = getI18n();

interface Props {
    data: Todo[];
    meta: IPaginatedMeta;
    query: IQuery;
    facets: ITableFacetFilterOption<Todo>[];
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Todos'),
        href: '/todos',
    },
];

const tableOptions: ITableOptions<Todo> = {
    key: 'id',
    withRowActions: true,
    withTermSearch: true,
    withToolbar: true,
    columns: [
        {
            key: 'title',
            label: t('Title'),
            hideable: true,
            sortable: true,
            class: 'w-full',
        },
        {
            key: 'assignee',
            label: t('Assigned to'),
            value: (row: Todo) => row.user?.name,
            hideable: true,
        },
    ],
};

const toggleComplete = (todo: Todo) => {
    router.patch(
        route('todos.toggle-complete', todo.id),
        {},
        {
            preserveScroll: true,
        },
    );
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="$t('Todos')" />

        <div class="mx-auto my-8 flex w-full max-w-5xl flex-col">
            <div class="mb-6 flex justify-between">
                <h1 class="text-2xl font-semibold">{{ $t('Todos') }}</h1>
            </div>

            <DataTable
                :rows="props.data"
                :meta="props.meta"
                :options="tableOptions"
                :query="props.query"
                :facet-filters="props.facets"
                @reload="router.reload({ data: $event })"
            >
                <template #primaryAction>
                    <Link :href="route('todos.create')">
                        <Button>{{ $t('Create Todo') }}</Button>
                    </Link>
                </template>
                <template #actions>actios</template>

                <template #title="{ row: todo }">
                    <Label :for="`todo${todo.id}`" class="flex flex-nowrap space-x-2">
                        <Checkbox :id="`todo${todo.id}`" :model-value="todo.completed" @update:modelValue="toggleComplete(todo)" />
                        <span :class="{ 'text-neutral-400 line-through': todo.completed }">{{ todo.title }}</span>
                    </Label>
                </template>

                <template #rowActions="{ row: todo }">
                    <DataTableRowActions :row="todo">
                        <DropdownMenuItem @click.stop="toggleComplete(todo)">
                            {{ todo.completed ? $t('Mark as Incomplete') : $t('Mark as Complete') }}
                        </DropdownMenuItem>
                        <DropdownMenuItem as-child>
                            <ConfirmButton
                                as="span"
                                :title="$t('Delete Todo')"
                                :confirmation="$t('Do you want to delete todo?')"
                                @confirmed="router.delete(route('todos.destroy', todo.id))"
                            ></ConfirmButton>
                        </DropdownMenuItem>
                    </DataTableRowActions>
                </template>

                <template #empty>
                    {{ $t('No todos found. Create your first todo!') }}
                </template>
            </DataTable>
        </div>
    </AppLayout>
</template>
