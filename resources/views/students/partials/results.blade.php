@php
    $total = method_exists($students, 'total') ? $students->total() : $students->count();

    $hasFilter =
        request()->filled('academic_year_id') ||
        request()->filled('class_id') ||
        request()->filled('section_id') ||
        (request()->has('is_boarding') && request('is_boarding') !== '') ||
        request()->filled('search');

    // Transaction Center route (safe)
    $txCenterUrl = null;
    if (\Illuminate\Support\Facades\Route::has('transactions.center')) {
        $txCenterUrl = route('transactions.center');
    } elseif (\Illuminate\Support\Facades\Route::has('transaction-center.index')) {
        $txCenterUrl = route('transaction-center.index');
    }
@endphp

<div class="px-1 pb-3 text-xs text-slate-500 dark:text-slate-400">
    Total: <span class="font-semibold">{{ $total }}</span>
    @if (method_exists($students, 'firstItem') && $students->firstItem())
        • Showing {{ $students->firstItem() }}–{{ $students->lastItem() }}
    @endif

    @if ($hasFilter)
        <span
            class="ml-2 inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700
                   dark:border-emerald-900/40 dark:bg-emerald-900/20 dark:text-emerald-200">
            Filters Applied
        </span>
    @endif
</div>

<div
    class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead
                class="sticky top-0 z-10 bg-slate-50/95 dark:bg-slate-800/95 backdrop-blur border-b border-slate-200 dark:border-white/10">
                <tr class="text-xs uppercase tracking-wide text-slate-600 dark:text-slate-200">
                    <th class="px-4 py-3 text-left whitespace-nowrap">ID</th>
                    <th class="px-4 py-3 text-left whitespace-nowrap">Student</th>
                    <th class="px-4 py-3 text-left whitespace-nowrap">Roll</th>
                    <th class="px-4 py-3 text-left whitespace-nowrap">Class</th>
                    <th class="px-4 py-3 text-left whitespace-nowrap">Section</th>
                    <th class="px-4 py-3 text-left whitespace-nowrap">Mobile</th>
                    <th class="px-4 py-3 text-center whitespace-nowrap">Boarding</th>
                    <th class="px-4 py-3 text-center whitespace-nowrap">Status</th>
                    <th class="px-4 py-3 text-right whitespace-nowrap">Action</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                @forelse ($students as $student)
                    @php
                        $displayName = $student->full_name ?: 'Student #' . $student->id;
                        $yearLabel = $student->academicYear->academic_years ?? ($student->academicYear->year ?? '-');
                        $isActive = (bool) ($student->isActived ?? true);
                    @endphp

                    <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/60 transition">
                        <td class="px-4 py-3 text-slate-600 dark:text-slate-300">#{{ $student->id }}</td>

                        <td class="px-4 py-3">
                            <div class="font-semibold text-slate-900 dark:text-slate-100">{{ $displayName }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ $yearLabel }}</div>
                        </td>

                        <td class="px-4 py-3 text-slate-700 dark:text-slate-200">
                            <span
                                class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-2 py-1 text-xs">
                                {{ $student->roll ?? '-' }}
                            </span>
                        </td>

                        <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $student->class->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $student->section->name ?? '-' }}
                        </td>
                        <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $student->mobile ?? '-' }}</td>

                        <td class="px-4 py-3 text-center">
                            @if ((int) ($student->is_boarding ?? 0) === 1)
                                <span
                                    class="inline-flex rounded-full bg-emerald-50 text-emerald-800 px-2.5 py-1 text-xs font-semibold
                                             dark:bg-emerald-900/20 dark:text-emerald-200 dark:border dark:border-emerald-900/40">
                                    Boarding
                                </span>
                            @else
                                <span
                                    class="inline-flex rounded-full bg-slate-100 text-slate-700 px-2.5 py-1 text-xs font-semibold
                                             dark:bg-slate-800 dark:text-slate-200">
                                    No
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3 text-center">
                            @if ($isActive)
                                <span
                                    class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800
                                             dark:border-emerald-900/40 dark:bg-emerald-900/20 dark:text-emerald-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span> Active
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center gap-1 rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-800
                                             dark:border-rose-900/40 dark:bg-rose-900/20 dark:text-rose-200">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span> Inactive
                                </span>
                            @endif
                        </td>

                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2 flex-wrap">
                                @if (\Illuminate\Support\Facades\Route::has('students.show'))
                                    <a href="{{ route('students.show', $student) }}"
                                        class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                        View
                                    </a>
                                @endif

                                @if ($txCenterUrl)
                                    <a href="{{ $txCenterUrl }}?student_id={{ $student->id }}"
                                        class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                        Fee/Tx
                                    </a>
                                @endif

                                @if (\Illuminate\Support\Facades\Route::has('students.edit'))
                                    <a href="{{ route('students.edit', $student) }}"
                                        class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                        Edit
                                    </a>
                                @endif

                                @if (\Illuminate\Support\Facades\Route::has('students.destroy'))
                                    <form action="{{ route('students.destroy', $student) }}" method="POST"
                                        onsubmit="return confirm('This will archive the student if transactions exist. Continue?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="inline-flex items-center rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100
                                                       dark:border-rose-900/40 dark:bg-rose-900/20 dark:text-rose-200 dark:hover:bg-rose-900/30 transition">
                                            Delete
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="px-4 py-12 text-center text-slate-500 dark:text-slate-400">
                            <div class="space-y-2">
                                <p>No students found.</p>
                                @if ($hasFilter)
                                    <a href="{{ route('students.index') }}"
                                        class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition">
                                        Clear filters
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if (method_exists($students, 'links'))
        <div class="border-t border-slate-200 dark:border-white/10 bg-slate-50/60 dark:bg-slate-800/40 px-4 py-3">
            {{ $students->links() }}
        </div>
    @endif
</div>
