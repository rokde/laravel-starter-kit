<script lang="ts" setup>
import ConfirmButton from '@/components/ConfirmButton.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Label } from '@/components/ui/label';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head, router, useForm } from '@inertiajs/vue3';
import { Passkey, PasskeyOptions } from '@passkey/types';

import HeadingSmall from '@/components/HeadingSmall.vue';
import { Input } from '@/components/ui/input';
import { startRegistration } from '@simplewebauthn/browser';

const { t } = getI18n();

interface Props {
    passkeys: any;
    passkeyOptions: PasskeyOptions;
}

const props = defineProps<Props>();

const form = useForm({
    name: '',
    passkey: '',
});

const submit = async () => {
    const passkey = await startRegistration({ optionsJSON: props.passkeyOptions });
    form.passkey = JSON.stringify(passkey);

    form.post(route('settings.passkeys.store'));
};

const deletePasskey = (passkey: Passkey) => {
    router.delete(route('settings.passkeys.destroy', { passkey: passkey.id }));
};

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: t('passkeys::passkeys.passkeys'),
        href: route('settings.passkeys.edit'),
    },
];
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="$t('passkeys::passkeys.passkeys')" />

        <SettingsLayout>
            <div class="space-y-6">
                <HeadingSmall
                    :title="$t('passkeys::passkeys.passkeys')"
                    :description="$t('Update your account\'s passkeys to use a more secure way to login')"
                />

                <div class="mt-6">
                    <ul class="space-y-4">
                        <li
                            v-for="passkey in props.passkeys"
                            :key="passkey.id"
                            class="flex items-center justify-between rounded-lg bg-gray-100 p-4 shadow-sm"
                        >
                            <div class="text-gray-700">
                                {{ passkey.name }}
                            </div>
                            <div class="ml-2">
                                {{ $t('passkeys::passkeys.last_used') }}: {{ passkey.last_used_at ?? $t('passkeys::passkeys.not_used_yet') }}
                            </div>

                            <div>
                                <ConfirmButton
                                    :label="$t('passkeys::passkeys.delete')"
                                    :title="$t('Delete Passkey?')"
                                    :confirmation="$t('If you remove the passkey, you can not login again with the passkey.')"
                                    @confirmed="deletePasskey(passkey)"
                                />
                            </div>
                        </li>
                    </ul>
                </div>

                <form @submit.prevent="submit" class="space-y-6">
                    <div class="grid gap-2">
                        <Label for="name">{{ $t('passkeys::passkeys.name') }}</Label>
                        <Input id="name" v-model="form.name" class="block w-full" autocomplete="off" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <div class="flex items-center gap-4">
                        <Button :disabled="form.processing">{{ $t('passkeys::passkeys.create') }}</Button>

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
