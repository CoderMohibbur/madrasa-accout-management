@props([
    'eyebrow' => null,
    'title' => null,
    'description' => null,
    'plain' => false,
])

<div {{ $attributes->class(['ui-page-header', 'ui-page-header--plain' => $plain]) }}>
    <div>
        @if ($eyebrow)
            <div class="ui-page-kicker">{{ $eyebrow }}</div>
        @endif

        @if ($title)
            <h1 class="ui-page-title">{{ $title }}</h1>
        @endif

        @if ($description)
            <p class="ui-page-description">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="flex flex-wrap items-center gap-3">
            {{ $actions }}
        </div>
    @endisset
</div>
