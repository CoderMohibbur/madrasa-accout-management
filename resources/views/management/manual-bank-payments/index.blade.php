<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Manual Bank Payment Review
            </h2>
            <p class="mt-1 text-sm text-slate-500">
                This queue is additive to the existing management screens. Approving a row finalizes the dedicated payment and receipt flow without touching the legacy WordPress IPN routing.
            </p>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                            <thead class="bg-slate-50 text-left text-xs uppercase tracking-[0.2em] text-slate-500 dark:bg-slate-900/70 dark:text-slate-400">
                                <tr>
                                    <th class="px-4 py-3">Invoice</th>
                                    <th class="px-4 py-3">Guardian</th>
                                    <th class="px-4 py-3">Student</th>
                                    <th class="px-4 py-3">Amount</th>
                                    <th class="px-4 py-3">Reference</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th class="px-4 py-3">Submitted</th>
                                    <th class="px-4 py-3">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                @forelse ($payments as $payment)
                                    <tr>
                                        <td class="px-4 py-4 font-medium text-slate-900 dark:text-slate-100">
                                            {{ $payment->payable?->invoice_number ?: 'Invoice #'.$payment->payable_id }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <div>{{ $payment->user?->name ?: 'Unknown user' }}</div>
                                            <div class="text-xs text-slate-500">{{ $payment->user?->email }}</div>
                                        </td>
                                        <td class="px-4 py-4">{{ $payment->payable?->student?->full_name ?: 'Student #'.$payment->payable?->student_id }}</td>
                                        <td class="px-4 py-4">{{ number_format((float) $payment->amount, 2) }} {{ $payment->currency }}</td>
                                        <td class="px-4 py-4">{{ $payment->provider_reference ?: '-' }}</td>
                                        <td class="px-4 py-4">{{ strtoupper($payment->status) }}</td>
                                        <td class="px-4 py-4">
                                            {{ optional($payment->initiated_at ?? $payment->created_at)->format('Y-m-d H:i') }}
                                        </td>
                                        <td class="px-4 py-4">
                                            <div class="flex flex-wrap gap-2">
                                                <form method="POST" action="{{ route('management.payments.manual-bank.approve', $payment) }}">
                                                    @csrf
                                                    <input type="hidden" name="matched_bank_reference" value="{{ $payment->provider_reference }}">
                                                    <button type="submit" class="rounded-full bg-emerald-500 px-4 py-2 text-xs font-semibold text-white hover:bg-emerald-400">
                                                        Approve
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('management.payments.manual-bank.reject', $payment) }}">
                                                    @csrf
                                                    <input type="hidden" name="decision_note" value="Manual review rejected from management queue.">
                                                    <button type="submit" class="rounded-full bg-rose-500 px-4 py-2 text-xs font-semibold text-white hover:bg-rose-400">
                                                        Reject
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-4 py-10 text-center text-sm text-slate-500 dark:text-slate-400">
                                            No manual-bank review rows are waiting right now.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($payments->hasPages())
                        <div class="mt-6">
                            {{ $payments->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
