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
                    <div class="grid grid-cols-2 gap-10">
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

                                <!-- Fist Name -->
                                <div class="mt-5">
                                    <x-input-label for="name" :value="__('Fist Name')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                        :value="isset($Section) ? $Section->name : old('name')" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                                 <!-- Full Name -->
                                 <div class="mt-5">
                                    <x-input-label for="hidden" :value="__('Full Name')" />
                                    <x-text-input id="hidden" class="block mt-1 w-full" type="text" name="name"
                                        :value="isset($Section) ? $Section->name : old('name')" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div> 
                                <!--date of birth-->
                                <div class="mt-5">
                                    <x-input-label for="name" :value="__('Date of Birth')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="date" name="name"
                                        :value="isset($Section) ? $Section->name : old('name')" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div> 
                                <!-- Email -->
                                <div class="mt-5">
                                    <x-input-label for="name" :value="__('Email')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="Email" name="name"
                                        :value="isset($Section) ? $Section->name : old('name')" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div> <!-- Year -->
                                <div class="mt-5">
                                    <x-input-label for="name" :value="__('Year')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                        :value="isset($Section) ? $Section->name : old('name')" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div> <!-- Student Photo -->
                                <div class="mt-5">
                                    <x-input-label for="name" :value="__('Student Photo')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="File" name="name"
                                        :value="isset($Section) ? $Section->name : old('name')" required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                               
                            </form>

                        </div>
                        <div>
                             <!-- Last Name -->
                             <div class="mt-5">
                                <x-input-label for="name" :value="__('Last Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                    :value="isset($Section) ? $Section->name : old('name')" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                             <!-- Roll -->
                             <div class="mt-5">
                                <x-input-label for="name" :value="__('Roll')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                    :value="isset($Section) ? $Section->name : old('name')" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div> 
                            <!-- Age -->
                            <div class="mt-5">
                                <x-input-label for="name" :value="__('Age')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="number" name="name"
                                    :value="isset($Section) ? $Section->name : old('name')" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                             <!-- Mobile -->
                            <div class="mt-5">
                                <x-input-label for="name" :value="__('Mobile Number')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="tel" name="name"
                                    :value="isset($Section) ? $Section->name : old('name')" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                             <!-- Class Name -->
                            <div class="mt-5">
                                <x-input-label for="name" :value="__('Class Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                    :value="isset($Section) ? $Section->name : old('name')" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>
                             {{-- <!-- Class Name -->
                            <div class="mt-5">
                                <x-input-label for="name" :value="__('Section Name')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name"
                                    :value="isset($Section) ? $Section->name : old('name')" required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div> --}}
                            
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
                        </div>
                       
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
