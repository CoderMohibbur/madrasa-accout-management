<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add Donation') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <x-toast-success />
                    <form method="POST" enctype="multipart/form-data"
                    action="{{ isset($transactions) ? route('update_donor.update_donor, $transactions->id') : route('donosr_store.donosr_store') }}">

                        @csrf
                        @if (isset($transactions))
                        @method('PUT') {{-- Use PUT for update --}}
                    @endif

                        <div class="grid grid-cols-2 gap-10">
                            <div>

                                <!-- Donor Name -->
                                <div class="mt-5">
                                    <x-input-label for="doner_id" :value="__('Donor Name')" />
                                    <select id="doner_id" name="doner_id" class="block mt-1 w-full">
                                        @foreach ($Donors as $Donor)
                                            <option value="{{ $Donor->id }}"
                                                {{ isset($transactions) && $transactions->doner_id == $Donor->id ? 'selected' : '' }}>
                                                {{ $Donor->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('doner_id')" class="mt-2" />
                                </div>
                              <!-- Donor For -->
                                <div class="mt-5">
                                    <x-input-label for="c_s_1" :value="__('Donation For')" />

                                    <select id="c_s_1" name="c_s_1" class="block mt-1 w-full" required>
                                        <option value="" disabled selected>Select an option</option>
                                        <option value="Madrasa" {{ isset($transactions) && $transactions->c_s_1 === 'Madrasa' ? 'selected' : '' }}>Madrasa</option>
                                        <option value="Masjid" {{ isset($transactions) && $transactions->c_s_1 === 'Masjid' ? 'selected' : '' }}>Masjid</option>
                                        <option value="Atim" {{ isset($transactions) && $transactions->c_s_1 === 'Atim' ? 'selected' : '' }}>Atim</option>
                                    </select>

                                    <x-input-error :messages="$errors->get('c_s_1')" class="mt-2" />
                                </div>

                                <!--Note -->
                                <div class="mt-5">
                                    <x-input-label for="note" :value="__('Note')" />
                                    <textarea id="note" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" type="text" name="note" required>{{ isset($transactions) ? $transactions->note : old('note') }}</textarea>
                                    @error('note')
                                        <div class="mt-2 text-red-500">{{ $message }}</div>
                                    @enderror
                                 </div>


                            </div>


                            <div>

                                <!-- Lender Amount -->
                                <div class="mt-5">
                                    <x-input-label for="debit" :value="__('Donation Amount')" />
                                    <x-text-input id="debit" class="block mt-1 w-full" type="text"
                                        name="debit" :value="isset($transactions) ? $transactions->debit : old('debit')" required />
                                    <x-input-error :messages="$errors->get('debit')" class="mt-2" />
                                </div>

                                <div class="mt-5">
                                    <!-- Account -->
                                    <x-input-label for="account_id" :value="__('Account')" />
                                    <select id="account_id" name="account_id" class="block mt-1 w-full">
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}"
                                                {{ isset($transaction) && $transaction->account_id == $account->id ? 'selected' : '' }}>
                                                {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('account_id')" class="mt-2" />
                                </div>

                                <!-- Status -->
                                <div class="mt-5">
                                    <x-input-label for="isActived" :value="__('Active Status')" />
                                    <select id="isActived" name="isActived" class="block mt-1 w-full">
                                        <option value="1"
                                            {{ isset($transactions) && $transactions->isActived ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0"
                                            {{ isset($transactions) && !$transactions->isActived ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                    <x-input-error :messages="$errors->get('isActived')" class="mt-2" />
                                </div>

                                <!-- Save/Update Button -->
                                <div class="flex items-center justify-end mt-4">
                                    <x-primary-button>
                                        @if (isset($transactions))
                                            {{ __('Update') }}
                                        @else
                                            {{ __('Save') }}
                                        @endif
                                    </x-primary-button>
                                </div>
                             </div>


                            </div>

                        </div>
                    </form>

                </div>
                <br> <br> <br>

                 <div class="col-span-3">
                    <table class="border-collapse table-auto w-full text-sm">
                        <thead>
                            <tr>
                                <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">No.</th>
                                <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">Doner Name</th>
                                <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">Doner For</th>
                                <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">Donation Ammount</th>
                                <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">Donation Date</th>
                                <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-center">Account</th>
                                <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">Note</th>
                                <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">Status</th>
                                <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-slate-800">
                            @foreach ($transactionss as $transactions)
                                <tr>
                                    <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400"> {{ $transactions->id }}</td>
                                    <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400"> {{ $transactions->doner->name ?? 'N/A'}}</td>
                                    <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400"> {{ $transactions->c_s_1 }}</td>
                                    <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400"> {{ $transactions->debit }}</td>
                                    <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400"> {{ $transactions->transactions_date }}</td>
                                    <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400"> {{ $transactions->account_id }}</td>
                                    <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400"> {{ $transactions->note }}</td>
                                    <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                        @if($transactions->isActived)
                                        <span class="text-green-500">Active</span>
                                        @else
                                            <span class="text-red-500">Inactive</span>
                                        @endif
                                    </td>
                                    <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400 text-center">
                                        <a href="{{ route('edit_donor.edit_donor', $transactions->id) }}">
                                            <x-primary-button >
                                                {{ __('Edit') }}
                                            </x-primary-button>
                                        </a>
                                        <form action="{{ route('destroy_donor.destroy_donor', $transactions->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <x-danger-button >
                                                {{ __('Delete') }}
                                            </x-primary-button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>




















