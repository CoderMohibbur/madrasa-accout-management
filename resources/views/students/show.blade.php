@php
    /** @var \App\Models\Student $student */
    $pageTitle = 'Student Profile';

    $name = $student->full_name ?: trim(($student->first_name ?? '').' '.($student->last_name ?? ''));
    $name = $name ?: ('Student #'.$student->id);

    $money = fn($n) => number_format((float)$n, 2);

    // Optional stats (controller থেকে পাঠালে দেখাবে)
    $feeTotalPaid   = (float) ($feeTotalPaid   ?? 0);
    $feeTotalDue    = (float) ($feeTotalDue    ?? 0);
    $feePaidInRange = (float) ($feePaidInRange ?? 0);
    $feeDueInRange  = (float) ($feeDueInRange  ?? 0);

    $recentStudentTx = $recentStudentTx ?? collect();
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('students.index') }}"
                       class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                        ← Back
                    </a>

                    <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                        {{ $pageTitle }}
                    </h2>
                </div>

                <p class="text-xs text-slate-500 mt-1">
                    View student details • fees • transactions
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('students.edit', $student->id) }}"
                   class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                    Edit Student
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Hero --}}
            <div class="rounded-3xl border border-slate-200 bg-gradient-to-r from-slate-900 to-slate-700 p-5 text-white shadow-sm">
                <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="h-12 w-12 rounded-2xl bg-white/10 border border-white/10 flex items-center justify-center shrink-0">
                            <span class="text-lg font-bold">
                                {{ strtoupper(mb_substr(trim($name), 0, 1)) }}
                            </span>
                        </div>

                        <div class="min-w-0">
                            <div class="text-xl font-semibold truncate">{{ $name }}</div>
                            <div class="text-xs opacity-80 mt-1">
                                ID: #{{ $student->id }}
                                • Roll: {{ $student->roll ?? '-' }}
                                • Mobile: {{ $student->mobile ?? '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2">
                        @if($student->isActived)
                            <span class="inline-flex items-center gap-1 rounded-full bg-emerald-500/15 border border-emerald-400/20 px-3 py-1 text-xs font-semibold">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span> Active
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-rose-500/15 border border-rose-400/20 px-3 py-1 text-xs font-semibold">
                                <span class="h-1.5 w-1.5 rounded-full bg-rose-400"></span> Inactive
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Content Grid --}}
            <div class="grid grid-cols-12 gap-3">

                {{-- Left: Profile --}}
                <div class="col-span-12 lg:col-span-5 space-y-3">

                    {{-- Basic Info --}}
                    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-200">
                            <div class="text-sm font-semibold text-slate-900">Basic Information</div>
                            <div class="text-xs text-slate-500">Student personal details</div>
                        </div>

                        <div class="p-4 grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-2xl border border-slate-200 p-3">
                                <div class="text-[11px] text-slate-500">First Name</div>
                                <div class="font-semibold text-slate-900">{{ $student->first_name ?? '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 p-3">
                                <div class="text-[11px] text-slate-500">Last Name</div>
                                <div class="font-semibold text-slate-900">{{ $student->last_name ?? '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 p-3 col-span-2">
                                <div class="text-[11px] text-slate-500">Full Name</div>
                                <div class="font-semibold text-slate-900">{{ $student->full_name ?? $name }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 p-3">
                                <div class="text-[11px] text-slate-500">Date of Birth</div>
                                <div class="font-semibold text-slate-900">{{ $student->dob ?? '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 p-3">
                                <div class="text-[11px] text-slate-500">Age</div>
                                <div class="font-semibold text-slate-900">{{ $student->age ?? '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 p-3">
                                <div class="text-[11px] text-slate-500">Email</div>
                                <div class="font-semibold text-slate-900 truncate">{{ $student->email ?? '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 p-3">
                                <div class="text-[11px] text-slate-500">Mobile</div>
                                <div class="font-semibold text-slate-900">{{ $student->mobile ?? '-' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Academic / Meta --}}
                    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-200">
                            <div class="text-sm font-semibold text-slate-900">Academic / Settings</div>
                            <div class="text-xs text-slate-500">Class • Section • Year • Fee Type</div>
                        </div>

                        <div class="p-4 grid grid-cols-2 gap-3 text-sm">
                            <div class="rounded-2xl border border-slate-200 p-3">
                                <div class="text-[11px] text-slate-500">Class ID</div>
                                <div class="font-semibold text-slate-900">{{ $student->class_id ?? '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 p-3">
                                <div class="text-[11px] text-slate-500">Section ID</div>
                                <div class="font-semibold text-slate-900">{{ $student->section_id ?? '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 p-3">
                                <div class="text-[11px] text-slate-500">Academic Year ID</div>
                                <div class="font-semibold text-slate-900">{{ $student->academic_year_id ?? '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 p-3">
                                <div class="text-[11px] text-slate-500">Fees Type ID</div>
                                <div class="font-semibold text-slate-900">{{ $student->fees_type_id ?? '-' }}</div>
                            </div>

                            <div class="rounded-2xl border border-slate-200 p-3 col-span-2">
                                <div class="text-[11px] text-slate-500">Scholarship Amount</div>
                                <div class="font-semibold text-slate-900">{{ $money($student->scholarship_amount ?? 0) }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Right: Fees + Transactions --}}
                <div class="col-span-12 lg:col-span-7 space-y-3">

                    {{-- Fee Summary (optional) --}}
                    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                            <div>
                                <div class="text-sm font-semibold text-slate-900">Fee Summary</div>
                                <div class="text-xs text-slate-500">Paid / Due overview</div>
                            </div>

                            <div class="text-xs text-slate-500">
                                (Controller থেকে fee stats পাঠালে এখানে show করবে)
                            </div>
                        </div>

                        <div class="p-4 grid grid-cols-12 gap-3">
                            <div class="col-span-12 sm:col-span-6 lg:col-span-3 rounded-3xl border border-slate-200 p-4">
                                <div class="text-xs text-slate-500">Paid (All)</div>
                                <div class="mt-1 text-xl font-semibold text-emerald-700">{{ $money($feeTotalPaid) }}</div>
                            </div>

                            <div class="col-span-12 sm:col-span-6 lg:col-span-3 rounded-3xl border border-slate-200 p-4">
                                <div class="text-xs text-slate-500">Due (All)</div>
                                <div class="mt-1 text-xl font-semibold text-rose-700">{{ $money($feeTotalDue) }}</div>
                            </div>

                            <div class="col-span-12 sm:col-span-6 lg:col-span-3 rounded-3xl border border-slate-200 p-4">
                                <div class="text-xs text-slate-500">Paid (Selected Range)</div>
                                <div class="mt-1 text-xl font-semibold text-slate-900">{{ $money($feePaidInRange) }}</div>
                            </div>

                            <div class="col-span-12 sm:col-span-6 lg:col-span-3 rounded-3xl border border-slate-200 p-4">
                                <div class="text-xs text-slate-500">Due (Selected Range)</div>
                                <div class="mt-1 text-xl font-semibold text-slate-900">{{ $money($feeDueInRange) }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Recent Transactions for this student (optional) --}}
                    <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                            <div>
                                <div class="text-sm font-semibold text-slate-900">Recent Transactions</div>
                                <div class="text-xs text-slate-500">Latest transactions of this student</div>
                            </div>

                            @if(\Illuminate\Support\Facades\Route::has('transactions.center'))
                                <a href="{{ route('transactions.center') }}"
                                   class="text-xs text-slate-700 hover:underline">Transaction Center</a>
                            @endif
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                                    <tr>
                                        <th class="px-4 py-3 text-left">Date</th>
                                        <th class="px-4 py-3 text-left">Type</th>
                                        <th class="px-4 py-3 text-left">Note</th>
                                        <th class="px-4 py-3 text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse($recentStudentTx as $tx)
                                        @php
                                            $typeName = data_get($tx, 'type.name') ?? ('Type #'.($tx->transactions_type_id ?? ''));
                                            $d = (float)($tx->debit ?? 0);
                                            $c = (float)($tx->credit ?? 0);
                                            $amount = $d > 0 ? $d : $c;
                                            $isIn = $d > 0;
                                            $note = $tx->note ?? $tx->remarks ?? '-';
                                        @endphp
                                        <tr class="hover:bg-slate-50/70">
                                            <td class="px-4 py-3 whitespace-nowrap">
                                                <div class="font-medium text-slate-900">{{ $tx->transactions_date ?? '-' }}</div>
                                                <div class="text-xs text-slate-500">#{{ $tx->id ?? '' }}</div>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-2 py-1 text-xs">
                                                    {{ $typeName }}
                                                </span>
                                            </td>
                                            <td class="px-4 py-3 text-slate-600">{{ $note }}</td>
                                            <td class="px-4 py-3 text-right font-semibold {{ $isIn ? 'text-emerald-700' : 'text-rose-700' }}">
                                                {{ $money($amount) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-10 text-center text-slate-500">
                                                No transactions found for this student.
                                                <div class="text-xs text-slate-400 mt-1">
                                                    (Controller থেকে $recentStudentTx পাঠালে এখানে show করবে)
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</x-app-layout>
