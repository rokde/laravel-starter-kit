<script setup lang="ts">
import { Select, SelectContent, SelectGroup, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { Role } from '@workspace/types';

interface Props {
    modelValue: string;
    roles: { [key: string]: Role };
    disabled?: boolean;
}
const props = withDefaults(defineProps<Props>(), {
    disabled: false,
});

const emits = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();
</script>

<template>
    <Select :model-value="props.modelValue" @update:modelValue="emits('update:modelValue', $event)" :disabled="props.disabled">
        <SelectTrigger class="w-[180px]">
            <SelectValue :placeholder="$t('Select a role')">
                {{ props.modelValue ? $t(`roles.${props.modelValue}.name`) : $t('Select a role') }}
            </SelectValue>
        </SelectTrigger>
        <SelectContent>
            <SelectGroup>
                <SelectItem v-for="role of props.roles" :key="role.key" :value="role.key">
                    <div class="flex flex-col items-start">
                        <span>{{ $t(`roles.${role.key}.name`) }}</span>
                        <span class="text-muted-foreground">{{ $t(`roles.${role.key}.description`) }}</span>
                    </div>
                </SelectItem>
            </SelectGroup>
        </SelectContent>
    </Select>
</template>
