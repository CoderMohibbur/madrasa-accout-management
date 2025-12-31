<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">
                    {{ __('Students') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Manage student records • edit • delete • status
                </p>
            </div>

            @if (\Illuminate\Support\Facades\Route::has('students.create'))
                <a href="{{ route('students.create') }}"
                   class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    <span class="text-base leading-none">+</span>
                    {{ __('Add Student') }}
                </a>
            @endif
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Toast --}}
            <x-toast-success />

            {{-- Top bar --}}
            <div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm">
                <div class="p-4 sm:p-5 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-2">
                        <div class="h-10 w-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center dark:bg-white dark:text-slate-900">
                            <span class="text-sm font-bold">S</span>
                        </div>
                        <div>
                            <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                                {{ __('Student List') }}
                            </div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">
                                Total: <span class="font-semibold">{{ $students->count() }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Search (front-end filter) --}}
                    <div class="w-full sm:w-80">
                        <div class="relative">
                            <input id="studentSearch" type="text" placeholder="Search by name, roll, phone..."
                                class="w-full rounded-2xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 px-10 py-2 text-sm text-slate-800 dark:text-slate-100 placeholder:text-slate-400 focus:border-slate-400 focus:ring-slate-200">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">
                                🔎
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="rounded-3xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="sticky top-0 z-10 bg-slate-50/95 dark:bg-slate-800/95 backdrop-blur border-b border-slate-200 dark:border-slate-700">
                            <tr class="text-xs uppercase tracking-wide text-slate-600 dark:text-slate-200">
                                <th class="px-4 py-3 text-left whitespace-nowrap">ID</th>
                                <th class="px-4 py-3 text-left whitespace-nowrap">Student</th>
                                <th class="px-4 py-3 text-left whitespace-nowrap">DOB</th>
                                <th class="px-4 py-3 text-left whitespace-nowrap">Roll</th>
                                <th class="px-4 py-3 text-left whitespace-nowrap">Contact</th>
                                <th class="px-4 py-3 text-center whitespace-nowrap">Age</th>
                                <th class="px-4 py-3 text-right whitespace-nowrap">Scholarship</th>
                                <th class="px-4 py-3 text-center whitespace-nowrap">Status</th>
                                <th class="px-4 py-3 text-right whitespace-nowrap">Action</th>
                            </tr>
                        </thead>

                        <tbody id="studentTableBody" class="divide-y divide-slate-100 dark:divide-slate-800">
                            @forelse ($students as $student)
                                @php
                                    $name = $student->full_name
                                        ?: trim(($student->first_name ?? '').' '.($student->last_name ?? ''));

                                    $email = $student->email ?: '-';
                                    $mobile = $student->mobile ?: '-';
                                @endphp

                                <tr class="student-row hover:bg-slate-50/70 dark:hover:bg-slate-800/60 transition
                                           odd:bg-white even:bg-slate-50/40 dark:odd:bg-slate-900 dark:even:bg-slate-900/50">
                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                        #{{ $student->id }}
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3 min-w-0">
                                            <div class="h-10 w-10 rounded-2xl bg-slate-900 text-white flex items-center justify-center shrink-0
                                                        dark:bg-white dark:text-slate-900">
                                                <span class="text-xs font-bold">
                                                    {{ strtoupper(mb_substr(trim($name ?: 'S'), 0, 1)) }}
                                                </span>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-semibold text-slate-900 dark:text-slate-100 truncate">
                                                    {{ $name ?: 'Student #'.$student->id }}
                                                </div>
                                                <div class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                                    {{ $student->first_name }} {{ $student->last_name }}
                                                </div>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300 whitespace-nowrap">
                                        {{ $student->dob ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-slate-600 dark:text-slate-300">
                                        <span class="inline-flex items-center rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 px-2 py-1 text-xs">
                                            {{ $student->roll ?? '-' }}
                                        </span>
                                    </td>

                                    <td class="px-4 py-3">
                                        <div class="text-slate-700 dark:text-slate-200 truncate">{{ $mobile }}</div>
                                        <div class="text-xs text-slate-500 dark:text-slate-400 truncate">{{ $email }}</div>
                                    </td>

                                    <td class="px-4 py-3 text-center text-slate-600 dark:text-slate-300">
                                        {{ $student->age ?? '-' }}
                                    </td>

                                    <td class="px-4 py-3 text-right font-semibold text-slate-900 dark:text-slate-100">
                                        {{ number_format((float)($student->scholarship_amount ?? 0), 2) }}
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
                                        <div class="flex items-center justify-end gap-2">
                                            <a href="{{ route('students.edit', $student->id) }}"
                                               class="inline-flex items-center rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 px-3 py-2 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                                                Edit
                                            </a>

                                            <form action="{{ route('students.destroy', $student->id) }}" method="POST"
                                                  onsubmit="return confirm('Are you sure you want to delete this student?');">
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

                                    {{-- hidden search text --}}
                                    <td class="hidden student-search-text">
                                        {{ strtolower($name.' '.$student->roll.' '.$student->mobile.' '.$student->email) }}
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

                {{-- Footer --}}
                <div class="border-t border-slate-200 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/40 px-4 py-3">
                    <div class="text-xs text-slate-500 dark:text-slate-400">
                        Tip: Search works instantly (front-end). For server-side search/pagination, tell me — I’ll add it.
                    </div>
                </div>
            </div>

        </div>
    </div>

    {{-- Simple front-end search --}}
    <script>
        (function () {
            const input = document.getElementById('studentSearch');
            const rows  = Array.from(document.querySelectorAll('.student-row'));

            if (!input) return;

            input.addEventListener('input', function () {
                const q = (this.value || '').toLowerCase().trim();

                rows.forEach(row => {
                    const t = row.querySelector('.student-search-text')?.textContent || '';
                    row.style.display = t.includes(q) ? '' : 'none';
                });
            });
        })();
    </script>
</x-app-layout>
