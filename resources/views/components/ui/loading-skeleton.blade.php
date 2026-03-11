@props([
    'lines' => 4,
    'showCard' => true,
])

<div {{ $attributes->class(['ui-loading-skeleton']) }} aria-hidden="true">
    @if ($showCard)
        <div class="ui-loading-bar h-5 w-40"></div>
        <div class="mt-3 ui-loading-bar w-72 max-w-full"></div>
    @endif

    <div class="mt-6 space-y-3">
        @for ($i = 0; $i < $lines; $i++)
            <div class="ui-loading-bar {{ $i === $lines - 1 ? 'w-2/3' : 'w-full' }}"></div>
        @endfor
    </div>
</div>
