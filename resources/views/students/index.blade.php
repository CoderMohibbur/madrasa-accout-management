@php
    $total = method_exists($students, 'total') ? $students->total() : $students->count();

    // Transaction Center route (safe)
    $txCenterUrl = null;
    if (\Illuminate\Support\Facades\Route::has('transactions.center')) {
        $txCenterUrl = route('transactions.center');
    } elseif (\Illuminate\Support\Facades\Route::has('transaction-center.index')) {
        $txCenterUrl = route('transaction-center.index');
    }
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">
                    {{ __('Students') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Admission → List filter → Profile → Fee collection
                </p>
            </div>

            <div class="flex items-center gap-2">
                @if($txCenterUrl)
                    <a href="{{ $txCenterUrl }}"
                       class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                        Transaction Center
                    </a>
                @endif

                @if (\Illuminate\Support\Facades\Route::has('students.create'))
                    <a href="{{ route('students.create') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                        <span class="text-base leading-none">+</span>
                        {{ __('Admit Student') }}
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            <x-toast-success />

            {{-- Filters + Search (server-side) --}}
            <form method="GET" action="{{ route('students.index') }}"
                  class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 shadow-sm">
                <div class="p-4 sm:p-5 grid grid-cols-12 gap-3 items-end">

                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Academic Year</label>
                        <select name="academic_year_id"
                                class="w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-sm
                                       focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All</option>
                            @foreach(($academic_years ?? []) as $y)
                                @php $label = $y->academic_years ?? $y->year ?? ('Year #'.$y->id); @endphp
                                <option value="{{ $y->id }}" @selected(request('academic_year_id') == $y->id)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Class</label>
                        <select name="class_id"
                                class="w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-sm
                                       focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All</option>
                            @foreach(($classes ?? []) as $c)
                                <option value="{{ $c->id }}" @selected(request('class_id') == $c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Section</label>
                        <select name="section_id"
                                class="w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-sm
                                       focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All</option>
                            @foreach(($sections ?? []) as $s)
                                <option value="{{ $s->id }}" @selected(request('section_id') == $s->id)>{{ $s->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Boarding</label>
                        <select name="is_boarding"
                                class="w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-sm
                                       focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All</option>
                            <option value="1" @selected(request('is_boarding') === '1')>Boarding</option>
                            <option value="0" @selected(request('is_boarding') === '0')>Non-Boarding</option>
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-9">
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Search (name/roll/mobile)</label>
                        <div class="relative">
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Type name / roll / phone..."
                                   class="w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-10 py-2 text-sm
                                          text-slate-800 dark:text-slate-100 placeholder:text-slate-400
                                          focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔎</div>
                        </div>
                    </div>

                    <div class="col-span-12 sm:col-span-3 flex gap-2">
                        <button class="w-full rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                            Apply
                        </button>
                        <a href="{{ route('students.index') }}"
                           class="w-full text-center rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-4 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-800">
                            Reset
                        </a>
                    </div>

                </div>

                <div class="px-4 pb-4 text-xs text-slate-500 dark:text-slate-400">
                    Total: <span class="font-semibold">{{ $total }}</span>
                    @if(method_exists($students, 'firstItem') && $students->firstItem())
                        • Showing {{ $students->firstItem() }}–{{ $students->lastItem() }}
                    @endif
                </div>
            </form>

            {{-- Table --}}
            <div class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="sticky top-0 z-10 bg-slate-50/95 dark:bg-slate-800/95 backdrop-blur border-b border-slate-200 dark:border-white/10">
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
                                    $displayName = $student->full_name ?? ('Student #'.$student->id);
                                    $yearLabel = $student->academicYear->academic_years ?? $student->academicYear->year ?? '-';
                                @endphp

                                <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/60 transition">
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300">#{{ $student->id }}</td>

                                    <td class="px-4 py-3">
                                        <div class="font-semibold text-slate-900 dark:text-slate-100">
                                            {{ $displayName }}
                                        </div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ $yearLabel }}
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">
                                        <span class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-2 py-1 text-xs">
                                            {{ $student->roll ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $student->class->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $student->section->name ?? '-' }}</td>
                                    <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ $student->mobile ?? '-' }}</td>

                                    <td class="px-4 py-3 text-center">
                                        @if($student->is_boarding)
                                            <span class="inline-flex rounded-full bg-emerald-50 text-emerald-800 px-2.5 py-1 text-xs font-semibold
                                                         dark:bg-emerald-900/20 dark:text-emerald-200 dark:border dark:border-emerald-900/40">
                                                Boarding
                                            </span>
                                        @else
                                            <span class="inline-flex rounded-full bg-slate-100 text-slate-700 px-2.5 py-1 text-xs font-semibold
                                                         dark:bg-slate-800 dark:text-slate-200">
                                                No
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-center">
                                        @if ($student->isActived)
                                            <span class="inline-flex items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-800
                                                         dark:border-emerald-900/40 dark:bg-emerald-900/20 dark:text-emerald-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1 rounded-full border border-rose-200 bg-rose-50 px-2.5 py-1 text-xs font-semibold text-rose-800
                                                         dark:border-rose-900/40 dark:bg-rose-900/20 dark:text-rose-200">
                                                <span class="h-1.5 w-1.5 rounded-full bg-rose-500"></span>
                                                Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-end gap-2 flex-wrap">
                                            <a href="{{ route('students.show', $student) }}"
                                               class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                                                View
                                            </a>

                                            @if($txCenterUrl)
                                                <a href="{{ $txCenterUrl }}?student_id={{ $student->id }}"
                                                   class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                                                    Fee/Tx
                                                </a>
                                            @endif

                                            <a href="{{ route('students.edit', $student) }}"
                                               class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                                                Edit
                                            </a>

                                            <form action="{{ route('students.destroy', $student) }}" method="POST"
                                                  onsubmit="return confirm('This will archive the student if transactions exist. Continue?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-100
                                                               dark:border-rose-900/40 dark:bg-rose-900/20 dark:text-rose-200 dark:hover:bg-rose-900/30">
                                                    Delete
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="px-4 py-12 text-center text-slate-500 dark:text-slate-400">
                                        No students found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if(method_exists($students, 'links'))
                    <div class="border-t border-slate-200 dark:border-white/10 bg-slate-50/60 dark:bg-slate-800/40 px-4 py-3">
                        {{ $students->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>