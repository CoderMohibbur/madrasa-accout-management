@props([
    'items' => [],
    'soft' => false,
    'numbered' => false,
])

<div {{ $attributes->class(['ui-checklist', 'ui-checklist--soft' => $soft]) }}>
    @foreach ($items as $index => $item)
        @php
            $title = is_array($item)
                ? ($item['title'] ?? $item['label'] ?? $item['text'] ?? '')
                : (string) $item;
            $description = is_array($item) ? ($item['description'] ?? $item['meta'] ?? null) : null;
            $icon = $numbered ? str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) : '✓';
        @endphp

        <div class="ui-checklist__item">
            <span class="ui-checklist__icon" aria-hidden="true">{{ $icon }}</span>

            <div class="ui-checklist__content">
                <div class="ui-checklist__title">{{ $title }}</div>

                @if ($description)
                    <div class="ui-checklist__description">{{ $description }}</div>
                @endif
            </div>
        </div>
    @endforeach
</div>
