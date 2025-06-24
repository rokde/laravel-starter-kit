<script setup lang="ts">
import ConfirmButton from '@/components/ConfirmButton.vue';
import UserInfo from '@/components/UserInfo.vue';
import type { User } from '@/types';
import { router, useForm } from '@inertiajs/vue3';
import MemberRoleSelect from '@workspace/components/MemberRoleSelect.vue';
import { Role } from '@workspace/types';
import { Check } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    members: Array<User & { role: string; membership: { role: string } }>;
    roles: { [key: string]: Role };
    remove: boolean;
    readonly?: boolean;
}

const props = withDefaults(defineProps<Props>(), {
    readonly: false,
});

const currentId = ref<number | null>(null);
const form = useForm({
    id: '',
    role: '',
});

const modifyRoleForMember = (id: number, role: string) => {
    currentId.value = id;
    form.id = id.toString(10);
    form.role = role;

    form.patch(route('workspaces.members.update'), {
        preserveScroll: true,
        onSuccess: () => {
            router.reload();
        },
    });
};

const removeMember = (member: User) => {
    router.delete(route('workspaces.members.delete', member.id), {
        preserveScroll: true,
        onSuccess: () => {
            router.reload();
        },
    });
};
</script>

<template>
    <div v-for="member of props.members" :key="member.id" class="flex items-center space-x-2">
        <UserInfo :user="member" show-email />
        <div class="flex items-center gap-2">
            <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
            >
                <Check v-show="form.recentlySuccessful && currentId === member.id" class="size-4 text-green-600" />
            </Transition>
            <MemberRoleSelect
                :model-value="member.membership.role"
                @update:model-value="modifyRoleForMember(member.id, $event)"
                :roles="props.roles"
                :disabled="props.readonly"
            />
        </div>
        <ConfirmButton
            as="icon"
            :title="$t('Remove member?')"
            :confirmation="$t('If you remove the member, they will no longer have access to the workspace.')"
            :disabled="!props.remove"
            @confirmed="removeMember(member)"
            data-pan="remove-workspace-member"
        />
    </div>
</template>
