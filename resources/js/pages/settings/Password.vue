<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

import HeadingSmall from '@/components/HeadingSmall.vue';
import RevealPassword from '@/components/RevealPassword.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { getI18n } from '@/i18n';
import { type BreadcrumbItem } from '@/types';

const { t } = getI18n();

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

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: t('Password settings'),
        href: '/settings/password',
    },
];

const passwordInput = ref<HTMLInputElement | null>(null);
const currentPasswordInput = ref<HTMLInputElement | null>(null);

const displayRules = ref<boolean>(false);

const form = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
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

const updatePassword = () => {
    form.put(route('password.update'), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
        onError: (errors: any) => {
            if (errors.password) {
                form.reset('password', 'password_confirmation');
                if (passwordInput.value instanceof HTMLInputElement) {
                    passwordInput.value.focus();
                }
            }

            if (errors.current_password) {
                form.reset('current_password');
                if (currentPasswordInput.value instanceof HTMLInputElement) {
                    currentPasswordInput.value.focus();
                }
            }
        },
    });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="$t('Password settings')" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    :title="$t('Update password')"
                    :description="$t('Ensure your account is using a long, random password to stay secure')"
                />

                <form @submit.prevent="updatePassword" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="current_password">{{ $t('Current password') }}</Label>
                        <div class="with-revealer">
                            <Input
                                id="current_password"
                                ref="currentPasswordInput"
                                v-model="form.current_password"
                                type="password"
                                class="block w-full"
                                autocomplete="current-password"
                                :placeholder="$t('Current password')"
                            />
                            <RevealPassword ref-id="current_password" />
                        </div>
                        <InputError :message="form.errors.current_password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password">{{ $t('New password') }}</Label>
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
                                ref="passwordInput"
                                v-model="form.password"
                                type="password"
                                class="block w-full"
                                autocomplete="new-password"
                                @focus="displayRules = true"
                                :placeholder="$t('New password')"
                            />
                            <RevealPassword ref-id="password" />
                        </div>
                        <InputError :message="form.errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="password_confirmation">{{ $t('Confirm Password') }}</Label>
                        <div class="with-revealer">
                            <Input
                                id="password_confirmation"
                                v-model="form.password_confirmation"
                                type="password"
                                class="block w-full"
                                autocomplete="new-password"
                                :placeholder="$t('Confirm Password')"
                            />
                            <RevealPassword ref-id="password_confirmation" />
                        </div>
                        <InputError :message="form.errors.password_confirmation" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">{{ $t('Save') }}</Button>

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
            </div>
        </SettingsLayout>
    </AppLayout>
</template>
