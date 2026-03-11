@props([
    'label' => null,
    'value' => null,
    'meta' => null,
])

<div {{ $attributes->class(['ui-stat-card']) }}>
    @if ($label)
        <div class="ui-stat-label">{{ $label }}</div>
    @endif

    <div class="ui-stat-value">{{ $value }}</div>

    @if ($meta)
        <div class="ui-stat-meta">{{ $meta }}</div>
    @endif
</div>
