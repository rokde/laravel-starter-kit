<script setup lang="ts">
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head } from '@inertiajs/vue3';
import RegisterForm from '@/pages/auth/components/RegisterForm.vue';

interface Props {
    appliedRules: {
        min: number | null;
        max: number | null;
        mixedCase: boolean;
        letters: boolean;
        numbers: boolean;
        symbols: boolean;
        uncompromised: boolean;
        compromisedThreshold: number;
        customRules: unknown[];
    };
}

const props = defineProps<Props>();
</script>

<template>
    <AuthBase :title="$t('Create an account')" :description="$t('Enter your details below to create your account')">
        <Head :title="$t('Sign up')" />

        <RegisterForm :applied-rules="props.appliedRules" can-login />
    </AuthBase>
</template>
