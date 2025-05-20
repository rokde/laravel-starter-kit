import { useTypedPageProps } from '@/composables/useTypedPageProps';
import type { PageProps } from '@/types/globals';
import { Notification } from '../types';

export function useNotifications(): Notification[] {
    const { userNotifications } = useTypedPageProps<PageProps>().props;

    return userNotifications as unknown as Notification[];
}
