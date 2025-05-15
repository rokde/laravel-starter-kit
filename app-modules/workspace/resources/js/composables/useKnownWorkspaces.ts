import { useTypedPageProps } from '@/composables/useTypedPageProps';
import type { PageProps } from '@/types/globals';
import { WorkspaceInfo } from '../types';

export function useKnownWorkspaces(): WorkspaceInfo[] {
    const { knownWorkspaces } = useTypedPageProps<PageProps>().props;

    return knownWorkspaces as unknown as WorkspaceInfo[];
}
