@props([
    'variant' => 'info',
    'title' => null,
])

@php
    $variant = in_array($variant, ['success', 'error', 'warning', 'info'], true) ? $variant : 'info';
@endphp

<div {{ $attributes->class(['ui-alert', "ui-alert--{$variant}"]) }} role="alert">
    <div class="ui-alert__icon" aria-hidden="true">
        @switch($variant)
            @case('success')
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M16.704 5.29a1 1 0 010 1.42l-7.29 7.29a1 1 0 01-1.414 0l-3-3a1 1 0 111.414-1.414l2.293 2.293 6.583-6.59a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
            @break

            @case('error')
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-10.293a1 1 0 00-1.414-1.414L10 8.586 7.707 6.293a1 1 0 10-1.414 1.414L8.586 10l-2.293 2.293a1 1 0 101.414 1.414L10 11.414l2.293 2.293a1 1 0 001.414-1.414L11.414 10l2.293-2.293z" clip-rule="evenodd" />
                </svg>
            @break

            @case('warning')
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.72-1.36 3.486 0l6.518 11.596c.75 1.334-.213 2.996-1.742 2.996H3.48c-1.53 0-2.492-1.662-1.742-2.996L8.257 3.1zM11 13a1 1 0 10-2 0 1 1 0 002 0zm-1-6a1 1 0 00-1 1v3a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
            @break

            @default
                <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M18 10A8 8 0 112 10a8 8 0 0116 0zm-7-3a1 1 0 10-2 0 1 1 0 002 0zm-1 3a1 1 0 00-.993.883L9 11v3a1 1 0 001.993.117L11 14v-3a1 1 0 00-1-1z" clip-rule="evenodd" />
                </svg>
        @endswitch
    </div>

    <div class="ui-alert__content">
        @if ($title)
            <div class="ui-alert__title">{{ $title }}</div>
        @endif

        <div>{{ $slot }}</div>
    </div>
</div>
