<script setup lang="ts">
import DatePicker from '@/components/DatePicker.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { getI18n } from '@/i18n';
import { useForm } from '@inertiajs/vue3';

const { t } = getI18n();

interface User {
    id: number;
    name: string;
    email: string;
}

interface Props {
    workspaceUsers: User[];
    presets: Array<{ value: string; label: string }>;
}

const props = defineProps<Props>();

const emits = defineEmits<{
    (e: 'submitted'): void;
}>();

const form = useForm({
    title: '',
    user_id: props.workspaceUsers.length > 0 ? props.workspaceUsers[0].id : '',
    completed: false,
    due_date: '',
});

const submit = () => {
    form.post(route('todos.store'), {
        preserveScroll: true,
        onSuccess: () => emits('submitted'),
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="w-full space-y-6">
        <div class="grid gap-2">
            <Label for="title">{{ $t('Title') }}</Label>
            <Input id="title" class="mt-1 block w-full" v-model="form.title" required autocomplete="off" :placeholder="$t('Todo title')" autofocus />
            <InputError class="mt-2" :message="form.errors.title" />
        </div>

        <div v-if="props.workspaceUsers.length > 1" class="grid gap-2">
            <Label for="user_id">{{ $t('Assign to') }}</Label>
            <Select v-model="form.user_id">
                <SelectTrigger class="w-full">
                    <SelectValue :placeholder="$t('Select a user')" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="user in props.workspaceUsers" :key="user.id" :value="user.id"> {{ user.name }} ({{ user.email }}) </SelectItem>
                </SelectContent>
            </Select>
            <InputError class="mt-2" :message="form.errors.user_id" />
        </div>

        <div class="grid gap-2">
            <Label for="due_date">{{ $t('Due date') }}</Label>
            <DatePicker v-model="form.due_date" :presets="props.presets" clearable />
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
</template>
