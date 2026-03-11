<x-app-layout>
    <x-slot name="header">
        <div>
            <p class="ui-page-kicker">Shared account settings</p>
            <h1 class="ui-page-title">{{ __('Profile settings') }}</h1>
            <p class="ui-page-description">
                Manage shared account details, contact verification, password security, and Google sign-in on the same identity.
            </p>
        </div>
    </x-slot>

    <div class="ui-container py-8 sm:py-10">
        <div class="space-y-6">
            <x-ui.card>
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="max-w-xl">
                    @include('profile.partials.google-link')
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="max-w-xl">
                    @include('profile.partials.delete-user-form')
                </div>
            </x-ui.card>
        </div>
    </div>
</x-app-layout>
