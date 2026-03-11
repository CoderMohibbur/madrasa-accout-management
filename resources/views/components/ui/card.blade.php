@props([
    'title' => null,
    'description' => null,
    'soft' => false,
])

<section {{ $attributes->class(['ui-card', 'ui-card--soft' => $soft]) }}>
    @if ($title || $description || isset($headerActions))
        <div class="ui-card__header">
            <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
                <div>
                    @if ($title)
                        <h2 class="ui-card__title">{{ $title }}</h2>
                    @endif

                    @if ($description)
                        <p class="ui-card__description">{{ $description }}</p>
                    @endif
                </div>

                @isset($headerActions)
                    <div class="shrink-0">
                        {{ $headerActions }}
                    </div>
                @endisset
            </div>
        </div>
    @endif

    <div class="ui-card__body">
        {{ $slot }}
    </div>
</section>
