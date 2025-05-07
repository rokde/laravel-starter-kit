import { useTypedPageProps } from '@/composables/useTypedPageProps';
import { User } from '@/types';
import type { PageProps } from '@/types/globals';

export function useAuth(): User {
    const { auth } = useTypedPageProps<PageProps>().props;

    if (auth === null) {
        throw new Error('You are not logged in.');
    }

    return auth.user as unknown as User;
}
