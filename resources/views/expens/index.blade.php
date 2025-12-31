@php $pageTitle = 'Expense Heads'; @endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">{{ $pageTitle }}</h2>
                <p class="text-xs text-slate-500 mt-1">Settings → Expense title list (amount goes to Transactions)</p>
            </div>

            <a href="{{ route('expens.create') }}"
               class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm text-white hover:bg-slate-800">
                + Add Expense
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            @if (session('success'))
                <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-800 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead class="bg-slate-50 text-slate-600 text-xs uppercase">
                            <tr>
                                <th class="px-4 py-3 text-left">Name</th>
                                <th class="px-4 py-3 text-left">Status</th>
                                <th class="px-4 py-3 text-right">Actions</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-slate-100">
                            @forelse($expenses as $e)
                                <tr class="hover:bg-slate-50/70">
                                    <td class="px-4 py-3">
                                        <div class="font-medium text-slate-900">{{ $e->name }}</div>
                                        <div class="text-xs text-slate-500">#{{ $e->id }}</div>
                                    </td>

                                    <td class="px-4 py-3">
                                        @if($e->isActived)
                                            <span class="inline-flex items-center rounded-lg border border-emerald-200 bg-emerald-50 px-2 py-1 text-xs text-emerald-800">Active</span>
                                        @else
                                            <span class="inline-flex items-center rounded-lg border border-rose-200 bg-rose-50 px-2 py-1 text-xs text-rose-800">Inactive</span>
                                        @endif
                                    </td>

                                    <td class="px-4 py-3 text-right whitespace-nowrap">
                                        <a href="{{ route('expens.edit', $e->id) }}"
                                           class="text-xs rounded-lg border border-slate-200 bg-white px-2 py-1 hover:bg-slate-50">
                                            Edit
                                        </a>

                                        <form action="{{ route('expens.destroy', $e->id) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                onclick="return confirm('Delete this expense head?')"
                                                class="text-xs rounded-lg border border-rose-200 bg-rose-50 px-2 py-1 text-rose-700 hover:bg-rose-100">
                                                Delete
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-4 py-10 text-center text-slate-500">No expense heads found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="px-4 py-3 border-t border-slate-200">
                    {{ $expenses->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
