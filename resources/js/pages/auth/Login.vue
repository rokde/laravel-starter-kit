<script setup lang="ts">
import AuthBase from '@/layouts/AuthLayout.vue';
import LoginForm from '@/pages/auth/components/LoginForm.vue';
import { Head } from '@inertiajs/vue3';

defineProps<{
    status?: string;
    canResetPassword: boolean;
}>();
</script>

<template>
    <AuthBase :title="$t('Log in to your account')" :description="$t('Enter your email and password below to log in')">
        <Head :title="$t('Log in')" />

        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ $t(status) }}
        </div>

        <LoginForm :can-reset-password="canResetPassword" can-register />
    </AuthBase>
</template>
