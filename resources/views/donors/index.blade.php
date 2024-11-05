<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('List Donor') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="text-gray-900 dark:text-gray-100">
                    <x-toast-success />
                    <div class="grid grid-cols-1 gap-10">
                        <div class="overflow-auto rounded-lg shadow-lg">
                            <table class="border-collapse table-auto w-full text-sm">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="border-b dark:border-slate-600 font-medium px-4 py-2 text-left text-slate-400 dark:text-slate-200"> ID</th>
                                        <th class="border-b dark:border-slate-600 font-medium px-4 py-2 text-left text-slate-400 dark:text-slate-200"> Name</th>
                                        <th class="border-b dark:border-slate-600 font-medium px-4 py-2 text-left text-slate-400 dark:text-slate-200"> Email</th>
                                        <th class="border-b dark:border-slate-600 font-medium px-4 py-2 text-left text-slate-400 dark:text-slate-200"> Mobile</th>
                                        <th class="border-b dark:border-slate-600 font-medium px-4 py-2 text-left text-slate-400 dark:text-slate-200"> Fees Type</th>
                                        <th class="border-b dark:border-slate-600 font-medium px-4 py-2 text-left text-slate-400 dark:text-slate-200"> Status</th>
                                        <th class="border-b dark:border-slate-600 font-medium px-4 py-2 text-center text-slate-400 dark:text-slate-200"> Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-slate-800"> @foreach ($Donors as $Donor)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                        <td class="border-b border-gray-100 dark:border-gray-700 px-4 py-2 text-gray-600 dark:text-gray-400"> {{ $Donor->id }} </td>
                                        <td class="border-b border-gray-100 dark:border-gray-700 px-4 py-2 text-gray-600 dark:text-gray-400"> {{ $Donor->name }} </td>
                                        <td class="border-b border-gray-100 dark:border-gray-700 px-4 py-2 text-gray-600 dark:text-gray-400"> {{ $Donor->email }} </td>
                                        <td class="border-b border-gray-100 dark:border-gray-700 px-4 py-2 text-gray-600 dark:text-gray-400"> {{ $Donor->mobile }} </td>
                                        <td class="border-b border-gray-100 dark:border-gray-700 px-4 py-2 text-gray-600 dark:text-gray-400"> {{ $Donor->fees_type_id }} </td>
                                        <td class="border-b border-gray-100 dark:border-gray-700 px-4 py-2 text-gray-600 dark:text-gray-400"> @if ($Donor->isActived) <span class="text-green-500">Active</span> @else <span class="text-red-500">Inactive</span> @endif </td>
                                        <td class="border-b border-gray-100 dark:border-gray-700 px-4 py-2 text-center">
                                            <a href="{{ route('donors.edit', $Donor->id) }}">
                                                <x-primary-button> {{ __('Edit') }} </x-primary-button>
                                            </a>
                                            <form action="{{ route('donors.destroy', $Donor->id) }}" method="POST" style="display:inline;"> @csrf @method('DELETE')
                                                <x-danger-button> {{ __('Delete') }} </x-primary-button>
                                            </form>
                                        </td>
                                    </tr> @endforeach </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>