@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-start text-base font-semibold text-emerald-800 transition focus:outline-none focus:ring-2 focus:ring-emerald-500'
            : 'block w-full rounded-2xl border border-transparent px-4 py-3 text-start text-base font-medium text-slate-600 transition hover:border-slate-200 hover:bg-white hover:text-slate-950 focus:outline-none focus:ring-2 focus:ring-emerald-500';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
