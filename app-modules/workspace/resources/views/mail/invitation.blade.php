<x-mail::message>
    {{ __('You have been invited to join the :workspace workspace!', ['workspace' => $invitation->workspace->name]) }}

    {{ __('If you do not have an account, it will be created by accepting the invitation.') }}

    <x-mail::button :url="$acceptUrl">
        {{ __('Accept Invitation') }}
    </x-mail::button>

    {{ __('If you did not expect to receive an invitation to this workspace, you may discard this email.') }}
</x-mail::message>
