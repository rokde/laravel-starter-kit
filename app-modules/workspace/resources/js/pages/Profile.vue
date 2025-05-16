<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import UserInfo from '@/components/UserInfo.vue';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import WorkspaceSettingsLayout from '@workspace/layouts/WorkspaceSettingsLayout.vue';
import { Workspace, WorkspaceOwner } from '@workspace/types';

const { t } = getI18n();

interface Props {
    workspace: Workspace;
    owner: WorkspaceOwner;
    abilities: {
        'workspace.update': boolean;
    };
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
        onSuccess: () => {
            setTimeout(() => location.reload(), 1000);
        },
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

                    <div class="flex space-x-2">
                        <UserInfo :user="props.owner" show-email />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="name">{{ $t('Name') }}</Label>
                    <Input
                        id="name"
                        class="mt-1 block w-full"
                        v-model="form.name"
                        required
                        autocomplete="off"
                        :placeholder="$t('Full name')"
                        :disabled="!props.abilities['workspace.update']"
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div class="flex items-center gap-4">
                    <Button :disabled="!props.abilities['workspace.update'] || form.processing">{{ $t('Save') }}</Button>

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
