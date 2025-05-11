<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem, User } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Workspace } from '@workspace/index';
import WorkspaceSettingsLayout from '@workspace/layouts/WorkspaceSettingsLayout.vue';
import { SquareUserRound } from 'lucide-vue-next';

const { t } = getI18n();

interface Props {
    workspace: Workspace;
    owner: User;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Workspace settings'),
        href: '/workspaces/current',
    },
    {
        title: props.workspace.name,
        href: '/workspaces/current',
    },
];

const form = useForm({
    name: props.workspace.name,
});
const submit = () => {
    form.patch(route('workspaces.update'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="$t('Workspace settings')" />

        <WorkspaceSettingsLayout>
            <HeadingSmall :title="$t('Profile')" :description="$t('The workspace name and owner information.')" />

            <form @submit.prevent="submit" class="space-y-6">
                <div class="grid gap-2">
                    <Label for="owner_id">{{ $t('Owner') }}</Label>

                    <div class="mt-2 flex items-center">
                        <SquareUserRound class="text-muted-foreground size-8" />
                        <div class="ms-4 leading-tight">
                            <div class="text-gray-900 dark:text-white">{{ props.owner.name }}</div>
                            <div class="text-sm text-gray-700 dark:text-gray-300">
                                {{ props.owner.email }}
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="name">{{ $t('Name') }}</Label>
                    <Input id="name" class="mt-1 block w-full" v-model="form.name" required autocomplete="off" :placeholder="$t('Full name')" />
                    <p class="text-muted-foreground text-sm dark:text-red-500">
                        {{ $t('For caching reasons, the workspace changer retains the previous name for a further 10 minutes.') }}
                    </p>
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="flex items-center gap-4">
                    <Button :disabled="form.processing">{{ $t('Save') }}</Button>

                    <Transition
                        enter-active-class="transition ease-in-out"
                        enter-from-class="opacity-0"
                        leave-active-class="transition ease-in-out"
                        leave-to-class="opacity-0"
                    >
                        <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">{{ $t('Saved.') }}</p>
                    </Transition>
                </div>
            </form>
        </WorkspaceSettingsLayout>
    </AppLayout>
</template>
