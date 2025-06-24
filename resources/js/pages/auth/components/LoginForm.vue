<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import RevealPassword from '@/components/RevealPassword.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { getI18n } from '@/i18n';
import { useForm } from '@inertiajs/vue3';
import SignInWithPasskey from '@passkey/components/SignInWithPasskey.vue';
import { LoaderCircle } from 'lucide-vue-next';

const { t } = getI18n();

interface Props {
    canResetPassword: boolean;
    canRegister?: boolean;
    submitLabel?: string;
}

const props = withDefaults(defineProps<Props>(), {
    canRegister: false,
    submitLabel: t('Log in'),
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="flex flex-col gap-6">
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="email">{{ $t('Email address') }}</Label>
                <Input
                    id="email"
                    type="email"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="email"
                    v-model="form.email"
                    placeholder="email@example.com"
                />
                <InputError :message="form.errors.email" />
            </div>

            <div class="grid gap-2">
                <div class="flex items-center justify-between">
                    <Label for="password">{{ $t('Password') }}</Label>
                    <TextLink v-if="props.canResetPassword" :href="route('password.request')" class="text-sm" :tabindex="5">
                        {{ $t('Forgot password?') }}
                    </TextLink>
                </div>
                <div class="with-revealer">
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="2"
                        autocomplete="current-password"
                        v-model="form.password"
                        :placeholder="$t('Password')"
                    />
                    <RevealPassword ref-id="password" />
                </div>
                <InputError :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <Label for="remember" class="flex items-center space-x-3">
                    <Checkbox id="remember" v-model="form.remember" :tabindex="3" />
                    <span>{{ $t('Remember me') }}</span>
                </Label>
            </div>

            <Button type="submit" class="mt-4 w-full" :tabindex="4" :disabled="form.processing" data-pan="login">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                {{ props.submitLabel }}
            </Button>

            <SignInWithPasskey />
        </div>

        <div v-if="props.canRegister" class="text-muted-foreground text-center text-sm">
            {{ $t("Don't have an account?") }}
            <TextLink :href="route('register')" :tabindex="5">{{ $t('Sign up') }}</TextLink>
        </div>
    </form>
</template>
