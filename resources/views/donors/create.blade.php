<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add Donor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <x-toast-success />
                    <form method="POST" enctype="multipart/form-data"
                        action="{{ isset($Donor) ? route('donors.update', $Donor->id) : route('donors.store') }}">

                        @csrf

                        @if (isset($Donor))
                            @method('PUT') {{-- Use PUT for update --}}
                        @endif
                        <div class="grid grid-cols-2 gap-10">

                            <div>
                                <!-- Full Name -->
                                <div class="mt-5">
                                    <x-input-label for="name" :value="__('Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                        :value="isset($Donor) ? $Donor->name : old('name')" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                {{-- <!-- Fees Type ID -->
                                <div class="mt-5">
                                    <x-input-label for="fees_type_id" :value="__('Fees Type')" />
                                    <select id="fees_type_id" name="fees_type_id" class="block mt-1 w-full">
                                        @foreach ($fees_types as $fees_type)
                                            <option value="{{ $fees_type->id }}"
                                                {{ isset($student) && $student->fees_type_id == $fees_type->id ? 'selected' : '' }}>
                                                {{ $fees_type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('fees_type_id')" class="mt-2" />
                                </div> --}}

                                <!-- Email -->
                                <div class="mt-5">
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                        :value="isset($Donor) ? $Donor->email : old('email')" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                            </div>
                            <div>
                                <!-- Mobile -->
                                <div class="mt-5">
                                    <x-input-label for="mobile" :value="__('Mobile Number')" />
                                    <x-text-input id="mobile" class="block mt-1 w-full" type="tel" name="mobile"
                                        :value="isset($Donor) ? $Donor->mobile : old('mobile')" required />
                                    <x-input-error :messages="$errors->get('mobile')" class="mt-2" />
                                </div>
                                <!-- Status -->
                                <div class="mt-5">
                                    <x-input-label for="status" :value="__('Status')" />
                                    <x-status id="isActived" name="isActived" :value="isset($Donor) ? $Donor->isActived : old('isActived')" required />
                                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                </div>
                                <!-- Save/Update Button -->
                                <div class="flex items-center justify-end mt-4">
                                    <x-primary-button>
                                        @if (isset($Donor))
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
