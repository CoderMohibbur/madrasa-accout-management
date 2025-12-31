@php $pageTitle = 'Edit Expense Head'; @endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">{{ $pageTitle }}</h2>
                <p class="text-xs text-slate-500 mt-1">Update title/category/status</p>
            </div>
            <a href="{{ route('expens.index') }}" class="text-sm text-slate-700 hover:underline">Back</a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-4">
                @if ($errors->any())
                    <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-3 py-2 text-rose-800 text-sm">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $e)
                                <li>{{ $e }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('expens.update', $expense->id) }}" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Name</label>
                        <input type="text" name="name" value="{{ old('name', $expense->name) }}"
                            class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-600 mb-1">Category (optional)</label>
                        <select name="catagory_id"
                            class="w-full rounded-xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                            <option value="">-- None --</option>
                            @foreach ($categories ?? [] as $c)
                                <option value="{{ $c->id }}" @selected((string) old('catagory_id', $expense->catagory_id) === (string) $c->id)>
                                    {{ $c->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="isActived" value="1" @checked(old('isActived', $expense->isActived))>
                        <span class="text-sm text-slate-700">Active</span>
                    </label>

                    <button type="submit"
                        class="w-full rounded-xl bg-slate-900 text-white text-sm px-4 py-2 hover:bg-slate-800">
                        Update
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
