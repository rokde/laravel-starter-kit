import type { route as routeFn } from 'ziggy-js';

declare global {
    interface Window {
        locale: string;
    }
    const route: typeof routeFn;
}
