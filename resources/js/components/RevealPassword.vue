<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { Eye, EyeOff } from 'lucide-vue-next';
import { onMounted, ref } from 'vue';

interface Props {
    refId: string;
}

const props = defineProps<Props>();

const passwordInput = ref<HTMLInputElement | null>(null);
const inputType = ref<'password' | 'text'>('password');

onMounted(() => {
    passwordInput.value = document.getElementById(props.refId);
    inputType.value = passwordInput.value?.getAttribute('type');
});

const toggleType = () => {
    if (passwordInput.value.getAttribute('type') === 'password') {
        passwordInput.value.setAttribute('type', 'text');
        inputType.value = 'text';
    } else {
        passwordInput.value.setAttribute('type', 'password');
        inputType.value = 'password';
    }
};
</script>

<template>
    <Button v-if="passwordInput" type="button" variant="secondary" @click="toggleType">
        <Eye v-if="inputType === 'password'" />
        <EyeOff v-else />
    </Button>
</template>

<style>
.with-revealer {
    @apply relative;
}
.with-revealer > input {
    padding-right: 3rem;
}
.with-revealer > button {
    @apply absolute rounded-l-none;
    height: 34px;
    top: 1px;
    right: 1px;
    bottom: 1px;
}
</style>
