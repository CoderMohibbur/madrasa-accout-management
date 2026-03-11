@props([
    'title' => 'Guardian Portal',
    'description' => null,
])

@php
    $navItems = [
        ['label' => 'Overview', 'route' => 'guardian.dashboard', 'pattern' => 'guardian.dashboard'],
        ['label' => 'Invoices', 'route' => 'guardian.invoices.index', 'pattern' => 'guardian.invoices.*'],
        ['label' => 'Payment History', 'route' => 'guardian.history.index', 'pattern' => 'guardian.history.*'],
    ];
@endphp

<x-portal-shell
    :title="$title"
    :description="$description"
    portal-label="Guardian Portal"
    home-route="guardian.dashboard"
    badge-variant="success"
    :nav-items="$navItems"
>
    {{ $slot }}
</x-portal-shell>
