@props([
    'eyebrow' => null,
    'title' => null,
    'description' => null,
    'centered' => false,
])

<div {{ $attributes->class(['ui-section-heading', 'ui-section-heading--centered' => $centered]) }}>
    <div class="space-y-3">
        @if ($eyebrow)
            <div class="ui-section-heading__eyebrow">{{ $eyebrow }}</div>
        @endif

        @if ($title)
            <h2 class="ui-section-heading__title">{{ $title }}</h2>
        @endif

        @if ($description)
            <p class="ui-section-heading__description">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="ui-section-heading__actions">
            {{ $actions }}
        </div>
    @endisset
</div>
