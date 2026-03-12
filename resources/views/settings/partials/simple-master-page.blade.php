@php
    $totalRecords = count($records ?? []);
    $activeRecords = collect($records ?? [])->filter(fn ($record) => (bool) data_get($record, 'isActived', true))->count();
    $isEditing = isset($editingRecord) && $editingRecord;
    $settingsHubUrl = \Illuminate\Support\Facades\Route::has('settings.index') ? route('settings.index') : url('/settings');
    $dashboardUrl = \Illuminate\Support\Facades\Route::has('dashboard') ? route('dashboard') : url('/dashboard');
    $fieldName = $fieldName ?? 'name';
    $fieldLabel = $fieldLabel ?? 'Name';
    $fieldPlaceholder = $fieldPlaceholder ?? "Enter {$fieldLabel}";
@endphp

<x-app-layout>
    <x-slot name="header">
        <div>
            <div class="ui-page-kicker">Settings / Master Data</div>
            <h2 class="ui-page-title">{{ $pageTitle }}</h2>
            <p class="ui-page-description">{{ $pageDescription }}</p>
        </div>

        <div class="flex flex-wrap gap-2">
            <a href="{{ $settingsHubUrl }}" class="ui-button ui-button--secondary">Settings Hub</a>
            <a href="{{ $dashboardUrl }}" class="ui-button ui-button--ghost">Dashboard</a>
        </div>
    </x-slot>

    <x-toast-success />

    <div class="ui-container py-8 sm:py-10">
        <div class="ui-admin-cluster">
            <section class="ui-admin-hero">
                <div class="ui-admin-hero__grid">
                    <div>
                        <span class="ui-page-kicker">{{ $heroEyebrow ?? 'Structured maintenance' }}</span>
                        <h1 class="ui-admin-hero__title">{{ $heroTitle ?? $pageTitle }}</h1>
                        <p class="ui-admin-hero__copy">{{ $heroDescription ?? $pageDescription }}</p>

                        <div class="mt-6 grid gap-3 sm:grid-cols-2">
                            <div class="ui-admin-note">
                                <div class="ui-admin-note__title">Keep shared records consistent</div>
                                <p class="mt-2">
                                    These values are reused across admissions, fees, reporting, and transaction entry. Keeping them tidy improves every downstream workflow.
                                </p>
                            </div>

                            <div class="ui-admin-note ui-admin-note--accent">
                                <div class="ui-admin-note__title">{{ $isEditing ? 'Editing existing record' : 'Adding a new record' }}</div>
                                <p class="mt-2">
                                    {{ $isEditing
                                        ? 'Update carefully so existing records remain understandable across the product.'
                                        : 'Add a clean, well-named record so it is easy to reuse throughout the system.' }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="ui-admin-hero__stats">
                        <div class="ui-admin-kpi">
                            <div class="ui-admin-kpi__label">Total Records</div>
                            <div class="ui-admin-kpi__value">{{ $totalRecords }}</div>
                            <div class="ui-admin-kpi__meta">All visible records on this maintenance page.</div>
                        </div>

                        <div class="ui-admin-kpi">
                            <div class="ui-admin-kpi__label">Active Records</div>
                            <div class="ui-admin-kpi__value">{{ $activeRecords }}</div>
                            <div class="ui-admin-kpi__meta">Records currently marked active for broader use.</div>
                        </div>

                        <div class="ui-admin-kpi">
                            <div class="ui-admin-kpi__label">Current State</div>
                            <div class="ui-admin-kpi__value">{{ $isEditing ? 'Editing' : 'Ready' }}</div>
                            <div class="ui-admin-kpi__meta">{{ $isEditing ? 'An existing record is loaded into the form.' : 'The form is ready for a new record.' }}</div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="grid gap-6 xl:grid-cols-[0.92fr,1.08fr]">
                <x-ui.card
                    :title="$isEditing ? 'Update record' : 'Add record'"
                    :description="$isEditing ? 'Refine the selected record without changing the underlying route or behavior.' : 'Create a new reusable record for this master-data section.'"
                >
                    <form method="POST"
                        action="{{ $isEditing ? route($updateRouteName, $editingRecord->id) : route($storeRouteName) }}"
                        class="space-y-5">
                        @csrf

                        @if ($isEditing)
                            @method('PUT')
                        @endif

                        <div>
                            <x-input-label :for="$fieldName" :value="$fieldLabel" />
                            <x-text-input
                                :id="$fieldName"
                                :name="$fieldName"
                                type="text"
                                class="mt-3"
                                :value="$isEditing ? data_get($editingRecord, $fieldName) : old($fieldName)"
                                :placeholder="$fieldPlaceholder"
                                required
                            />
                            <x-input-error :messages="$errors->get($fieldName)" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="isActived" value="Status" />
                            <x-status
                                id="isActived"
                                name="isActived"
                                class="mt-3"
                                :value="$isEditing ? data_get($editingRecord, 'isActived') : old('isActived', 1)"
                            />
                            <x-input-error :messages="$errors->get('isActived')" class="mt-2" />
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <x-primary-button>{{ $isEditing ? 'Update Record' : 'Save Record' }}</x-primary-button>

                            <a href="{{ route($indexRouteName) }}" class="ui-button ui-button--secondary">
                                Reset Form
                            </a>
                        </div>
                    </form>
                </x-ui.card>

                <x-ui.table
                    :title="$tableTitle ?? 'Current records'"
                    :description="$tableDescription ?? 'Review all visible records, reopen any row for editing, or remove records that are no longer needed.'"
                >
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{ $fieldLabel }}</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($records as $record)
                            <tr>
                                <td>#{{ $record->id }}</td>
                                <td class="font-medium text-slate-950">{{ data_get($record, $fieldName) }}</td>
                                <td>
                                    <x-ui.badge :variant="(bool) data_get($record, 'isActived', true) ? 'success' : 'danger'">
                                        {{ (bool) data_get($record, 'isActived', true) ? 'Active' : 'Inactive' }}
                                    </x-ui.badge>
                                </td>
                                <td class="text-right">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route($editRouteName, $record->id) }}" class="ui-button ui-button--secondary">
                                            Edit
                                        </a>

                                        <form action="{{ route($deleteRouteName, $record->id) }}" method="POST"
                                            onsubmit="return confirm('Remove this record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ui-button ui-button--danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-slate-500">
                                    No records have been added here yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>

                    <x-slot name="mobile">
                        @forelse ($records as $record)
                            <x-ui.card soft>
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between gap-3">
                                        <div class="text-sm font-semibold text-slate-950">{{ data_get($record, $fieldName) }}</div>
                                        <x-ui.badge :variant="(bool) data_get($record, 'isActived', true) ? 'success' : 'danger'">
                                            {{ (bool) data_get($record, 'isActived', true) ? 'Active' : 'Inactive' }}
                                        </x-ui.badge>
                                    </div>

                                    <div class="text-xs uppercase tracking-[0.2em] text-slate-500">Record #{{ $record->id }}</div>

                                    <div class="flex flex-wrap gap-2">
                                        <a href="{{ route($editRouteName, $record->id) }}" class="ui-button ui-button--secondary">
                                            Edit
                                        </a>

                                        <form action="{{ route($deleteRouteName, $record->id) }}" method="POST"
                                            onsubmit="return confirm('Remove this record?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="ui-button ui-button--danger">Delete</button>
                                        </form>
                                    </div>
                                </div>
                            </x-ui.card>
                        @empty
                            <x-ui.empty-state
                                title="No records yet"
                                description="This section will start filling up as soon as the first record is created."
                            />
                        @endforelse
                    </x-slot>
                </x-ui.table>
            </div>
        </div>
    </div>
</x-app-layout>
