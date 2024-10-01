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
                    <form method="POST" enctype="multipart/form-data"
                        action="{{ isset($student) ? route('students.update', $student->id) : route('students.store') }}">

                        @csrf

                        @if (isset($student))
                            @method('PUT') {{-- Use PUT for update --}}
                        @endif
                        <div class="grid grid-cols-2 gap-10">


                            <div>

                                <!-- First Name -->
                                <div class="mt-5">
                                    <x-input-label for="first_name" :value="__('First Name')" />
                                    <x-text-input id="first_name" class="block mt-1 w-full" type="text"
                                        name="first_name" :value="isset($student) ? $student->first_name : old('first_name')" required />
                                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                                </div>

                                <!-- Last Name -->
                                <div class="mt-5">
                                    <x-input-label for="last_name" :value="__('Last Name')" />
                                    <x-text-input id="last_name" class="block mt-1 w-full" type="text"
                                        name="last_name" :value="isset($student) ? $student->last_name : old('last_name')" required />
                                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                                </div>

                                <!-- Full Name -->
                                <div class="mt-5">
                                    <x-input-label for="full_name" :value="__('Full Name')" />
                                    <x-text-input id="full_name" class="block mt-1 w-full" type="text"
                                        name="full_name" :value="isset($student) ? $student->full_name : old('full_name')" required />
                                    <x-input-error :messages="$errors->get('full_name')" class="mt-2" />
                                </div>

                                <!-- Date of Birth -->
                                <div class="mt-5">
                                    <x-input-label for="dob" :value="__('Date of Birth')" />
                                    <x-text-input id="dob" class="block mt-1 w-full" type="date" name="dob"
                                        :value="isset($student) ? $student->dob : old('dob')" required />
                                    <x-input-error :messages="$errors->get('dob')" class="mt-2" />
                                </div>

                                <!-- Roll -->
                                <div class="mt-5">
                                    <x-input-label for="roll" :value="__('Roll')" />
                                    <x-text-input id="roll" class="block mt-1 w-full" type="number" name="roll"
                                        :value="isset($student) ? $student->roll : old('roll')" required />
                                    <x-input-error :messages="$errors->get('roll')" class="mt-2" />
                                </div>

                                <!-- Email -->
                                <div class="mt-5">
                                    <x-input-label for="email" :value="__('Email')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                        :value="isset($student) ? $student->email : old('email')" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <!-- Mobile -->
                                <div class="mt-5">
                                    <x-input-label for="mobile" :value="__('Mobile Number')" />
                                    <x-text-input id="mobile" class="block mt-1 w-full" type="tel" name="mobile"
                                        :value="isset($student) ? $student->mobile : old('mobile')" required />
                                    <x-input-error :messages="$errors->get('mobile')" class="mt-2" />
                                </div>


                            </div>
                            <div>
                                <!-- Age -->
                                <div class="mt-5">
                                    <x-input-label for="age" :value="__('Age')" />
                                    <x-text-input id="age" class="block mt-1 w-full" type="number" name="age"
                                        :value="isset($student) ? $student->age : old('age')" required />
                                    <x-input-error :messages="$errors->get('age')" class="mt-2" />
                                </div>
                                <!-- Student Photo -->
                                <div class="mt-5">
                                    <x-input-label for="photo" :value="__('Student Photo')" />
                                    <x-text-input id="photo" class="block mt-1 w-full" type="file" name="photo"
                                        :value="isset($student) ? $student->photo : old('photo')" />
                                    <x-input-error :messages="$errors->get('photo')" class="mt-2" />
                                </div>

                                <!-- Class ID -->
                                <div class="mt-5">
                                    <x-input-label for="class_id" :value="__('Class Name')" />
                                    <select id="class_id" name="class_id" class="block mt-1 w-full">
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                {{ isset($student) && $student->class_id == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('class_id')" class="mt-2" />
                                </div>

                                <!-- Section ID -->
                                <div class="mt-5">
                                    <x-input-label for="section_id" :value="__('Section Name')" />
                                    <select id="section_id" name="section_id" class="block mt-1 w-full">
                                        @foreach ($sections as $section)
                                            <option value="{{ $section->id }}"
                                                {{ isset($student) && $student->section_id == $section->id ? 'selected' : '' }}>
                                                {{ $section->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('section_id')" class="mt-2" />
                                </div>

                                <!-- Fees Type ID -->
                                <div class="mt-5">
                                    <x-input-label for="fees_type_id" :value="__('Fees Type')" />
                                    <select id="fees_type_id" name="fees_type_id" class="block mt-1 w-full">
                                        @foreach ($fees_types as $fees_type)
                                            <option value="{{ $fees_type->id }}"
                                                {{ isset($student) && $student->fees_type_id == $fees_type->id ? 'selected' : '' }}>
                                                {{ $fees_type->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('fees_type_id')" class="mt-2" />
                                </div>

                                <!-- Academic Year -->
                                <div class="mt-5">
                                    <x-input-label for="academic_year_id" :value="__('Academic Year')" />
                                    <select id="academic_year_id" name="academic_year_id" class="block mt-1 w-full">
                                        @foreach ($academic_years as $year)
                                            <option value="{{ $year->id }}"
                                                {{ isset($student) && $student->academic_year_id == $year->id ? 'selected' : '' }}>
                                                {{ $year->year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('academic_year_id')" class="mt-2" />
                                </div>
                                <!-- Status -->
                                <div class="mt-5">
                                    <x-input-label for="isActived" :value="__('Active Status')" />
                                    <select id="isActived" name="isActived" class="block mt-1 w-full">
                                        <option value="1"
                                            {{ isset($student) && $student->isActived ? 'selected' : '' }}>Active
                                        </option>
                                        <option value="0"
                                            {{ isset($student) && !$student->isActived ? 'selected' : '' }}>Inactive
                                        </option>
                                    </select>
                                    <x-input-error :messages="$errors->get('isActived')" class="mt-2" />
                                </div>

                                <!-- Save/Update Button -->
                                <div class="flex items-center justify-end mt-4">
                                    <x-primary-button>
                                        @if (isset($student))
                                            {{ __('Update') }}
                                        @else
                                            {{ __('Save') }}
                                        @endif
                                    </x-primary-button>
                                </div>
                            </div>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
