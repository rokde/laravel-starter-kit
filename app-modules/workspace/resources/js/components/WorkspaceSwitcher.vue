<script setup lang="ts">
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuShortcut,
    DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu';
import { SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { useAuth } from '@/composables/useAuth';
import { Workspace } from '@workspace/index';
import { Briefcase, ChevronDown, Cog, Plus } from 'lucide-vue-next';
import { computed, onMounted, ref } from 'vue';

const workspaces = ref<Workspace[]>([]);
const user = useAuth();
const activeWorkspace = computed<Workspace | null>(() => workspaces.value.find((w: Workspace) => w.id === user.workspace_id));

const switchActiveWorkspace = (workspace: Workspace) => {
    console.log(workspace);
};

onMounted(() => {
    fetch(route('internal.api.workspaces.index'))
        .then((response) => response.json())
        .then((data: { workspaces: Workspace[] }) => (workspaces.value = data.workspaces))
        .catch((error) => console.error('Error:', error));
});
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <SidebarMenuButton class="w-fit px-1.5">
                        <span class="truncate font-semibold">{{ activeWorkspace?.name ?? $t('No workspace selected') }}</span>
                        <ChevronDown class="opacity-50" />
                    </SidebarMenuButton>
                </DropdownMenuTrigger>
                <DropdownMenuContent class="w-64 rounded-lg" align="start" side="bottom" :side-offset="4">
                    <DropdownMenuItem v-if="activeWorkspace" class="ga-2 p-2">
                        <div class="bg-background flex size-6 items-center justify-center rounded-md border">
                            <Cog class="size-4" />
                        </div>
                        <div class="text-muted-foreground font-medium">{{ $t('Configure {name}', { name: activeWorkspace.name }) }}</div>
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
                        <div class="flex size-6 items-center justify-center rounded-sm border">
                            <Briefcase class="size-4 shrink-0" />
                        </div>
                        {{ workspace.name }}
                        <DropdownMenuShortcut>⌘{{ index + 1 }}</DropdownMenuShortcut>
                    </DropdownMenuItem>
                    <DropdownMenuItem class="ga-2 p-2">
                        <div class="bg-background flex size-6 items-center justify-center rounded-md border">
                            <Plus class="size-4" />
                        </div>
                        <div class="text-muted-foreground font-medium">{{ $t('Add workspace') }}</div>
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </SidebarMenuItem>
    </SidebarMenu>
</template>
