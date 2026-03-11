@props(['active'])

@php
$classes = ($active ?? false)
            ? 'inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-4 py-2 text-sm font-semibold text-emerald-800 transition focus:outline-none focus:ring-2 focus:ring-emerald-500'
            : 'inline-flex items-center rounded-full border border-transparent px-4 py-2 text-sm font-medium text-slate-600 transition hover:border-slate-200 hover:bg-white hover:text-slate-950 focus:outline-none focus:ring-2 focus:ring-emerald-500';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
