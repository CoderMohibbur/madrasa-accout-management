<?php

use App\Http\Controllers\Management\ManagementReportingController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('management.access-control'))->name('dashboard');

Route::view('/access-control', 'portals.foundation', [
    'title' => 'Management Access Control',
    'description' => 'Phase 1 established dedicated management routing, role middleware, and the schema foundation for guardian and donor identity linking. Phase 4 now adds a separate additive reporting surface under this protected namespace.',
    'highlights' => [
        'Single users table and single web guard remain unchanged.',
        'Future guardian and donor assignments will be routed through this protected namespace.',
        'Management reporting now has an additive route under `/management/reporting` while legacy `reports.*` routes remain unchanged.',
        'Legacy management route names outside this namespace remain untouched.',
    ],
])->name('access-control');

Route::get('/reporting', [ManagementReportingController::class, 'index'])->name('reporting.index');
