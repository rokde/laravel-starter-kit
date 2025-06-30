<script setup lang="ts">
import ConfirmButton from '@/components/ConfirmButton.vue';
import { Select, SelectContent, SelectGroup, SelectItem, SelectSeparator, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Role } from '@authorization/types/index';
import { ref } from 'vue';

interface Props {
    modelValue: string;
    roles: Role[];
    disabled?: boolean;
    transferOwnership?: boolean;
}
const props = withDefaults(defineProps<Props>(), {
    disabled: false,
    transferOwnership: false,
});

const emits = defineEmits<{
    (e: 'update:modelValue', value: number): void;
    (e: 'transferOwnership'): void;
}>();

const confirmOwnerRole = ref<typeof ConfirmButton | null>();

const OWNER_ROLE_ID = 0;

const roleUpdated = (role: number): void => {
    if (role !== OWNER_ROLE_ID) {
        emits('update:modelValue', role);
    }

    if (confirmOwnerRole.value) {
        confirmOwnerRole.value.confirming = true;
    }
};
</script>

<template>
    <Select :model-value="props.modelValue" @update:modelValue="roleUpdated" :disabled="props.disabled">
        <SelectTrigger class="w-[180px]">
            <SelectValue :placeholder="$t('Select a role')">
                {{ props.modelValue ? $t(`roles.${props.modelValue}.name`) : $t('Select a role') }}
            </SelectValue>
        </SelectTrigger>
        <SelectContent>
            <SelectGroup v-if="props.transferOwnership">
                <SelectItem :value="OWNER_ROLE_ID">
                    <div class="flex flex-col items-start">
                        <span>{{ $t(`roles.owner.name`) }}</span>
                        <span class="text-muted-foreground">{{ $t(`roles.owner.description`) }}</span>
                    </div>
                </SelectItem>
            </SelectGroup>
            <SelectSeparator v-if="props.transferOwnership" />
            <SelectGroup>
                <SelectItem v-for="role of props.roles" :key="role.id" :value="role.id">
                    <div class="flex flex-col items-start">
                        <span>{{ role.name }}</span>
                    </div>
                </SelectItem>
            </SelectGroup>
        </SelectContent>
    </Select>
    <ConfirmButton
        v-if="props.transferOwnership"
        as="invisible"
        ref="confirmOwnerRole"
        :title="$t('Transfer of ownership')"
        :confirmation="
            $t('Are you sure you want to transfer ownership of this workspace? You remain a member of the workspace. This process is irreversible.')
        "
        :submit-label="$t('Transfer of ownership')"
        @confirmed="emits('transferOwnership')"
    />
</template>
