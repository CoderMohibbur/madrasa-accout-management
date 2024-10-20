<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add Lender') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <x-toast-success />
                    <form method="POST" enctype="multipart/form-data"
                        action="{{ isset($lender) ? route('lender.update', $lender->id) : route('lender.store') }}">

                        @csrf

                        @if (isset($lender))
                            @method('PUT') {{-- Use PUT for update --}}
                        @endif
                        <div class="grid grid-cols-2 gap-10">

                            <div>
                                <!-- Name -->
                                <div class="mt-5">
                                    <x-input-label for="name" :value="__('Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                        :value="isset($lender) ? $lender->name : old('name')" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <!-- phone -->
                                <div class="mt-5">
                                    <x-input-label for="phone" :value="__('Phone Number')" />
                                    <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone"
                                        :value="isset($lender) ? $lender->phone : old('phone')" required />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>


                                <!-- Email -->
                                <div class="mt-5">
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                        :value="isset($lender) ? $lender->email : old('email')" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                                <!-- address -->
                                <div class="mt-5">
                                    <x-input-label for="address" :value="__('Address')" />
                                    <textarea id="address"
                                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        name="address" required>{{ isset($lender) ? $lender->address : old('address') }}</textarea>
                                    @error('address')
                                        <div class="mt-2 text-red-500">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>

                            <div>






                                <!-- bank_detils -->
                                <div class="mt-5">
                                    <x-input-label for="bank_detils" :value="__('Bank Detils')" />
                                    <textarea id="bank_detils"
                                        class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                                        type="text" name="bank_detils" required>{{ isset($lender) ? $lender->bank_detils : old('bank_detils') }}</textarea>
                                    @error('bank_detils')
                                        <div class="mt-2 text-red-500">{{ $message }}</div>
                                    @enderror
                                </div>


                                <!-- users id-->
                                <div class="mt-5">
                                    <x-input-label for="users_id" :value="__('Users Id')" />
                                    <select id="users_id" name="users_id" class="block mt-1 w-full">
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                {{ isset($lender) && $lender->users_id == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}

                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('users_id')" class="mt-2" />
                                </div>
                                <!-- Status -->
                                <div class="mt-5">
                                    <x-input-label for="status" :value="__('Status')" />
                                    <x-status id="isActived" name="isActived" :value="isset($lender) ? $lender->isActived : old('isActived')" required />
                                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                </div>



                                <!-- Save/Update Button -->
                                <div class="flex items-center justify-end mt-4">
                                    <x-primary-button>
                                        @if (isset($lender))
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
        </div>
    </div>
    </div>
</x-app-layout>
