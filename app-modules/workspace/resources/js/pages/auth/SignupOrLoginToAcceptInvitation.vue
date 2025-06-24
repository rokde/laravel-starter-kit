<script setup lang="ts">
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import AuthBase from '@/layouts/AuthLayout.vue';
import LoginForm from '@/pages/auth/components/LoginForm.vue';
import RegisterForm from '@/pages/auth/components/RegisterForm.vue';
import { AppliedPasswordRules } from '@/types';
import { Head } from '@inertiajs/vue3';

defineProps<{
    canResetPassword: boolean;
    appliedRules: AppliedPasswordRules;
}>();
</script>

<template>
    <AuthBase :title="$t('Accept invitation')" :description="$t('Accept the invitation to join the workspace.')">
        <Head :title="$t('Accept invitation')" />

        <Tabs default-value="login" class="w-[400px]">
            <TabsList>
                <TabsTrigger value="login">
                    {{ $t('Already have an account?') }}
                </TabsTrigger>
                <TabsTrigger value="register">
                    {{ $t("Don't have an account?") }}
                </TabsTrigger>
            </TabsList>
            <TabsContent value="login">
                <LoginForm :can-reset-password="canResetPassword" :submit-label="$t('Log in and accept invitation')" />
            </TabsContent>
            <TabsContent value="register">
                <RegisterForm :applied-rules="appliedRules" :submit-label="$t('Create account and accept invitation')" />
            </TabsContent>
        </Tabs>
    </AuthBase>
</template>
