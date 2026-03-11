@props([
    'title' => 'Guardian Information',
    'description' => null,
])

@php
    $navItems = [
        ['label' => 'Overview', 'route' => 'guardian.info.dashboard', 'pattern' => 'guardian.info.dashboard'],
        ['label' => 'Institution', 'route' => 'guardian.info.institution', 'pattern' => 'guardian.info.institution'],
        ['label' => 'Admission', 'route' => 'guardian.info.admission', 'pattern' => 'guardian.info.admission'],
    ];
@endphp

<x-portal-shell
    :title="$title"
    :description="$description"
    portal-label="Guardian Information"
    home-route="guardian.info.dashboard"
    badge-variant="info"
    :nav-items="$navItems"
>
    {{ $slot }}
</x-portal-shell>
