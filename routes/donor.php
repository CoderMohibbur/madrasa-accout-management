<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'portals.foundation', [
    'title' => 'Donor Portal Foundation',
    'description' => 'Phase 1 creates the donor access boundary and donor-user linkage groundwork. Donation history and self-service flows stay for Phase 3.',
    'highlights' => [
        'Donor access is isolated behind the dedicated donor role.',
        'Donor user linkage is additive and does not alter legacy donor entry flows.',
        'Live payment initiation remains blocked until Phase 5 controls exist.',
    ],
])->name('dashboard');
