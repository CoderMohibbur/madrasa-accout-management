@props([
    'variant' => 'neutral',
])

@php
    $variant = in_array($variant, ['neutral', 'success', 'info', 'warning', 'danger'], true) ? $variant : 'neutral';
@endphp

<span {{ $attributes->class(['ui-badge', "ui-badge--{$variant}"]) }}>
    {{ $slot }}
</span>
