@props([
    'title' => 'Donor Portal',
    'description' => null,
    'navItems' => null,
])

@php
    $navItems = $navItems ?? [
        ['label' => 'Overview', 'route' => 'donor.dashboard', 'pattern' => 'donor.dashboard'],
        ['label' => 'Donations', 'route' => 'donor.donations.index', 'pattern' => 'donor.donations.*'],
        ['label' => 'Receipts', 'route' => 'donor.receipts.index', 'pattern' => 'donor.receipts.*'],
    ];
@endphp

<x-portal-shell
    :title="$title"
    :description="$description"
    portal-label="Donor Portal"
    home-route="donor.dashboard"
    badge-variant="warning"
    :nav-items="$navItems"
>
    {{ $slot }}
</x-portal-shell>
