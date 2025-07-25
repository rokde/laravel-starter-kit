<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import RevealPassword from '@/components/RevealPassword.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { AppliedPasswordRules } from '@/types';
import { useForm } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { computed, ref } from 'vue';

const getUserTimeZone = (): string => {
    try {
        return Intl.DateTimeFormat().resolvedOptions().timeZone;
    } catch {
        return 'UTC';
    }
};

interface Props {
    appliedRules: AppliedPasswordRules;
    canLogin?: boolean;
    submitLabel?: string;
}

const props = withDefaults(defineProps<Props>(), {
    canLogin: false,
    submitLabel: 'Create account',
});

const displayRules = ref<boolean>(false);

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    locale: window.locale,
    timezone: getUserTimeZone(),
});

const checkMinRule = computed<boolean>(() => {
    if (props.appliedRules.min === null) return true;
    return form.password.length >= props.appliedRules.min;
});
const checkMaxRule = computed<boolean>(() => {
    if (props.appliedRules.max === null) return true;
    return form.password !== '' && form.password.length <= props.appliedRules.max;
});
const checkLettersRule = computed<boolean>(() => {
    if (props.appliedRules.letters === null) return true;
    return form.password.match(/[a-zA-Z]/) !== null;
});
const checkMixedCaseRule = computed<boolean>(() => {
    if (props.appliedRules.mixedCase === null) return true;
    return form.password.match(/[a-z]+.*[A-Z]+|[A-Z]+.*[a-z]/) !== null;
});
const checkNumbersRule = computed<boolean>(() => {
    if (props.appliedRules.numbers === null) return true;
    return form.password.match(/[0-9]/) !== null;
});
const checkSymbolsRule = computed<boolean>(() => {
    if (props.appliedRules.symbols === null) return true;
    return form.password.match(/[^a-zA-Z0-9\ ]/) !== null;
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <form @submit.prevent="submit" class="flex flex-col gap-6">
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="name">{{ $t('Name') }}</Label>
                <Input
                    id="name"
                    type="text"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="name"
                    v-model="form.name"
                    :placeholder="$t('Full name')"
                />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">{{ $t('Email address') }}</Label>
                <Input id="email" type="email" required :tabindex="2" autocomplete="email" v-model="form.email" placeholder="email@example.com" />
                <InputError :message="form.errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="password">{{ $t('Password') }}</Label>
                <div v-if="displayRules" class="text-xs">
                    <p>{{ $t('Password must contain the following') }}:</p>
                    <ul class="ml-5 list-disc">
                        <li v-if="props.appliedRules.min" :class="{ 'text-green-600 dark:text-green-400': checkMinRule }">
                            {{ $t('At least {min} characters', { min: props.appliedRules.min }) }}
                        </li>
                        <li v-if="props.appliedRules.max" :class="{ 'text-green-600 dark:text-green-400': checkMaxRule }">
                            {{ $t('Up to {max} characters', { max: props.appliedRules.max }) }}
                        </li>
                        <li v-if="props.appliedRules.letters" :class="{ 'text-green-600 dark:text-green-400': checkLettersRule }">
                            {{ $t('Includes at least one letter') }}
                        </li>
                        <li v-if="props.appliedRules.mixedCase" :class="{ 'text-green-600 dark:text-green-400': checkMixedCaseRule }">
                            {{ $t('Contains both uppercase and lowercase letters') }}
                        </li>
                        <li v-if="props.appliedRules.numbers" :class="{ 'text-green-600 dark:text-green-400': checkNumbersRule }">
                            {{ $t('Includes at least one number') }}
                        </li>
                        <li v-if="props.appliedRules.symbols" :class="{ 'text-green-600 dark:text-green-400': checkSymbolsRule }">
                            {{ $t('Contains at least one special character') }}
                        </li>
                    </ul>
                </div>
                <div class="with-revealer">
                    <Input
                        id="password"
                        type="password"
                        required
                        :tabindex="3"
                        autocomplete="new-password"
                        @focus="displayRules = true"
                        v-model="form.password"
                        :placeholder="$t('Password')"
                    />
                    <RevealPassword ref-id="password" />
                </div>
                <InputError :message="form.errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">{{ $t('Confirm password') }}</Label>
                <div class="with-revealer">
                    <Input
                        id="password_confirmation"
                        type="password"
                        required
                        :tabindex="4"
                        autocomplete="new-password"
                        v-model="form.password_confirmation"
                        :placeholder="$t('Confirm password')"
                    />
                    <RevealPassword ref-id="password_confirmation" />
                </div>
                <InputError :message="form.errors.password_confirmation" />
            </div>

            <Button type="submit" class="mt-2 w-full" tabindex="5" :disabled="form.processing" data-pan="register">
                <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                {{ $t(props.submitLabel) }}
            </Button>
        </div>

        <div class="text-muted-foreground text-center text-sm">
            {{ $t('agreement_start') }}
            <TextLink :href="route('static.terms')" :tabindex="6">{{ $t('Terms of Service') }}</TextLink>
            {{ $t('and') }}
            <TextLink :href="route('static.policy')" :tabindex="7">{{ $t('Privacy Policy') }}</TextLink>
            {{ $t('agreement_end') }}
        </div>

        <div v-if="props.canLogin" class="text-muted-foreground text-center text-sm">
            {{ $t('Already have an account?') }}
            <TextLink :href="route('login')" :tabindex="8">{{ $t('Log in') }}</TextLink>
        </div>
    </form>
</template>
