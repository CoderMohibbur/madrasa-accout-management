<div id="toast-notification" class="fixed right-5 top-5 z-50 flex w-full max-w-sm flex-col gap-3">
    @if (session('success'))
        <x-ui.alert variant="success">
            {{ session('success') }}
        </x-ui.alert>
    @endif

    @if (session('error'))
        <x-ui.alert variant="error">
            {{ session('error') }}
        </x-ui.alert>
    @endif
</div>
