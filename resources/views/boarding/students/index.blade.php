@php
    $pageTitle = 'Boarding Students';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="text-xl font-semibold text-slate-800 dark:text-slate-100">{{ $pageTitle }}</div>
                <div class="text-xs text-slate-500 dark:text-slate-400">Auto list: is_boarding = true</div>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                    Dashboard
                </a>

                @if(!empty($txCenterUrl))
                    <a href="{{ $txCenterUrl }}"
                       class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                        Transaction Center
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Filters --}}
            <div class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 p-4 shadow-sm">
                <form method="GET" class="grid grid-cols-1 sm:grid-cols-5 gap-3">
                    <div>
                        <label class="block text-xs text-slate-500 dark:text-slate-400">Academic Year</label>
                        <select name="academic_year_id"
                                class="w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All</option>
                            @foreach($years as $y)
                                @php $label = $y->year ?? $y->name ?? $y->title ?? ('Year #'.$y->id); @endphp
                                <option value="{{ $y->id }}" @selected((string)request('academic_year_id') === (string)$y->id)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-slate-500 dark:text-slate-400">Class</label>
                        <select name="class_id"
                                class="w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All</option>
                            @foreach($classes as $c)
                                <option value="{{ $c->id }}" @selected((string)request('class_id') === (string)$c->id)>{{ $c->name ?? $c->class_name ?? ('Class #'.$c->id) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-slate-500 dark:text-slate-400">Section</label>
                        <select name="section_id"
                                class="w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All</option>
                            @foreach($sections as $s)
                                <option value="{{ $s->id }}" @selected((string)request('section_id') === (string)$s->id)>{{ $s->name ?? $s->section_name ?? ('Section #'.$s->id) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-xs text-slate-500 dark:text-slate-400">Search</label>
                        <div class="flex gap-2">
                            <input type="text" name="search" value="{{ request('search') }}"
                                   placeholder="name / mobile / roll"
                                   class="w-full rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-sm text-slate-900 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <button class="rounded-xl bg-emerald-600 px-4 py-2 text-sm font-semibold text-white hover:bg-emerald-500">
                                Apply
                            </button>
                            <a href="{{ route('boarding.students.index') }}"
                               class="rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Table --}}
            <div class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 shadow-sm overflow-hidden">
                <div class="px-4 py-3 border-b border-slate-200 dark:border-white/10 flex items-center justify-between">
                    <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                        Boarding Students ({{ $students->total() }})
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">
                        Only active + not deleted + is_boarding=true
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-800 text-slate-600 dark:text-slate-200 text-xs uppercase">
                        <tr>
                            <th class="px-4 py-3 text-left">Student</th>
                            <th class="px-4 py-3 text-left">Academic</th>
                            <th class="px-4 py-3 text-left">Boarding</th>
                            <th class="px-4 py-3 text-right">Action</th>
                        </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100 dark:divide-white/10">
                        @forelse($students as $st)
                            <tr class="hover:bg-slate-50/70 dark:hover:bg-slate-800/60">
                                <td class="px-4 py-3">
                                    <div class="font-semibold text-slate-900 dark:text-slate-100">
                                        {{ $st->full_name ?? ('Student #'.$st->id) }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        ID: #{{ $st->id }} • Roll: {{ $st->roll ?? '-' }} • {{ $st->mobile ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="text-slate-900 dark:text-slate-100">
                                        {{ $st->academicYear->year ?? $st->academicYear->academic_years ?? '-' }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        {{ $st->class->name ?? '-' }} • {{ $st->section->name ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-4 py-3">
                                    <div class="text-slate-900 dark:text-slate-100">
                                        Start: {{ $st->boarding_start_date ?? '-' }}
                                    </div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">
                                        End: {{ $st->boarding_end_date ?? '-' }}
                                    </div>
                                </td>

                                <td class="px-4 py-3 text-right whitespace-nowrap">
                                    <a href="{{ route('students.show', $st) }}"
                                       class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                                        Profile
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-10 text-center text-slate-500 dark:text-slate-400">
                                    No boarding students found.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="border-t border-slate-200 dark:border-white/10 bg-slate-50/60 dark:bg-slate-800/40 px-4 py-3">
                    {{ $students->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>