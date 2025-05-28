<script setup lang="ts">
import { Button } from '@/components/ui/button';
import { useForm } from '@inertiajs/vue3';
import { browserSupportsWebAuthn, startAuthentication } from '@simplewebauthn/browser';
import { ref } from 'vue';

const submitting = ref<boolean>(false);

const signInWithPasskey = async () => {
    submitting.value = true;

    const response = await fetch(route('passkeys.authentication_options'));

    const options = await response.json();

    let startAuthenticationResponse;
    try {
        startAuthenticationResponse = await startAuthentication({ optionsJSON: options });
    } catch (e) {
        console.error(e);
        submitting.value = false;
        return;
    }

    const form = useForm({
        start_authentication_response: JSON.stringify(startAuthenticationResponse),
    });

    form.post(route('passkeys.login'), {
        onFinish: () => (submitting.value = false),
    });
};
</script>

<template>
    <Button v-if="browserSupportsWebAuthn()" type="button" variant="ghost" :disabled="submitting" @click="signInWithPasskey">
        {{ $t('passkeys::passkeys.authenticate_using_passkey') }}
    </Button>
</template>
