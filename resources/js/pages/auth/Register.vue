<script setup lang="ts">
import AuthBase from '@/layouts/AuthLayout.vue';
import RegisterForm from '@/pages/auth/components/RegisterForm.vue';
import { AppliedPasswordRules } from '@/types';
import { Head } from '@inertiajs/vue3';

interface Props {
    appliedRules: AppliedPasswordRules;
}

const props = defineProps<Props>();
</script>

<template>
    <AuthBase :title="$t('Create an account')" :description="$t('Enter your details below to create your account')">
        <Head :title="$t('Sign up')" />

        <RegisterForm :applied-rules="props.appliedRules" can-login />
    </AuthBase>
</template>
