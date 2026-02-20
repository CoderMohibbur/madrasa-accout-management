@php
    /** @var \App\Models\Student|null $student */
    $isEdit = isset($student);
    $title = $isEdit ? 'Edit Student' : 'Student Admission';

    $safeDate = function ($value) {
        if (!$value) return null;
        try {
            return \Illuminate\Support\Carbon::parse($value)->toDateString();
        } catch (\Throwable $e) {
            return null;
        }
    };

    $dobVal = $isEdit ? $safeDate($student->dob ?? null) : old('dob');
    $bStartVal = $isEdit ? $safeDate($student->boarding_start_date ?? null) : old('boarding_start_date');
    $bEndVal   = $isEdit ? $safeDate($student->boarding_end_date ?? null) : old('boarding_end_date');

    $txCenterUrl = null;
    if (\Illuminate\Support\Facades\Route::has('transactions.center')) {
        $txCenterUrl = route('transactions.center');
    } elseif (\Illuminate\Support\Facades\Route::has('transaction-center.index')) {
        $txCenterUrl = route('transaction-center.index');
    }

    $photoUrl = null;
    if ($isEdit && !empty($student->photo)) {
        $photoUrl = asset('storage/' . $student->photo);
    }

    // Alpine boolean init
    $initBoarding = $isEdit
        ? ((bool)($student->is_boarding ?? false))
        : (old('is_boarding') ? true : false);
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 dark:text-slate-100 leading-tight">{{ $title }}</h2>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Fill student information • class • section • fees • year • boarding
                </p>
            </div>

            <div class="flex items-center gap-2 flex-wrap">
                <a href="{{ route('students.index') }}"
                   class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                    ← Back
                </a>

                @if($isEdit)
                    <a href="{{ route('students.show', $student) }}"
                       class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                        View Profile
                    </a>
                @endif

                @if($txCenterUrl && $isEdit)
                    <a href="{{ $txCenterUrl }}?student_id={{ $student->id }}"
                       class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-3 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                        Transaction Center
                    </a>
                @endif
            </div>
        </div>
    </x-slot>

    {{-- x-cloak helper (layout এ না থাকলে এখানেই কাজ করবে) --}}
    <style>
        [x-cloak] { display:none !important; }
    </style>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Hero --}}
            <div class="rounded-3xl border border-slate-200 dark:border-white/10 bg-gradient-to-r from-slate-900 to-slate-700 p-5 text-white shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div class="min-w-0">
                        <div class="text-sm/6 opacity-80">
                            {{ $isEdit ? 'Update student information' : 'Create a new student profile' }}
                        </div>
                        <div class="text-xl font-semibold mt-1 truncate">
                            {{ $isEdit ? ($student->full_name ?? 'Student #'.$student->id) : 'Student Registration' }}
                        </div>
                        <div class="text-xs opacity-80 mt-1">
                            Fields marked required must be filled.
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-2 w-full sm:w-auto">
                        <div class="rounded-2xl bg-white/10 border border-white/10 p-3">
                            <div class="text-[11px] opacity-80">Mode</div>
                            <div class="text-sm font-semibold">{{ $isEdit ? 'Edit' : 'Create' }}</div>
                        </div>
                        <div class="rounded-2xl bg-white/10 border border-white/10 p-3">
                            <div class="text-[11px] opacity-80">Status</div>
                            <div class="text-sm font-semibold">
                                {{ $isEdit ? ($student->isActived ? 'Active' : 'Inactive') : 'New' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <x-toast-success />

            @if (session('error'))
                <div class="p-4 rounded-2xl border border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-900/40 dark:bg-rose-900/20 dark:text-rose-200">
                    <strong>🚫 Error:</strong> {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-2xl border border-rose-200 bg-rose-50 text-rose-800 dark:border-rose-900/40 dark:bg-rose-900/20 dark:text-rose-200">
                    <div class="font-semibold text-sm">Please fix the errors below.</div>
                    <ul class="mt-2 text-xs list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" enctype="multipart/form-data"
                  action="{{ $isEdit ? route('students.update', $student->id) : route('students.store') }}"
                  class="space-y-3"
                  x-data="{ boarding: {{ $initBoarding ? 'true' : 'false' }} }">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="grid grid-cols-12 gap-3">

                    {{-- LEFT: Personal --}}
                    <div class="col-span-12 lg:col-span-7 space-y-3">

                        {{-- Personal Info --}}
                        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm overflow-hidden">
                            <div class="px-4 py-3 border-b border-slate-200 dark:border-white/10">
                                <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Personal Info</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">Full name • Father • DOB • Contact</div>
                            </div>

                            <div class="p-4 grid grid-cols-12 gap-3">

                                {{-- Full Name --}}
                                <div class="col-span-12">
                                    <x-input-label for="full_name" :value="__('Full Name *')" />
                                    <x-text-input id="full_name"
                                                  class="mt-1 w-full rounded-2xl border-slate-200 dark:border-white/10 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                                  type="text" name="full_name"
                                                  :value="$isEdit ? $student->full_name : old('full_name')" required />
                                    <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
                                </div>

                                {{-- Father Name --}}
                                <div class="col-span-12">
                                    <x-input-label for="father_name" :value="__('Father Name (optional)')" />
                                    <x-text-input id="father_name"
                                                  class="mt-1 w-full rounded-2xl border-slate-200 dark:border-white/10 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                                  type="text" name="father_name"
                                                  :value="$isEdit ? $student->father_name : old('father_name')" />
                                    <x-input-error :messages="$errors->get('father_name')" class="mt-2" />
                                </div>

                                {{-- DOB --}}
                                <div class="col-span-12 sm:col-span-6">
                                    <x-input-label for="dob" :value="__('Date of Birth (optional)')" />
                                    <x-text-input id="dob"
                                                  class="mt-1 w-full rounded-2xl border-slate-200 dark:border-white/10 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                                  type="date" name="dob"
                                                  :value="$dobVal" />
                                    <x-input-error :messages="$errors->get('dob')" class="mt-2" />
                                </div>

                                {{-- Roll --}}
                                <div class="col-span-12 sm:col-span-6">
                                    <x-input-label for="roll" :value="__('Roll *')" />
                                    <x-text-input id="roll"
                                                  class="mt-1 w-full rounded-2xl border-slate-200 dark:border-white/10 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                                  type="number" name="roll" min="1"
                                                  :value="$isEdit ? $student->roll : old('roll')" required />
                                    <x-input-error :messages="$errors->get('roll')" class="mt-2" />
                                </div>

                                {{-- Email --}}
                                <div class="col-span-12 sm:col-span-6">
                                    <x-input-label for="email" :value="__('Email (optional)')" />
                                    <x-text-input id="email"
                                                  class="mt-1 w-full rounded-2xl border-slate-200 dark:border-white/10 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                                  type="email" name="email"
                                                  :value="$isEdit ? $student->email : old('email')" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                {{-- Mobile --}}
                                <div class="col-span-12 sm:col-span-6">
                                    <x-input-label for="mobile" :value="__('Mobile Number *')" />
                                    <x-text-input id="mobile"
                                                  class="mt-1 w-full rounded-2xl border-slate-200 dark:border-white/10 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                                  type="tel" name="mobile"
                                                  :value="$isEdit ? $student->mobile : old('mobile')" required />
                                    <x-input-error :messages="$errors->get('mobile')" class="mt-2" />
                                </div>

                                {{-- Address --}}
                                <div class="col-span-12">
                                    <x-input-label for="address" :value="__('Address (optional)')" />
                                    <textarea id="address" name="address" rows="3"
                                              class="mt-1 w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100
                                                     focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">{{ $isEdit ? $student->address : old('address') }}</textarea>
                                    <x-input-error :messages="$errors->get('address')" class="mt-2" />
                                </div>

                            </div>
                        </div>

                        {{-- Boarding --}}
                        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm overflow-hidden">
                            <div class="px-4 py-3 border-b border-slate-200 dark:border-white/10">
                                <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Boarding</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">Hostel status • dates • note</div>
                            </div>

                            <div class="p-4 space-y-3">
                                <label class="inline-flex items-center gap-2 text-sm">
                                    <input type="checkbox" name="is_boarding" value="1"
                                           x-model="boarding"
                                           class="rounded border-slate-200 dark:border-white/10 text-emerald-600 focus:ring-emerald-500">
                                    <span class="text-slate-700 dark:text-slate-200">Is Boarding Student?</span>
                                </label>

                                <div x-show="boarding" x-cloak class="grid grid-cols-12 gap-3">
                                    <div class="col-span-12 sm:col-span-6">
                                        <x-input-label for="boarding_start_date" :value="__('Boarding Start Date (optional)')" />
                                        <x-text-input id="boarding_start_date"
                                                      class="mt-1 w-full rounded-2xl border-slate-200 dark:border-white/10 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                                      type="date" name="boarding_start_date"
                                                      :value="$bStartVal" />
                                        <x-input-error :messages="$errors->get('boarding_start_date')" class="mt-2" />
                                    </div>

                                    <div class="col-span-12 sm:col-span-6">
                                        <x-input-label for="boarding_end_date" :value="__('Boarding End Date (optional)')" />
                                        <x-text-input id="boarding_end_date"
                                                      class="mt-1 w-full rounded-2xl border-slate-200 dark:border-white/10 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                                      type="date" name="boarding_end_date"
                                                      :value="$bEndVal" />
                                        <x-input-error :messages="$errors->get('boarding_end_date')" class="mt-2" />
                                    </div>

                                    <div class="col-span-12">
                                        <x-input-label for="boarding_note" :value="__('Boarding Note (optional)')" />
                                        <textarea id="boarding_note" name="boarding_note" rows="2"
                                                  class="mt-1 w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100
                                                         focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 text-sm">{{ $isEdit ? $student->boarding_note : old('boarding_note') }}</textarea>
                                        <x-input-error :messages="$errors->get('boarding_note')" class="mt-2" />
                                    </div>
                                </div>

                                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                                    Note: If boarding unchecked, controller auto clears boarding fields.
                                </p>
                            </div>
                        </div>

                        {{-- Photo --}}
                        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm overflow-hidden">
                            <div class="px-4 py-3 border-b border-slate-200 dark:border-white/10">
                                <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Student Photo</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">Upload or change photo</div>
                            </div>

                            <div class="p-4 grid grid-cols-12 gap-3 items-start">
                                <div class="col-span-12 sm:col-span-7">
                                    <x-input-label for="photo" :value="__('Photo (optional)')" />
                                    <input id="photo" name="photo" type="file" accept="image/*"
                                           class="mt-1 block w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 text-sm text-slate-900 dark:text-slate-100
                                                  file:mr-3 file:rounded-xl file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800" />
                                    <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                                    <p class="mt-1 text-[11px] text-slate-500 dark:text-slate-400">Max 2MB (jpg/png/webp).</p>
                                </div>

                                <div class="col-span-12 sm:col-span-5">
                                    <div class="text-xs text-slate-500 dark:text-slate-400 mb-2">Preview</div>
                                    <div class="rounded-3xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-slate-950 overflow-hidden">
                                        <div class="aspect-[4/3] flex items-center justify-center">
                                            @if ($photoUrl)
                                                <img id="photoPreview" src="{{ $photoUrl }}" alt="Student Photo"
                                                     class="h-full w-full object-cover" />
                                            @else
                                                <div id="photoPlaceholder" class="text-sm text-slate-500 dark:text-slate-400">No photo</div>
                                                <img id="photoPreview" class="hidden h-full w-full object-cover" alt="Preview" />
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT: Academic + Status --}}
                    <div class="col-span-12 lg:col-span-5 space-y-3">

                        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm overflow-hidden">
                            <div class="px-4 py-3 border-b border-slate-200 dark:border-white/10">
                                <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Academic & Fees</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">Class • Section • Fee type • Year</div>
                            </div>

                            <div class="p-4 grid grid-cols-12 gap-3">

                                {{-- Age --}}
                                <div class="col-span-12 sm:col-span-6">
                                    <x-input-label for="age" :value="__('Age (optional)')" />
                                    <x-text-input id="age"
                                                  class="mt-1 w-full rounded-2xl border-slate-200 dark:border-white/10 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                                  type="text" name="age"
                                                  :value="$isEdit ? $student->age : old('age')" />
                                    <x-input-error :messages="$errors->get('age')" class="mt-2" />
                                </div>

                                {{-- Scholarship --}}
                                <div class="col-span-12 sm:col-span-6">
                                    <x-input-label for="scholarship_amount" :value="__('Scholarship Amount (optional)')" />
                                    <x-text-input id="scholarship_amount"
                                                  class="mt-1 w-full rounded-2xl border-slate-200 dark:border-white/10 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                                  type="number" step="0.01" min="0" name="scholarship_amount"
                                                  :value="$isEdit ? $student->scholarship_amount : old('scholarship_amount')" />
                                    <x-input-error :messages="$errors->get('scholarship_amount')" class="mt-2" />
                                </div>

                                {{-- Class --}}
                                <div class="col-span-12">
                                    <x-input-label for="class_id" :value="__('Class Name *')" />
                                    <select id="class_id" name="class_id"
                                            class="mt-1 block w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 text-sm text-slate-900 dark:text-slate-100
                                                   focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required>
                                        <option value="">Select class</option>
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                @selected(($isEdit ? $student->class_id : old('class_id')) == $class->id)>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('class_id')" class="mt-2" />
                                </div>

                                {{-- Section --}}
                                <div class="col-span-12">
                                    <x-input-label for="section_id" :value="__('Section Name *')" />
                                    <select id="section_id" name="section_id"
                                            class="mt-1 block w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 text-sm text-slate-900 dark:text-slate-100
                                                   focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required>
                                        <option value="">Select section</option>
                                        @foreach ($sections as $section)
                                            <option value="{{ $section->id }}"
                                                @selected(($isEdit ? $student->section_id : old('section_id')) == $section->id)>
                                                {{ $section->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('section_id')" class="mt-2" />
                                </div>

                                {{-- Fees Type --}}
                                <div class="col-span-12">
                                    <x-input-label for="fees_type_id" :value="__('Fees Type (optional)')" />
                                    <select id="fees_type_id" name="fees_type_id"
                                            class="mt-1 block w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 text-sm text-slate-900 dark:text-slate-100
                                                   focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="">Select fees type</option>
                                        @foreach ($fees_types as $fees_type)
                                            <option value="{{ $fees_type->id }}"
                                                @selected(($isEdit ? $student->fees_type_id : old('fees_type_id')) == $fees_type->id)>
                                                {{ $fees_type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('fees_type_id')" class="mt-2" />
                                </div>

                                {{-- Academic Year --}}
                                <div class="col-span-12">
                                    <x-input-label for="academic_year_id" :value="__('Academic Year *')" />
                                    <select id="academic_year_id" name="academic_year_id"
                                            class="mt-1 block w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 text-sm text-slate-900 dark:text-slate-100
                                                   focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500"
                                            required>
                                        <option value="">Select year</option>
                                        @foreach ($academic_years as $year)
                                            @php $label = $year->academic_years ?? $year->year ?? ('Year #'.$year->id); @endphp
                                            <option value="{{ $year->id }}"
                                                @selected(($isEdit ? $student->academic_year_id : old('academic_year_id')) == $year->id)>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('academic_year_id')" class="mt-2" />
                                </div>

                                {{-- Status --}}
                                <div class="col-span-12">
                                    <x-input-label for="isActived" :value="__('Active Status')" />
                                    <select id="isActived" name="isActived"
                                            class="mt-1 block w-full rounded-2xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 text-sm text-slate-900 dark:text-slate-100
                                                   focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500">
                                        <option value="1" @selected(($isEdit ? $student->isActived : old('isActived', 1)) == 1)>Active</option>
                                        <option value="0" @selected(($isEdit ? $student->isActived : old('isActived', 1)) == 0)>Inactive</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('isActived')" class="mt-2" />
                                </div>

                            </div>
                        </div>

                        {{-- Action Card --}}
                        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-3xl shadow-sm overflow-hidden">
                            <div class="px-4 py-3 border-b border-slate-200 dark:border-white/10">
                                <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">Actions</div>
                                <div class="text-xs text-slate-500 dark:text-slate-400">Save changes safely</div>
                            </div>

                            <div class="p-4 flex items-center justify-between gap-3">
                                <div class="text-xs text-slate-500 dark:text-slate-400">
                                    {{ $isEdit ? 'Updating student profile.' : 'Creating new student profile.' }}
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('students.index') }}"
                                       class="inline-flex items-center rounded-xl border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-950 px-4 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800">
                                        Cancel
                                    </a>

                                    <button type="submit"
                                            class="inline-flex items-center rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white hover:bg-slate-800">
                                        {{ $isEdit ? 'Update' : 'Save' }}
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

            </form>
        </div>
    </div>

    <script>
        (function () {
            const photoEl = document.getElementById('photo');
            const previewEl = document.getElementById('photoPreview');
            const placeholderEl = document.getElementById('photoPlaceholder');
            let lastUrl = null;

            photoEl?.addEventListener('change', (e) => {
                const file = e.target.files?.[0];
                if (!file || !previewEl) return;

                if (lastUrl) URL.revokeObjectURL(lastUrl);
                lastUrl = URL.createObjectURL(file);

                previewEl.src = lastUrl;
                previewEl.classList.remove('hidden');
                placeholderEl?.classList.add('hidden');
            });
        })();
    </script>
</x-app-layout>