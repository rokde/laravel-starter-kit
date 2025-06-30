<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/content/ContentLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Permission } from '@authorization/types';
import { Head, useForm } from '@inertiajs/vue3';
import { Workspace } from '@workspace/types/index';

const { t } = getI18n();

interface Props {
    workspace: Workspace;
    permissions: { [key: string]: Permission[] };
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
    {
        title: t('Roles'),
        href: '/workspaces/current/roles',
    },
    {
        title: t('Create role'),
        href: '/workspaces/current/roles/create',
    },
];

const form = useForm<{ name: string; permissions: string[] }>({
    name: '',
    permissions: [],
});

const handleChange = (value: boolean | 'indeterminate', permission: Permission) => {
    if (value) {
        form.permissions.push(permission.id);
        return;
    }
    form.permissions = form.permissions.filter((p) => p !== permission.id);
};

const submit = () => {
    form.post(route('workspaces.roles.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbs">
        <Head :title="$t('Create role')" />

        <ContentLayout type="form">
            <Heading :title="$t('Create role')" />

            <form @submit.prevent="submit" class="w-full space-y-6">
                <div class="grid gap-2">
                    <Label for="name">{{ $t('Name') }}</Label>
                    <Input
                        id="name"
                        class="block w-full"
                        v-model="form.name"
                        required
                        autocomplete="off"
                        :placeholder="$t('Name of the role')"
                        autofocus
                    />
                    <InputError class="mt-2" :message="form.errors.name" />
                </div>

                <div v-if="props.permissions" class="grid gap-2">
                    <Label for="permissions">{{ $t('Permissions') }}</Label>

                    <template v-for="(permissions, group) of props.permissions" :key="group">
                        <div>
                            {{ $t('Resource') }}: <span class="capitalize">{{ group }}</span>
                        </div>
                        <Label v-for="permission of permissions" :key="permission.id">
                            <Checkbox
                                :model-value="form.permissions.includes(permission.id)"
                                @update:model-value="handleChange($event, permission)"
                                :value="permission.id"
                            />
                            {{ permission.description }}
                        </Label>
                    </template>
                    <InputError class="mt-2" :message="form.errors.permissions" />
                </div>

                <div class="flex items-center gap-4">
                    <Button :disabled="form.processing" data-pan="create-todo">{{ $t('Save') }}</Button>

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
        </ContentLayout>
    </AppLayout>
</template>
