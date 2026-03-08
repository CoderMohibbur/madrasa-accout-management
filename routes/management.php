<?php

use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('management.access-control'))->name('dashboard');

Route::view('/access-control', 'portals.foundation', [
    'title' => 'Management Access Control',
    'description' => 'Phase 1 established dedicated management routing, role middleware, and the schema foundation for guardian and donor identity linking.',
    'highlights' => [
        'Single users table and single web guard remain unchanged.',
        'Future guardian and donor assignments will be routed through this protected namespace.',
        'Legacy management route names outside this namespace remain untouched.',
    ],
])->name('access-control');
