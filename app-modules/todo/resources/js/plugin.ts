import { definePlugin } from '@/plugin';
import { RouteLocationRaw } from 'vue-router';
import { Todo } from './types';

declare module '@inertiajs/vue3' {
    interface PageProps {
        todo?: {
            routes: {
                index: string;
                create: string;
                store: string;
            };
        };
    }
}

export default definePlugin(({ app, route }) => {
    // Register components
    app.component('todo::Index', () => import('./pages/Index.vue'));
    app.component('todo::Create', () => import('./pages/Create.vue'));

    // Register routes
    route('todos.index', (): RouteLocationRaw => {
        return { name: 'todos.index' };
    });

    route('todos.create', (): RouteLocationRaw => {
        return { name: 'todos.create' };
    });

    route('todos.store', (): RouteLocationRaw => {
        return { name: 'todos.store' };
    });

    route('todos.update', (todo: Todo): RouteLocationRaw => {
        return { name: 'todos.update', params: { todo: todo.id } };
    });

    route('todos.toggle-complete', (todo: Todo): RouteLocationRaw => {
        return { name: 'todos.toggle-complete', params: { todo: todo.id } };
    });

    route('todos.destroy', (todo: Todo): RouteLocationRaw => {
        return { name: 'todos.destroy', params: { todo: todo.id } };
    });
});
