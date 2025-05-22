<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, router } from '@inertiajs/vue3';
import { Todo } from '../types';

const { t } = getI18n();

interface Props {
    todos: Todo[];
}

const props = defineProps<Props>();

const { todos } = props;

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Todos'),
        href: '/todos',
    },
];

const toggleComplete = (todo: Todo) => {
    router.patch(
        route('todos.toggle-complete', todo.id),
        {},
        {
            preserveScroll: true,
        },
    );
};

const deleteTodo = (todo: Todo) => {
    if (confirm(t('Are you sure you want to delete this todo?'))) {
        router.delete(route('todos.destroy', todo.id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="$t('Todos')" />

        <div class="mx-auto my-8 flex w-full max-w-5xl flex-col">
            <div class="mb-6 flex justify-between">
                <h1 class="text-2xl font-semibold">{{ $t('Todos') }}</h1>
                <Link :href="route('todos.create')">
                    <Button>{{ $t('Create Todo') }}</Button>
                </Link>
            </div>

            <div v-if="todos.length === 0" class="rounded-md bg-neutral-50 p-8 text-center">
                <p class="text-neutral-600">{{ $t('No todos found. Create your first todo!') }}</p>
            </div>

            <div v-else class="space-y-4">
                <div v-for="todo in todos" :key="todo.id" class="flex items-center justify-between rounded-md border border-neutral-200 p-4">
                    <div class="flex items-center space-x-4">
                        <Checkbox :checked="todo.completed" @update:checked="toggleComplete(todo)" />
                        <div>
                            <p :class="{ 'text-neutral-400 line-through': todo.completed }">{{ todo.title }}</p>
                            <p class="text-sm text-neutral-500" v-if="todo.user">{{ $t('Assigned to') }}: {{ todo.user.name }}</p>
                        </div>
                    </div>
                    <div class="flex space-x-2">
                        <Link :href="route('todos.update', todo.id)" method="patch" as="button" :data="{ completed: !todo.completed }">
                            <Button variant="outline" size="sm">
                                {{ todo.completed ? $t('Mark as Incomplete') : $t('Mark as Complete') }}
                            </Button>
                        </Link>
                        <Button variant="destructive" size="sm" @click="deleteTodo(todo)">
                            {{ $t('Delete') }}
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
