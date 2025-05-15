<script setup lang="ts">
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { useForm } from '@inertiajs/vue3';
import MemberRoleSelect from '@workspace/components/MemberRoleSelect.vue';
import { Role } from '@workspace/types';

interface Props {
    roles: { [key: string]: Role };
}

const props = defineProps<Props>();

const form = useForm({
    email: '',
    role: '',
});
const submit = () => {
    form.post(route('workspaces.invitations.store'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <div class="flex flex-col space-y-6">
        <HeadingSmall
            :title="$t('Invite members')"
            :description="
                $t(
                    'Work together with other users by inviting them to your workspace. Invitations can be sent to email addresses. Invitations can be revoked at any time.',
                )
            "
        />

        <form @submit.prevent="submit" class="space-y-6">
            <div class="flex space-x-4">
                <div class="grid grow gap-2">
                    <Label for="email">{{ $t('Email') }}</Label>
                    <Input
                        id="email"
                        type="email"
                        class="block w-full"
                        v-model="form.email"
                        required
                        autocomplete="off"
                        placeholder="john.doe@mail.com"
                    />
                    <InputError class="mt-2" :message="form.errors.email" />
                </div>

                <div class="grid gap-2">
                    <Label for="role">{{ $t('Role') }}</Label>
                    <MemberRoleSelect v-model="form.role" :roles="props.roles" />
                    <InputError class="mt-2" :message="form.errors.role" />
                </div>
            </div>

            <div class="flex items-center gap-4">
                <Button :disabled="form.processing">{{ $t('Send invite') }}</Button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">{{ $t('Invited.') }}</p>
                </Transition>
            </div>
        </form>
    </div>
</template>
