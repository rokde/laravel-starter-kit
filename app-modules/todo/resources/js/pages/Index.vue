<script setup lang="ts">
import DataTable from '@/components/DataTable/DataTable.vue';
import DataTableRowActions from '@/components/DataTable/DataTableRowActions.vue';
import { IPaginatedMeta, IQuery, ITableFacetFilterOption, ITableOptions } from '@/components/DataTable/types';
import Heading from '@/components/Heading.vue';
import SidePanel from '@/components/SidePanel.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { DropdownMenuItem, DropdownMenuSeparator } from '@/components/ui/dropdown-menu';
import { Label } from '@/components/ui/label';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/content/ContentLayout.vue';
import { localeDate } from '@/lib/date-functions';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import TodoForm from '@todo/components/TodoForm.vue';
import { ref } from 'vue';
import { Todo } from '../types';

const { t } = getI18n();

interface User {
    id: number;
    name: string;
    email: string;
}

interface Props {
    data: Todo[];
    meta: IPaginatedMeta;
    query: IQuery;
    facets: ITableFacetFilterOption<Todo>[];

    workspaceUsers: User[];
    presets: Array<{ value: string; label: string }>;
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
        {
            key: 'due_date',
            label: t('Due date'),
            value: (row: Todo) => localeDate(row.due_date),
            sortable: true,
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

const open = ref<boolean>(false);
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="$t('Todos')" />

        <ContentLayout>
            <Heading :title="$t('Todos')" />

            <DataTable
                :rows="props.data"
                :meta="props.meta"
                :options="tableOptions"
                :query="props.query"
                :facet-filters="props.facets"
                @reload="router.reload({ data: $event })"
            >
                <template #primaryAction>
                    <SidePanel :label="$t('Create Todo')" as="split-button" v-model:open="open">
                        <template #button>
                            <Link :href="route('todos.create')">
                                <Button class="rounded-r-none">{{ $t('Create Todo') }}</Button>
                            </Link>
                        </template>

                        <TodoForm :workspace-users="props.workspaceUsers" :presets="props.presets" class="p-4" @submitted="open = false" />
                    </SidePanel>
                </template>

                <template #title="{ row: todo }">
                    <Label :for="`todo${todo.id}`" class="flex cursor-pointer flex-nowrap space-x-2">
                        <Checkbox :id="`todo${todo.id}`" :model-value="todo.completed" @update:modelValue="toggleComplete(todo)" />
                        <span :class="{ 'text-neutral-400 line-through': todo.completed }">{{ todo.title }}</span>
                    </Label>
                </template>

                <template #rowActions="{ row: todo }">
                    <DataTableRowActions :row="todo">
                        <DropdownMenuItem @click.stop="toggleComplete(todo)">
                            {{ todo.completed ? $t('Mark as Incomplete') : $t('Mark as Complete') }}
                        </DropdownMenuItem>
                        <DropdownMenuSeparator />
                        <DropdownMenuItem variant="destructive" @click.stop="router.delete(route('todos.destroy', todo.id))">
                            <span class="text-red-500 hover:text-red-600 dark:text-red-700 dark:hover:text-red-600">{{ $t('Delete') }}</span>
                        </DropdownMenuItem>
                    </DataTableRowActions>
                </template>

                <template #empty>
                    {{ $t('No todos found. Create your first todo!') }}
                </template>
            </DataTable>
        </ContentLayout>
    </AppLayout>
</template>
