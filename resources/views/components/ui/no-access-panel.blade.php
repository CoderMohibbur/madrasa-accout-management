@props([
    'title' => 'Access is not available here',
    'description' => 'This account does not currently meet the requirements for this protected area.',
])

<div {{ $attributes->class(['ui-no-access']) }}>
    <div class="ui-no-access__icon" aria-hidden="true">
        <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M10 1a4 4 0 00-4 4v2H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2V9a2 2 0 00-2-2h-1V5a4 4 0 00-4-4zm2 6V5a2 2 0 10-4 0v2h4z" clip-rule="evenodd" />
        </svg>
    </div>

    <h3 class="ui-no-access__title">{{ $title }}</h3>
    <p class="ui-no-access__description">{{ $description }}</p>

    @isset($actions)
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            {{ $actions }}
        </div>
    @endisset
</div>
