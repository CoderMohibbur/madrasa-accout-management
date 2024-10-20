<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add Registration Fees') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <x-toast-success />
                    <div class="grid grid-cols-2 gap-10">
                        <div>
                            <form method="POST"  enctype="multipart/form-data"
                                action="{{ isset($registration) ? route('add_registration.update', $registration->id) : route('add_registration.store') }}">
                                @csrf

                                @if (isset($registration))
                                    @method('PUT') {{-- Use PUT for update --}}
                                @endif

                                <!-- Class ID (hidden for update) -->
                                @if (isset($registration))
                                    <div class="mt-5">
                                        <x-input-label class="hidden" for="id" />
                                        <x-text-input type="hidden" name="id" value="{{ $registration->id }}" />
                                        <x-input-error :messages="$errors->get('id')" class="mt-2" />
                                    </div>
                                @endif
                                 <!-- Class ID -->
                                <div class="mt-5">
                                    <x-input-label for="class_id" :value="__('Class Name')" />
                                    <select id="class_id" name="class_id" class="block mt-1 w-full">
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                {{ isset($registration) && $registration->class_id == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}

                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('class_id')" class="mt-2" />
                                </div>

                                <!-- monthly_fee -->
                                <div class="mt-5">
                                    <x-input-label for="monthly_fee" :value="__('Monthly Fee')" />
                                    <x-text-input id="monthly_fee" class="block mt-1 w-full" type="text" name="monthly_fee"
                                        :value="isset($registration) ? $registration->monthly_fee : old('monthly_fee')" required />
                                    <x-input-error :messages="$errors->get('monthly_fee')" class="mt-2" />
                                </div>
                                <!-- boarding_fee -->
                                <div class="mt-5">
                                    <x-input-label for="boarding_fee" :value="__('Boarding Fee')" />
                                    <x-text-input id="boarding_fee" class="block mt-1 w-full" type="text"
                                        name="boarding_fee" :value="isset($registration) ? $registration->boarding_fee : old('boarding_fee')" required />
                                    <x-input-error :messages="$errors->get('boarding_fee')" class="mt-2" />
                                </div>
                                <!--management_fee -->
                                <div class="mt-5">
                                    <x-input-label for="management_fee" :value="__('Management Fee')" />
                                    <x-text-input id="management_fee" class="block mt-1 w-full" type="text"
                                        name="management_fee" :value="isset($registration) ? $registration->management_fee : old('management_fee')" required />
                                    <x-input-error :messages="$errors->get('management_fee')" class="mt-2" />
                                </div>

                                </div>
                                <div>
                                <!--examination_fee  -->
                                <div class="mt-5">
                                    <x-input-label for="examination_fee" :value="__('Examination Fee')" />
                                    <x-text-input id="examination_fee" class="block mt-1 w-full" type="text"
                                        name="examination_fee" :value="isset($registration) ? $registration->examination_fee : old('examination_fee')" required />
                                    <x-input-error :messages="$errors->get('examination_fee')" class="mt-2" />
                                </div>
                                <!--other -->
                                <div class="mt-5">
                                    <x-input-label for="other" :value="__('Other')" />
                                    <x-text-input id="other" class="block mt-1 w-full" type="text"
                                        name="other" :value="isset($registration) ? $registration->other : old('other')" required />
                                    <x-input-error :messages="$errors->get('other')" class="mt-2" />
                                </div>
                                <!-- Status -->
                                <div class="mt-5">
                                    <x-input-label for="status" :value="__('Status')" />
                                    <x-status id="isActived" name="isActived" :value="isset($registration) ? $registration->isActived : old('isActived')" required />
                                    <x-input-error :messages="$errors->get('status')" year="mt-2" />
                                </div>

                                <!-- Save/Update Button -->
                                <div class="flex items-center justify-end mt-4">
                                    <x-primary-button>
                                        @if (isset($registration))
                                            {{ __('Update') }}
                                        @else
                                            {{ __('Save') }}
                                        @endif
                                    </x-primary-button>
                                </div>
                            </form>

                        </div>
                  
                        <div class="col-span-3">
                            <table class="border-collapse table-auto w-full text-sm">
                                <thead>
                                    <tr>
                                        {{-- <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            ID</th> --}}
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            Class Name</th>
                                            <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            Monthly Fee</th>
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            Boarding Fee</th>
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            Management Fee</th>
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            Examination Fee</th>
                                            <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            Other</th>
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            Status</th>
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-center">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-slate-800">
                                    @foreach ($registrations as $registration)
                                        <tr>
                                            {{-- <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $registration->id }}</td> --}}
                                                <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $registration->class->name }}</td>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $registration->monthly_fee }}</td>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $registration->boarding_fee }}</td>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $registration->management_fee }}</td>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $registration->examination_fee }}</td>
                                                <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $registration->other }}</td>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                @if($registration->isActived)
                                                <span class="text-green-500">Active</span>
                                                @else
                                                    <span class="text-red-500">Inactive</span>
                                                @endif</td>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400 text-center">
                                                <a href="{{ route('add_registration.edit', $registration->id) }}">
                                                    <x-primary-button>
                                                        {{ __('Edit') }}
                                                    </x-primary-button>
                                                </a>
                                                <form action="{{ route('add_registration.destroy', $registration->id) }}"
                                                    method="POST" style="display:inline;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <x-danger-button>
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
        </div>
    </div>
</x-app-layout>
