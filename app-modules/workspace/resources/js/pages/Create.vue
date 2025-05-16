<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import UserInfo from '@/components/UserInfo.vue';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { WorkspaceOwner } from '@workspace/types';

const { t } = getI18n();

interface Props {
    owner: WorkspaceOwner;
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: t('Create Workspace'),
        href: '/workspaces/new',
    },
];

const form = useForm({
    name: '',
});
const submit = () => {
    form.post(route('workspaces.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="$t('Workspace settings')" />

        <div class="mx-auto my-8 flex w-2xl max-w-2xl flex-col">
            <form @submit.prevent="submit" class="w-full space-y-6">
                <div class="grid gap-2">
                    <Label for="owner_id">{{ $t('Owner') }}</Label>

                    <div class="flex space-x-2">
                        <UserInfo :user="props.owner" show-email />
                    </div>
                </div>

                <div class="grid gap-2">
                    <Label for="name">{{ $t('Name') }}</Label>
                    <Input id="name" class="mt-1 block w-full" v-model="form.name" required autocomplete="off" :placeholder="$t('Name')" autofocus />
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
        </div>
    </AppLayout>
</template>
