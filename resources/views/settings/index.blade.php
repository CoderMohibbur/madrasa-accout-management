@php
    $pageTitle = 'Settings';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">{{ $pageTitle }}</h2>
                <p class="text-xs text-slate-500 mt-1">সব master data এক জায়গা থেকে add/edit/active করুন</p>
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            @if (session('success'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-emerald-900 text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach($entities as $key => $meta)
                    <div class="rounded-3xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                        <div class="px-4 py-3 border-b border-slate-200 flex items-center justify-between">
                            <div>
                                <div class="text-sm font-semibold text-slate-900">{{ $meta['title'] }}</div>
                                <div class="text-xs text-slate-500">Total: {{ ($data[$key] ?? collect())->count() }}</div>
                            </div>
                        </div>

                        {{-- Add form --}}
                        <form method="POST" action="{{ route('settings.store', $key) }}" class="p-4 space-y-3">
                            @csrf

                            <div class="grid grid-cols-1 gap-3">
                                @foreach($meta['fields'] as $f)
                                    <div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">{{ $f['label'] }}</label>
                                        <input
                                            type="{{ $f['type'] ?? 'text' }}"
                                            name="{{ $f['name'] }}"
                                            value="{{ old($f['name']) }}"
                                            class="w-full rounded-2xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200"
                                        />
                                        @error($f['name'])
                                            <div class="text-xs text-rose-600 mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>

                            <button class="w-full rounded-2xl bg-slate-900 text-white text-sm px-4 py-2 hover:bg-slate-800">
                                Add {{ $meta['title'] }}
                            </button>
                        </form>

                        {{-- List --}}
                        <div class="px-4 pb-4">
                            <div class="text-xs font-semibold text-slate-700 mb-2">Recent items</div>

                            <div class="space-y-2 max-h-72 overflow-auto pr-1">
                                @forelse(($data[$key] ?? []) as $row)
                                    <div x-data="{edit:false}" class="rounded-2xl border border-slate-200 p-3">
                                        {{-- View mode --}}
                                        <div x-show="!edit" class="flex items-start justify-between gap-3">
                                            <div class="min-w-0">
                                                <div class="text-sm font-medium text-slate-900 truncate">
                                                    {{-- show first field as title --}}
                                                    {{ data_get($row, $meta['fields'][0]['name']) ?? ('#'.$row->{$meta['pk']}) }}
                                                </div>

                                                <div class="text-[11px] text-slate-500 mt-1">
                                                    ID: #{{ $row->{$meta['pk']} }}
                                                    @if(property_exists($row,'isActived'))
                                                        • Status:
                                                        <span class="{{ $row->isActived ? 'text-emerald-700' : 'text-rose-700' }}">
                                                            {{ $row->isActived ? 'Active' : 'Inactive' }}
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex items-center gap-2 shrink-0">
                                                @if(property_exists($row,'isActived'))
                                                    <form method="POST" action="{{ route('settings.toggle', [$key, $row->{$meta['pk']}]) }}">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button class="rounded-xl border border-slate-200 px-2 py-1 text-xs hover:bg-slate-50">
                                                            Toggle
                                                        </button>
                                                    </form>
                                                @endif

                                                <button @click="edit=true" class="rounded-xl border border-slate-200 px-2 py-1 text-xs hover:bg-slate-50">
                                                    Edit
                                                </button>

                                                <form method="POST" action="{{ route('settings.destroy', [$key, $row->{$meta['pk']}]) }}"
                                                      onsubmit="return confirm('Remove this item?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="rounded-xl border border-rose-200 text-rose-700 px-2 py-1 text-xs hover:bg-rose-50">
                                                        Remove
                                                    </button>
                                                </form>
                                            </div>
                                        </div>

                                        {{-- Edit mode --}}
                                        <div x-show="edit" x-cloak>
                                            <form method="POST" action="{{ route('settings.update', [$key, $row->{$meta['pk']}]) }}" class="space-y-2">
                                                @csrf
                                                @method('PUT')

                                                <div class="grid grid-cols-1 gap-2">
                                                    @foreach($meta['fields'] as $f)
                                                        <div>
                                                            <label class="block text-[11px] font-medium text-slate-600 mb-1">{{ $f['label'] }}</label>
                                                            <input
                                                                type="{{ $f['type'] ?? 'text' }}"
                                                                name="{{ $f['name'] }}"
                                                                value="{{ old($f['name'], data_get($row, $f['name'])) }}"
                                                                class="w-full rounded-2xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200"
                                                            />
                                                        </div>
                                                    @endforeach
                                                </div>

                                                <div class="flex gap-2">
                                                    <button class="flex-1 rounded-2xl bg-slate-900 text-white text-sm px-4 py-2 hover:bg-slate-800">
                                                        Save
                                                    </button>
                                                    <button type="button" @click="edit=false"
                                                            class="flex-1 rounded-2xl border border-slate-200 text-sm px-4 py-2 hover:bg-slate-50">
                                                        Cancel
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-sm text-slate-500">No items found.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>
    </div>
</x-app-layout>
