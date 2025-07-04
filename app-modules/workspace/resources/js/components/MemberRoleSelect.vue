<script setup lang="ts">
import ConfirmButton from '@/components/ConfirmButton.vue';
import { Select, SelectContent, SelectGroup, SelectItem, SelectSeparator, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Role } from '@workspace/types';
import { computed, ref } from 'vue';

interface Props {
    modelValue: string;
    roles: { [key: string]: Role };
    ownerRoleKey?: string;
    disabled?: boolean;
}
const props = withDefaults(defineProps<Props>(), {
    disabled: false,
    ownerRoleKey: '-',
});

const emits = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const confirmOwnerRole = ref<typeof ConfirmButton | null>();

const roleUpdated = (role: string): void => {
    if (role !== props.ownerRoleKey) {
        emits('update:modelValue', role);
    }

    if (confirmOwnerRole.value) {
        confirmOwnerRole.value.confirming = true;
    }
};

const hasOwnerRole = computed<boolean>(() => props.roles.hasOwnProperty(props.ownerRoleKey));
const displayRoles = computed<{ [key: string]: Role }>(() => {
    const roles = { ...props.roles } as { [key: string]: Role };
    delete roles.owner;
    return roles;
});
</script>

<template>
    <Select :model-value="props.modelValue" @update:modelValue="roleUpdated" :disabled="props.disabled">
        <SelectTrigger class="w-[180px]">
            <SelectValue :placeholder="$t('Select a role')">
                {{ props.modelValue ? $t(`roles.${props.modelValue}.name`) : $t('Select a role') }}
            </SelectValue>
        </SelectTrigger>
        <SelectContent>
            <SelectGroup v-if="hasOwnerRole">
                <SelectItem value="owner">
                    <div class="flex flex-col items-start">
                        <span>{{ $t(`roles.${props.ownerRoleKey}.name`) }}</span>
                        <span class="text-muted-foreground">{{ $t(`roles.${props.ownerRoleKey}.description`) }}</span>
                    </div>
                </SelectItem>
            </SelectGroup>
            <SelectSeparator v-if="hasOwnerRole" />
            <SelectGroup>
                <SelectItem v-for="role of displayRoles" :key="role.key" :value="role.key">
                    <div class="flex flex-col items-start">
                        <span>{{ $t(`roles.${role.key}.name`) }}</span>
                        <span class="text-muted-foreground">{{ $t(`roles.${role.key}.description`) }}</span>
                    </div>
                </SelectItem>
            </SelectGroup>
        </SelectContent>
    </Select>
    <ConfirmButton
        v-if="hasOwnerRole"
        as="invisible"
        ref="confirmOwnerRole"
        :title="$t('Transfer of ownership')"
        :confirmation="
            $t('Are you sure you want to transfer ownership of this workspace? You remain a member of the workspace. This process is irreversible.')
        "
        :submit-label="$t('Transfer of ownership')"
        @confirmed="emits('update:modelValue', 'owner')"
    />
</template>
