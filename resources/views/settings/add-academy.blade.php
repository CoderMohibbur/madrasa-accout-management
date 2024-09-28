<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add-Academy year') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="grid grid-cols-4 gap-10">
                        <div>
                            <form method="POST"
                                action="{{ isset($class) ? route('add_academy.update', $class->id) : route('add_academy.store') }}">
                                @csrf

                                @if (isset($class))
                                    @method('PUT') {{-- Use PUT for update --}}
                                @endif

                                <!-- Class ID (hidden for update) -->
                                @if (isset($class))
                                    <div class="mt-5">
                                        <x-input-label class="hidden" for="id" />
                                        <x-text-input type="hidden" name="id" value="{{ $class->id }}" />
                                        <x-input-error :messages="$errors->get('id')" class="mt-2" />
                                    </div>
                                @endif

                                <!-- Year -->
                                <div class="mt-5">
                                    <x-input-label for="year" :value="__('year')" />
                                    <x-text-input id="year" class="block mt-1 w-full" type="text" name="year"
                                        :value="isset($year) ? $year->year : old('year')" required />
                                    <x-input-error :messages="$errors->get('year')" class="mt-2" />
                                </div>
                                <!-- Academic Years -->
                                <div class="mt-5">
                                    <x-input-label for="academic_years" :value="__('academic years')" />
                                    <x-text-input id="academic_years" class="block mt-1 w-full" type="text"
                                        name="academic_years" :value="isset($year) ? $class->academic_years : old('academic_years')" required />
                                    <x-input-error :messages="$errors->get('academic_years')" class="mt-2" />
                                </div>
                                <!--Starting Date -->
                                <div class="mt-5">
                                    <x-input-label for="starting_Date" :value="__('starting Date')" />
                                    <x-text-input id="Starting_Date" class="block mt-1 w-full" type="date"
                                        name="starting_Date" :value="isset($year) ? $year->starting_Date : old('starting_Date')" required />
                                    <x-input-error :messages="$errors->get('starting_Date')" class="mt-2" />
                                </div>
                                <!--Ending Date  -->
                                <div class="mt-5">
                                    <x-input-label for="Ending_Date" :value="__('Ending Date')" />
                                    <x-text-input id="Ending_Date" class="block mt-1 w-full" type="date"
                                        name="Ending_Date" :value="isset($year) ? $year->Ending_Date : old('Ending_Date')" required />
                                    <x-input-error :messages="$errors->get('Ending_Date')" class="mt-2" />
                                </div>
                                <!-- Status -->
                                <div class="mt-5">
                                    <x-input-label for="status" :value="__('Status')" />
                                    <x-status id="status" name="status" :value="isset($class) ? $class->status : old('status')" required />
                                    <x-input-error :messages="$errors->get('status')" year="mt-2" />
                                </div>

                                <!-- Save/Update Button -->
                                <div class="flex items-center justify-end mt-4">
                                    <x-primary-button>
                                        @if (isset($year))
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
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            ID</th>
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            year</th>
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            Academic years</th>
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            Starting Date</th>
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            Ending Date</th>
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            Status</th>
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-center">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-slate-800">
                                    @foreach ($years as $year)
                                        <tr>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $year->id }}</td>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $year->year }}</td>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $year->Academic_years }}</td>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $year->Starting_Date }}</td>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $year->Ending_Date }}</td>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $year->status }}</td>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400 text-center">
                                                <a href="{{ route('add_academy.edit', $year->id) }}">
                                                    <x-primary-button>
                                                        {{ __('Edit') }}
                                                    </x-primary-button>
                                                </a>
                                                <form action="{{ route('add_academy.destroy', $year->id) }}"
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
