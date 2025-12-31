@php
    $isEdit = isset($student);
    $title = $isEdit ? 'Edit Student' : 'Add Student';
@endphp

<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">{{ $title }}</h2>
                <p class="text-xs text-slate-500 mt-1">
                    Fill student information • class • section • fees • year
                </p>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('students.index') }}"
                    class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700 hover:bg-slate-50">
                    ← Back
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

            {{-- Hero --}}
            <div
                class="rounded-3xl border border-slate-200 bg-gradient-to-r from-slate-900 to-slate-700 p-5 text-white shadow-sm">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="text-sm/6 opacity-80">
                            {{ $isEdit ? 'Update student information' : 'Create a new student profile' }}</div>
                        <div class="text-xl font-semibold mt-1">
                            {{ $isEdit ? $student->full_name ?? 'Student #' . $student->id : 'Student Registration' }}
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
                <div class="mb-4 p-4 bg-red-100 border border-red-300 text-red-700 rounded-md">
                    <strong>🚫 Error:</strong> {{ session('error') }}
                </div>
            @endif


            <form method="POST" enctype="multipart/form-data"
                action="{{ $isEdit ? route('students.update', $student->id) : route('students.store') }}"
                class="space-y-3">
                @csrf
                @if ($isEdit)
                    @method('PUT')
                @endif

                <div class="grid grid-cols-12 gap-3">

                    {{-- LEFT: Personal --}}
                    <div class="col-span-12 lg:col-span-7 space-y-3">

                        <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                            <div class="px-4 py-3 border-b border-slate-200">
                                <div class="text-sm font-semibold text-slate-900">Personal Info</div>
                                <div class="text-xs text-slate-500">Name • DOB • Contact</div>
                            </div>

                            <div class="p-4 grid grid-cols-12 gap-3">

                                {{-- First Name --}}
                                <div class="col-span-12 sm:col-span-6">
                                    <x-input-label for="first_name" :value="__('First Name')" />
                                    <x-text-input id="first_name"
                                        class="mt-1 w-full rounded-2xl border-slate-200 focus:border-slate-400 focus:ring-slate-200"
                                        type="text" name="first_name" :value="$isEdit ? $student->first_name : old('first_name')" required />
                                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                                </div>

                                {{-- Last Name --}}
                                <div class="col-span-12 sm:col-span-6">
                                    <x-input-label for="last_name" :value="__('Last Name')" />
                                    <x-text-input id="last_name"
                                        class="mt-1 w-full rounded-2xl border-slate-200 focus:border-slate-400 focus:ring-slate-200"
                                        type="text" name="last_name" :value="$isEdit ? $student->last_name : old('last_name')" required />
                                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                                </div>

                                {{-- Full Name --}}
                                <div class="col-span-12">
                                    <x-input-label for="full_name" :value="__('Full Name')" />
                                    <x-text-input id="full_name"
                                        class="mt-1 w-full rounded-2xl border-slate-200 focus:border-slate-400 focus:ring-slate-200"
                                        type="text" name="full_name" :value="$isEdit ? $student->full_name : old('full_name')" required />
                                    <p class="mt-1 text-[11px] text-slate-500">
                                        Auto fill from First + Last name (you can edit manually).
                                    </p>
                                    <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
                                </div>

                                {{-- DOB --}}
                                <div class="col-span-12 sm:col-span-6">
                                    <x-input-label for="dob" :value="__('Date of Birth')" />
                                    <x-text-input id="dob"
                                        class="mt-1 w-full rounded-2xl border-slate-200 focus:border-slate-400 focus:ring-slate-200"
                                        type="date" name="dob" :value="$isEdit ? $student->dob : old('dob')" required />
                                    <x-input-error :messages="$errors->get('dob')" class="mt-2" />
                                </div>

                                {{-- Roll --}}
                                <div class="col-span-12 sm:col-span-6">
                                    <x-input-label for="roll" :value="__('Roll')" />
                                    <x-text-input id="roll"
                                        class="mt-1 w-full rounded-2xl border-slate-200 focus:border-slate-400 focus:ring-slate-200"
                                        type="number" name="roll" :value="$isEdit ? $student->roll : old('roll')" required />
                                    <x-input-error :messages="$errors->get('roll')" class="mt-2" />
                                </div>

                                {{-- Email --}}
                                <div class="col-span-12 sm:col-span-6">
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email"
                                        class="mt-1 w-full rounded-2xl border-slate-200 focus:border-slate-400 focus:ring-slate-200"
                                        type="email" name="email" :value="$isEdit ? $student->email : old('email')" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                {{-- Mobile --}}
                                <div class="col-span-12 sm:col-span-6">
                                    <x-input-label for="mobile" :value="__('Mobile Number')" />
                                    <x-text-input id="mobile"
                                        class="mt-1 w-full rounded-2xl border-slate-200 focus:border-slate-400 focus:ring-slate-200"
                                        type="tel" name="mobile" :value="$isEdit ? $student->mobile : old('mobile')" required />
                                    <x-input-error :messages="$errors->get('mobile')" class="mt-2" />
                                </div>

                            </div>
                        </div>

                        {{-- Photo --}}
                        <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                            <div class="px-4 py-3 border-b border-slate-200">
                                <div class="text-sm font-semibold text-slate-900">Student Photo</div>
                                <div class="text-xs text-slate-500">Upload or change photo</div>
                            </div>

                            <div class="p-4 grid grid-cols-12 gap-3 items-start">
                                <div class="col-span-12 sm:col-span-7">
                                    <x-input-label for="photo" :value="__('Photo')" />
                                    <input id="photo" name="photo" type="file" accept="image/*"
                                        class="mt-1 block w-full rounded-2xl border border-slate-200 bg-white text-sm file:mr-3 file:rounded-xl file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-800" />
                                    <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                                    <p class="mt-1 text-[11px] text-slate-500">PNG/JPG recommended.</p>
                                </div>

                                <div class="col-span-12 sm:col-span-5">
                                    <div class="text-xs text-slate-500 mb-2">Preview</div>
                                    <div class="rounded-3xl border border-slate-200 bg-slate-50 overflow-hidden">
                                        <div class="aspect-[4/3] flex items-center justify-center">
                                            @php
                                                $photoUrl = null;
                                                if ($isEdit && !empty($student->photo)) {
                                                    // আপনার storage path অনুযায়ী adjust করতে পারেন
                                                    $photoUrl = asset('storage/' . $student->photo);
                                                }
                                            @endphp

                                            @if ($photoUrl)
                                                <img id="photoPreview" src="{{ $photoUrl }}" alt="Student Photo"
                                                    class="h-full w-full object-cover" />
                                            @else
                                                <div class="text-sm text-slate-500">No photo</div>
                                                <img id="photoPreview" class="hidden h-full w-full object-cover"
                                                    alt="Preview" />
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- RIGHT: Academic + Status --}}
                    <div class="col-span-12 lg:col-span-5 space-y-3">

                        <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                            <div class="px-4 py-3 border-b border-slate-200">
                                <div class="text-sm font-semibold text-slate-900">Academic & Fees</div>
                                <div class="text-xs text-slate-500">Class • Section • Fee type • Year</div>
                            </div>

                            <div class="p-4 grid grid-cols-12 gap-3">

                                {{-- Age --}}
                                <div class="col-span-12 sm:col-span-6">
                                    <x-input-label for="age" :value="__('Age')" />
                                    <x-text-input id="age"
                                        class="mt-1 w-full rounded-2xl border-slate-200 focus:border-slate-400 focus:ring-slate-200"
                                        type="number" name="age" :value="$isEdit ? $student->age : old('age')" required />
                                    <x-input-error :messages="$errors->get('age')" class="mt-2" />
                                </div>

                                {{-- Scholarship --}}
                                <div class="col-span-12 sm:col-span-6">
                                    <x-input-label for="scholarship_amount" :value="__('Scholarship Amount')" />
                                    <x-text-input id="scholarship_amount"
                                        class="mt-1 w-full rounded-2xl border-slate-200 focus:border-slate-400 focus:ring-slate-200"
                                        type="text" name="scholarship_amount" :value="$isEdit ? $student->scholarship_amount : old('scholarship_amount')" />
                                    <x-input-error :messages="$errors->get('scholarship_amount')" class="mt-2" />
                                </div>

                                {{-- Class --}}
                                <div class="col-span-12">
                                    <x-input-label for="class_id" :value="__('Class Name')" />
                                    <select id="class_id" name="class_id"
                                        class="mt-1 block w-full rounded-2xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                {{ $isEdit && (string) $student->class_id === (string) $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('class_id')" class="mt-2" />
                                </div>

                                {{-- Section --}}
                                <div class="col-span-12">
                                    <x-input-label for="section_id" :value="__('Section Name')" />
                                    <select id="section_id" name="section_id"
                                        class="mt-1 block w-full rounded-2xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        @foreach ($sections as $section)
                                            <option value="{{ $section->id }}"
                                                {{ $isEdit && (string) $student->section_id === (string) $section->id ? 'selected' : '' }}>
                                                {{ $section->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('section_id')" class="mt-2" />
                                </div>

                                {{-- Fees Type --}}
                                <div class="col-span-12">
                                    <x-input-label for="fees_type_id" :value="__('Fees Type')" />
                                    <select id="fees_type_id" name="fees_type_id"
                                        class="mt-1 block w-full rounded-2xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        @foreach ($fees_types as $fees_type)
                                            <option value="{{ $fees_type->id }}"
                                                {{ $isEdit && (string) $student->fees_type_id === (string) $fees_type->id ? 'selected' : '' }}>
                                                {{ $fees_type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('fees_type_id')" class="mt-2" />
                                </div>

                                {{-- Academic Year --}}
                                <div class="col-span-12">
                                    <x-input-label for="academic_year_id" :value="__('Academic Year')" />
                                    <select id="academic_year_id" name="academic_year_id"
                                        class="mt-1 block w-full rounded-2xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        @foreach ($academic_years as $year)
                                            <option value="{{ $year->id }}"
                                                {{ $isEdit && (string) $student->academic_year_id === (string) $year->id ? 'selected' : '' }}>
                                                {{ $year->year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('academic_year_id')" class="mt-2" />
                                </div>

                                {{-- Status --}}
                                <div class="col-span-12">
                                    <x-input-label for="isActived" :value="__('Active Status')" />
                                    <select id="isActived" name="isActived"
                                        class="mt-1 block w-full rounded-2xl border-slate-200 text-sm focus:border-slate-400 focus:ring-slate-200">
                                        <option value="1" {{ $isEdit && $student->isActived ? 'selected' : '' }}>
                                            Active</option>
                                        <option value="0"
                                            {{ $isEdit && !$student->isActived ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('isActived')" class="mt-2" />
                                </div>

                            </div>
                        </div>

                        {{-- Sticky Action Card --}}
                        <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                            <div class="px-4 py-3 border-b border-slate-200">
                                <div class="text-sm font-semibold text-slate-900">Actions</div>
                                <div class="text-xs text-slate-500">Save changes safely</div>
                            </div>

                            <div class="p-4 flex items-center justify-between gap-3">
                                <div class="text-xs text-slate-500">
                                    {{ $isEdit ? 'Updating student profile.' : 'Creating new student profile.' }}
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('students.index') }}"
                                        class="inline-flex items-center rounded-xl border border-slate-200 bg-white px-4 py-2 text-sm text-slate-700 hover:bg-slate-50">
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
        // Auto full name
        const firstNameEl = document.getElementById('first_name');
        const lastNameEl = document.getElementById('last_name');
        const fullNameEl = document.getElementById('full_name');

        function combineNames() {
            if (!firstNameEl || !lastNameEl || !fullNameEl) return;
            const first = (firstNameEl.value || '').trim();
            const last = (lastNameEl.value || '').trim();
            const combined = (first + ' ' + last).trim();

            // শুধু create mode এ auto-fill; edit mode এ ইউজার manually change করলে overwrite না করা ভালো
            @if (!isset($student))
                fullNameEl.value = combined;
            @endif
        }

        firstNameEl?.addEventListener('input', combineNames);
        lastNameEl?.addEventListener('input', combineNames);

        // Photo preview
        const photoEl = document.getElementById('photo');
        const previewEl = document.getElementById('photoPreview');

        photoEl?.addEventListener('change', (e) => {
            const file = e.target.files?.[0];
            if (!file || !previewEl) return;

            const url = URL.createObjectURL(file);
            previewEl.src = url;
            previewEl.classList.remove('hidden');
        });
    </script>
</x-app-layout>
