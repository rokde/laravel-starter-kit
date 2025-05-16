<script setup lang="ts">
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { SidebarMenuButton } from '@/components/ui/sidebar';
import { useInitials } from '@/composables/useInitials';
import { Link, router } from '@inertiajs/vue3';
import { useKnownWorkspaces } from '@workspace/composables/useKnownWorkspaces';
import type { WorkspaceInfo } from '@workspace/types';
import { ChevronDown, Cog, Plus } from 'lucide-vue-next';
import { computed } from 'vue';

interface Props {
    align?: 'start' | 'center' | 'end';
    side?: 'top' | 'right' | 'bottom' | 'left';
}

const props = withDefaults(defineProps<Props>(), {
    align: 'start',
    side: 'bottom',
});

const workspaces = useKnownWorkspaces();

const activeWorkspace = computed<WorkspaceInfo>(() => workspaces.find((w: WorkspaceInfo) => w.currentWorkspace));

const { getInitials } = useInitials();

const switchActiveWorkspace = (workspace: WorkspaceInfo) => {
    router.put(
        route('workspaces.set-current'),
        { workspace_id: workspace.id },
        {
            preserveScroll: true,
            onSuccess: () => {
                setTimeout(() => location.reload(), 10);
            },
        },
    );
};
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <SidebarMenuButton class="w-fit px-1.5">
                <span class="truncate font-semibold">{{ activeWorkspace?.name ?? $t('No workspace selected') }}</span>
                <ChevronDown class="opacity-50" />
            </SidebarMenuButton>
        </DropdownMenuTrigger>
        <DropdownMenuContent class="w-64 rounded-lg" :align="props.align" :side="props.side" :side-offset="4">
            <DropdownMenuItem v-if="activeWorkspace" class="gap-2 p-2">
                <Link :href="route('workspaces.show')" class="flex items-center gap-2">
                    <div class="bg-background flex size-6 items-center justify-center rounded-md border">
                        <Cog class="size-4" />
                    </div>
                    <div class="text-muted-foreground font-medium">{{ $t('Configure {name}', { name: activeWorkspace.name }) }}</div>
                </Link>
            </DropdownMenuItem>
            <DropdownMenuSeparator v-if="activeWorkspace" />
            <DropdownMenuLabel class="text-muted-foreground text-xs">
                {{ $t('Workspaces') }}
            </DropdownMenuLabel>
            <DropdownMenuItem
                v-for="(workspace, index) in workspaces"
                :key="workspace.name"
                class="gap-2 p-2"
                @click="switchActiveWorkspace(workspace)"
            >
                <div
                    class="flex size-6 items-center justify-center rounded-sm border"
                    :class="{ 'bg-muted-foreground border-muted': workspace.currentWorkspace }"
                >
                    <span
                        class="flex size-4 shrink-0 items-center justify-center overflow-hidden rounded-full text-xs"
                        :class="{ 'text-white': workspace.currentWorkspace }"
                        >{{ getInitials(workspace.name) }}</span
                    >
                </div>
                {{ workspace.name }}
                <!-- DropdownMenuShortcut>⌘{{ index + 1 }}</DropdownMenuShortcut -->
            </DropdownMenuItem>
            <DropdownMenuSeparator />
            <DropdownMenuItem class="ga-2 p-2">
                <Link :href="route('workspaces.create')" class="flex items-center gap-2">
                    <div class="bg-background flex size-6 items-center justify-center rounded-md border">
                        <Plus class="size-4" />
                    </div>
                    <div class="text-muted-foreground font-medium">{{ $t('Add workspace') }}</div>
                </Link>
            </DropdownMenuItem>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
