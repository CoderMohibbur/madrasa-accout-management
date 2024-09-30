<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add Section') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <x-toast-success />
                    <div class="grid grid-cols-4 gap-10">
                        <div>
                            <form method="POST"
                                action="{{ isset($Section) ? route('Section.update', $Section->id) : route('Section.store') }}">
                                @csrf

                                @if (isset($Section))
                                    @method('PUT') {{-- Use PUT for update --}}
                                @endif

                                <!-- Class ID (hidden for update) -->
                                @if (isset($Section))
                                    <div class="mt-5">
                                        <x-input-label class="hidden" for="id" />
                                        <x-text-input type="hidden" name="id" value="{{ $Section->id }}" />
                                        <x-input-error :messages="$errors->get('id')" class="mt-2" />
                                    </div>
                                @endif

                                <!-- Class Name -->
                                <div class="mt-5">
                                    <x-input-label for="name" :value="__('Section Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                        :value="isset($Section) ? $Section->name : old('name')" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <!-- Status -->
                                <div class="mt-5">
                                    <x-input-label for="status" :value="__('Status')" />
                                    <x-status id="isActived" name="isActived" :value="isset($Section) ? $Section->isActived : old('isActived')" required />
                                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                                </div>

                                <!-- Save/Update Button -->
                                <div class="flex items-center justify-end mt-4">
                                    <x-primary-button>
                                        @if (isset($Section))
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
                                            Name</th>
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            Status</th>
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-center">
                                            Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-slate-800">
                                    @foreach ($Sections as $Section)
                                        <tr>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $Section->id }}</td>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $Section->name }}</td>
                                            <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                @if($Section->isActived)
                                                <span class="text-green-500">Active</span>
                                                @else
                                                    <span class="text-red-500">Inactive</span>
                                                @endif
                                            </td>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400 text-center">
                                                <a href="{{ route('Section.edit', $Section->id) }}">
                                                    <x-primary-button>
                                                        {{ __('Edit') }}
                                                    </x-primary-button>
                                                </a>
                                                <form action="{{ route('Section.destroy', $Section->id) }}"
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
