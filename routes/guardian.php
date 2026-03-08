<?php

use Illuminate\Support\Facades\Route;

Route::view('/', 'portals.foundation', [
    'title' => 'Guardian Portal Foundation',
    'description' => 'Phase 1 only establishes the protected route boundary and ownership-safe data model. Read-only guardian portal features land in Phase 2.',
    'highlights' => [
        'Guardian access is isolated behind the dedicated guardian role.',
        'Student visibility will be driven by guardian-student links and policies.',
        'No payment gateway behavior is active in this phase.',
    ],
])->name('dashboard');
