@props([
    'title' => 'Nothing to show yet',
    'description' => 'This section will populate once records are available for the current account and scope.',
])

<div {{ $attributes->class(['ui-empty-state']) }}>
    <div class="ui-empty-state__icon" aria-hidden="true">
        <svg class="h-6 w-6" viewBox="0 0 20 20" fill="currentColor">
            <path fill-rule="evenodd" d="M3 4a2 2 0 012-2h2.586A2 2 0 019 2.586l.707.707A2 2 0 0011.121 4H15a2 2 0 012 2v2.5a1 1 0 11-2 0V6H5v8h4.5a1 1 0 110 2H5a2 2 0 01-2-2V4zm10.293 8.293a1 1 0 011.414 0L16 13.586l1.293-1.293a1 1 0 111.414 1.414L17.414 15l1.293 1.293a1 1 0 01-1.414 1.414L16 16.414l-1.293 1.293a1 1 0 01-1.414-1.414L14.586 15l-1.293-1.293a1 1 0 010-1.414z" clip-rule="evenodd" />
        </svg>
    </div>

    <h3 class="ui-empty-state__title">{{ $title }}</h3>
    <p class="ui-empty-state__description">{{ $description }}</p>

    @isset($actions)
        <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
            {{ $actions }}
        </div>
    @endisset
</div>
