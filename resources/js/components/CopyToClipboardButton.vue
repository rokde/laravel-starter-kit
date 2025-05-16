<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { ClipboardCheck, ClipboardCopy } from 'lucide-vue-next';
import { ref } from 'vue';

interface Props {
    text: string;
}

const props = defineProps<Props>();
const copied = ref<boolean>(false);

const copy = async () => {
    const textToCopy = props.text;

    if (navigator.clipboard && window.isSecureContext) {
        await navigator.clipboard.writeText(textToCopy);
        copied.value = true;
    } else {
        // Use the 'out of viewport hidden text area' trick
        const textArea = document.createElement('textarea');
        textArea.value = textToCopy;

        // Move textarea out of the viewport so it's not visible
        textArea.style.opacity = '0';
        textArea.style.position = 'absolute';
        textArea.style.left = '-999999px';

        document.body.prepend(textArea);
        textArea.select();

        try {
            document.execCommand('copy');
            copied.value = true;
        } catch (error) {
            console.error(error);
        } finally {
            textArea.remove();
        }
    }

    setInterval(() => (copied.value = false), 3000);
};
</script>

<template>
    <Button @click.prevent.stop="copy" variant="ghost">
        <ClipboardCheck v-if="copied" class="size-4" />
        <ClipboardCopy v-else class="size-4" />
    </Button>
</template>
