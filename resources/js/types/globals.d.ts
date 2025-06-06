import { SharedData } from '@/types/index';
import { PageProps as InertiaPageProps } from '@inertiajs/core';
import type { route as routeFn } from 'ziggy-js';

declare global {
    interface Window {
        locale: string;
    }
    const route: typeof routeFn;
}

export type PageProps<T extends Record<string, unknown> | unknown[] = Record<string, unknown> | unknown[]> = SharedData & T;

declare module '@inertiajs/core' {
    interface PageProps extends InertiaPageProps, AppPageProps {}
}

declare module '@vue/runtime-core' {
    interface ComponentCustomProperties {
        $t: (key: string, ...args: any[]) => string;
    }
}
