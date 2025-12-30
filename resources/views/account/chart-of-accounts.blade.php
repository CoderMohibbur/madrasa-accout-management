<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chart-Of-Accounts') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <x-toast-success />

                    <div class="grid grid-cols-1 gap-10">
                        <div class="overflow-x-auto">
                            <table class="border-collapse table-auto w-full text-sm">
                                <thead>
                                    <tr>
                                        <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pb-3 text-slate-400 dark:text-slate-200 text-left">No.</th>
                                        <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pb-3 text-slate-400 dark:text-slate-200 text-left">Name</th>
                                        <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pb-3 text-slate-400 dark:text-slate-200 text-left">Account Number</th>

                                        <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pb-3 text-slate-400 dark:text-slate-200 text-left">Debit</th>
                                        <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pb-3 text-slate-400 dark:text-slate-200 text-left">Credit</th>
                                        <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pb-3 text-slate-400 dark:text-slate-200 text-left">Total</th>
                                        <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pb-3 text-slate-400 dark:text-slate-200 text-left">Balance</th>

                                        <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pb-3 text-slate-400 dark:text-slate-200 text-center">Status</th>
                                        <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pb-3 text-slate-400 dark:text-slate-200 text-center">Action</th>
                                    </tr>
                                </thead>

                                <tbody class="bg-white dark:bg-slate-800">
                                    @foreach ($accounts as $account)
                                        @php
                                            $debit  = (float) ($debitAgg[$account->id] ?? 0);
                                            $credit = (float) ($creditAgg[$account->id] ?? 0);
                                            $total  = $debit + $credit;

                                            // ✅ balance: opening + debit - credit
                                            $opening = (float) ($account->opening_balance ?? 0);
                                            $balance = $opening + $debit - $credit;
                                        @endphp

                                        <tr>
                                            <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $account->id }}
                                            </td>

                                            <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $account->name }}
                                            </td>

                                            <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $account->account_number }}
                                            </td>

                                            <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ number_format($debit, 2) }}
                                            </td>

                                            <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ number_format($credit, 2) }}
                                            </td>

                                            <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ number_format($total, 2) }}
                                            </td>

                                            <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ number_format($balance, 2) }}
                                            </td>

                                            <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-center">
                                                @if ($account->isActived)
                                                    <span class="text-green-500">Active</span>
                                                @else
                                                    <span class="text-red-500">Inactive</span>
                                                @endif
                                            </td>

                                            <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-center">
                                                <a href="{{ route('account.edit', $account->id) }}">
                                                    <x-primary-button>{{ __('Edit') }}</x-primary-button>
                                                </a>

                                                <form action="{{ route('account.destroy', $account->id) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-danger-button>
                                                        {{ __('Delete') }}
                                                    </x-danger-button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div class="mt-4">
                                {{ $accounts->links() }}
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
