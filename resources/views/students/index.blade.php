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
                @if ($txCenterUrl)
                    <a href="{{ $txCenterUrl }}"
                        class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition
                              focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        Transaction Center
                    </a>
                @endif

                @if (\Illuminate\Support\Facades\Route::has('students.create'))
                    <a href="{{ route('students.create') }}"
                        class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition
                              focus:outline-none focus:ring-2 focus:ring-emerald-500">
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

            {{-- Filters + Search --}}
            <form id="studentsFilterForm" method="GET" action="{{ route('students.index') }}"
                class="rounded-3xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-900 shadow-sm">
                <div class="p-4 sm:p-5 grid grid-cols-12 gap-3 items-end">

                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Academic
                            Year</label>
                        <select name="academic_year_id"
                            class="w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-sm
                                       focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All</option>
                            @foreach ($academic_years ?? [] as $y)
                                @php $label = $y->academic_years ?? ($y->year ?? 'Year #' . $y->id); @endphp
                                <option value="{{ $y->id }}" @selected((string) request('academic_year_id') === (string) $y->id)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Class</label>
                        <select name="class_id"
                            class="w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-sm
                                       focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All</option>
                            @foreach ($classes ?? [] as $c)
                                <option value="{{ $c->id }}" @selected((string) request('class_id') === (string) $c->id)>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-3">
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Section</label>
                        <select name="section_id"
                            class="w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-sm
                                       focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All</option>
                            @foreach ($sections ?? [] as $s)
                                <option value="{{ $s->id }}" @selected((string) request('section_id') === (string) $s->id)>
                                    {{ $s->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-3">
                        <label
                            class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">Boarding</label>
                        <select name="is_boarding"
                            class="w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-sm
                                       focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <option value="">All</option>
                            <option value="1" @selected(request('is_boarding') === '1')>Boarding</option>
                            <option value="0" @selected(request('is_boarding') === '0')>Non-Boarding</option>
                        </select>
                    </div>

                    <div class="col-span-12 sm:col-span-9">
                        <label class="block text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">
                            Search (name / father / roll / mobile / email)
                        </label>
                        <div class="relative">
                            <input id="studentSearchInput" type="search" name="search"
                                value="{{ request('search') }}" autocomplete="off"
                                placeholder="Type name / father / roll / phone / email..."
                                class="w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-10 py-2 text-sm
                                          text-slate-800 dark:text-slate-100 placeholder:text-slate-400
                                          focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                            <div class="absolute left-3 top-1/2 -translate-y-1/2 text-slate-400">🔎</div>
                        </div>

                        <div id="liveSearchStatus" class="hidden mt-2 text-[11px] text-slate-500 dark:text-slate-400">
                            Searching...
                        </div>
                    </div>

                    <div class="col-span-12 sm:col-span-3 flex gap-2">
                        <button type="submit"
                            class="w-full rounded-2xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800 transition
                                       focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            Apply
                        </button>

                        <a href="{{ route('students.index') }}"
                            class="w-full text-center rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition
                                  focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            Reset
                        </a>
                    </div>

                </div>

                <div class="px-4 pb-4 text-xs text-slate-500 dark:text-slate-400">
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
            </form>

            {{-- ✅ Live Results Wrapper --}}
            <div id="studentsResultsWrap">
                @include('students.partials.results', ['students' => $students])
            </div>

        </div>
    </div>

    {{-- ✅ Live Search Script (typing + filter change + pagination ajax) --}}
    <script>
        (function() {
            const form = document.getElementById('studentsFilterForm');
            const input = document.getElementById('studentSearchInput');
            const wrap = document.getElementById('studentsResultsWrap');
            const status = document.getElementById('liveSearchStatus');

            if (!form || !wrap) return;

            const watchFields = Array.from(form.querySelectorAll('input, select')).filter(el => {
                return el.name && el.type !== 'submit' && el.type !== 'button';
            });

            let timer = null;
            let controller = null;

            function setLoading(on) {
                if (on) {
                    status?.classList.remove('hidden');
                    wrap.classList.add('opacity-60');
                } else {
                    status?.classList.add('hidden');
                    wrap.classList.remove('opacity-60');
                }
            }

            function buildUrl(explicitUrl = null) {
                const url = new URL(explicitUrl || form.action, window.location.origin);
                const fd = new FormData(form);

                // ✅ typing/filter change -> page reset
                if (!explicitUrl) fd.delete('page');

                for (const [k, v] of fd.entries()) {
                    const vv = (v ?? '').toString().trim();
                    if (vv !== '') url.searchParams.set(k, vv);
                    else url.searchParams.delete(k);
                }
                return url.toString();
            }

            async function fetchAndSwap(url) {
                if (controller) controller.abort();
                controller = new AbortController();

                setLoading(true);

                try {
                    const res = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        signal: controller.signal,
                    });

                    if (!res.ok) throw new Error('HTTP ' + res.status);

                    const html = await res.text();
                    wrap.innerHTML = html;

                    // ✅ keep URL synced
                    window.history.replaceState({}, '', url);

                    bindPagination();
                } catch (e) {
                    if (e.name !== 'AbortError') console.error(e);
                } finally {
                    setLoading(false);
                }
            }

            function debounceFetch(delay = 250) {
                clearTimeout(timer);
                timer = setTimeout(() => fetchAndSwap(buildUrl()), delay);
            }

            function bindPagination() {
                // Laravel pagination usually renders <nav role="navigation">
                const nav = wrap.querySelector('nav[role="navigation"]');
                if (!nav) return;

                nav.querySelectorAll('a[href]').forEach(a => {
                    if (a.dataset.ajaxBound) return;
                    a.dataset.ajaxBound = '1';

                    a.addEventListener('click', (ev) => {
                        ev.preventDefault();
                        const href = a.getAttribute('href');
                        if (!href) return;
                        fetchAndSwap(href);
                    });
                });
            }

            // ✅ initial
            bindPagination();

            // ✅ submit -> ajax
            form.addEventListener('submit', (ev) => {
                ev.preventDefault();
                fetchAndSwap(buildUrl());
            });

            // ✅ typing -> ajax (live)
            input?.addEventListener('input', () => debounceFetch(250));

            // ✅ select change -> ajax (instant)
            watchFields.forEach(el => {
                if (el === input) return;
                el.addEventListener('change', () => debounceFetch(0));
            });
        })();
    </script>
</x-app-layout>
