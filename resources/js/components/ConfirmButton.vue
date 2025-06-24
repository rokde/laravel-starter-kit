<script setup lang="ts">
import {
    AlertDialog,
    AlertDialogAction,
    AlertDialogCancel,
    AlertDialogContent,
    AlertDialogDescription,
    AlertDialogFooter,
    AlertDialogHeader,
    AlertDialogTitle,
    AlertDialogTrigger,
} from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Trash, Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

const props = withDefaults(
    defineProps<{
        label?: string;
        title: string;
        confirmation: string;
        cancelLabel?: string;
        submitLabel?: string;
        as?: 'link' | 'icon' | 'invisible';
        withoutConfirmation?: boolean;
        disabled?: boolean;
    }>(),
    {
        label: 'Delete',
        cancelLabel: 'Cancel',
        submitLabel: 'Delete',
        as: 'link',
        withoutConfirmation: false,
        disabled: false,
    },
);

const emits = defineEmits<{
    confirmed: [];
}>();

const confirming = ref<boolean>(false);

defineExpose({ confirming });

const askForConfirmation = () => {
    if (props.withoutConfirmation) {
        confirmed();
        return;
    }

    confirming.value = true;
    window.addEventListener('keyup', enterKeyHandler);
};
const reject = () => {
    window.removeEventListener('keyup', enterKeyHandler);
    confirming.value = false;
};
const confirmed = () => {
    emits('confirmed');
    reject();
};

const enterKeyHandler = (event: KeyboardEvent) => {
    if (event.key === 'Enter') {
        event.preventDefault();
        event.stopPropagation();

        confirmed();
    }
    if (event.key === 'Escape') {
        event.preventDefault();
        event.stopPropagation();
        reject();
    }
};
</script>

<template>
    <AlertDialog v-model:open="confirming">
        <AlertDialogTrigger as-child>
            <Button
                v-if="props.as === 'link'"
                type="button"
                variant="link"
                class="text-red-500 hover:text-red-600 dark:text-red-700 dark:hover:text-red-600"
                :class="{ 'opacity-25': confirming }"
                :disabled="props.disabled || confirming"
                @click="askForConfirmation"
            >
                {{ $t(props.label) }}
            </Button>
            <Button
                v-else-if="props.as === 'icon'"
                type="button"
                variant="ghost"
                class="group text-red-300 hover:text-red-600 dark:text-red-800 dark:hover:text-red-600"
                :class="{ 'opacity-25': confirming }"
                :disabled="props.disabled || confirming"
                :title="$t(props.label)"
                @click="askForConfirmation"
            >
                <Trash class="size-4 group-hover:hidden" />
                <Trash2 class="hidden size-4 group-hover:block" />
            </Button>
        </AlertDialogTrigger>

        <AlertDialogContent>
            <AlertDialogHeader>
                <AlertDialogTitle>{{ props.title }}</AlertDialogTitle>
                <AlertDialogDescription>{{ props.confirmation }}</AlertDialogDescription>
            </AlertDialogHeader>
            <AlertDialogFooter>
                <AlertDialogCancel>{{ $t('Cancel') }}</AlertDialogCancel>
                <AlertDialogAction @click="confirmed">{{ $t(props.submitLabel) }}</AlertDialogAction>
            </AlertDialogFooter>
        </AlertDialogContent>
    </AlertDialog>
</template>
