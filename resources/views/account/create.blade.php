<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add Acount') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <x-toast-success />
                    <form method="POST" enctype="multipart/form-data"
                        action="{{ isset($account) ? route('account.update', $account->id) : route('account.store') }}">

                        @csrf

                        @if (isset($account))
                            @method('PUT') {{-- Use PUT for update --}}
                        @endif
                        <div class="grid grid-cols-2 gap-10">
                            <div>
                                <!-- Name -->
                                <div class="mt-5">
                                    <x-input-label for="name" :value="__('Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text"
                                        name="name" :value="isset($account) ? $account->name : old('name')" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <!-- account_number -->
                                <div class="mt-5">
                                    <x-input-label for="account_number" :value="__('Account Number')" />
                                    <x-text-input id="account_number" class="block mt-1 w-full" type="text"
                                        name="account_number" :value="isset($account) ? $account->account_number : old('account_number')" required />
                                    <x-input-error :messages="$errors->get('account_number')" class="mt-2" />
                                </div>

                                <!-- account_details-->
                                <div class="mt-5">
                                    <x-input-label for="account_details" :value="__('Account Details')" />
                                    <x-text-input id="account_details" class="block mt-1 w-full" type="text"
                                        name="account_details" :value="isset($account) ? $account->account_details : old('account_details')" required />
                                    <x-input-error :messages="$errors->get('account_details')" class="mt-2" />
                                </div>

                                <!-- opening_balance -->
                                <div class="mt-5">
                                    <x-input-label for="opening_balance" :value="__('Opening Balance')" />
                                    <x-text-input id="opening_balance" class="block mt-1 w-full" type="text" name="opening_balance"
                                        :value="isset($account) ? $account->opening_balance : old('opening_balance')" required />
                                    <x-input-error :messages="$errors->get('opening_balance')" class="mt-2" />
                                </div>

                                <!-- current_balance -->
                                <div class="mt-5">
                                    <x-input-label for="current_balance" :value="__('Current Balance')" />
                                    <x-text-input id="current_balance" class="block mt-1 w-full" type="text" name="current_balance"
                                        :value="isset($account) ? $account->current_balance : old('current_balance')" required />
                                    <x-input-error :messages="$errors->get('current_balance')" class="mt-2" />
                                </div>


                                <!-- Status -->
                                <div class="mt-5">
                                    <x-input-label for="isActived" :value="__('Active Status')" />
                                    <select id="isActived" name="isActived" class="block mt-1 w-full">
                                        <option value="1"
                                            {{ isset($account) && $account->isActived ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0"
                                            {{ isset($account) && !$account->isActived ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                    <x-input-error :messages="$errors->get('isActived')" class="mt-2" />
                                </div>

                                <!-- Save/Update Button -->
                                <div class="flex items-center justify-end mt-4">
                                    <x-primary-button>
                                        @if (isset($account))
                                            {{ __('Update') }}
                                        @else
                                            {{ __('Save') }}
                                        @endif
                                    </x-primary-button>
                                </div>
                            </div>

                        </div>
                    </form>
                    <script>
                        document.getElementById('first_name').addEventListener('input', combineNames);
                        document.getElementById('last_name').addEventListener('input', combineNames);

                        function combineNames() {
                            const firstName = document.getElementById('first_name').value;
                            const lastName = document.getElementById('last_name').value;
                            document.getElementById('full_name').value = `${firstName} ${lastName}`;
                        }
                    </script>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>


