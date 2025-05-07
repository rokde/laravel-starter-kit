import type { PageProps } from '@/types/globals';
import { usePage } from '@inertiajs/vue3';

export function useTypedPageProps<T extends Record<string, unknown> | unknown[] = Record<string, unknown> | unknown[]>() {
    return usePage<PageProps<T>>();
}
