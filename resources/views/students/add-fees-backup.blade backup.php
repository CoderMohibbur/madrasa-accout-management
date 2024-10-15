<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Add Fees') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <x-toast-success />
                    <form method="POST" enctype="multipart/form-data"
                        action="{{ isset($transactions) ? route('add_student_fees.update', $transactions->id) : route('add_student_fees.index') }}">

                        @csrf

                        @if (isset($transactions))
                            @method('PUT') {{-- Use PUT for update --}}
                        @endif
                        <div class="grid grid-cols-2 gap-10">
                            <div>
                                <!-- Student Id -->
                                <div class="mt-5">
                                    <x-input-label for="student_id" :value="__('Student Id')" />
                                    <select id="student_id" name="student_id" class="block mt-1 w-full">
                                        @foreach ($students as $student)
                                            <option value="{{ $student->id }}"
                                                {{ isset($transactions) && $student->student_id == $student->id ? 'selected' : '' }}>
                                                {{ $student->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                                </div>

                                <!-- Fess Type Id -->
                                <div class="mt-5">
                                    <x-input-label for="fess_type_id" :value="__('Fess Type Id')" />
                                    <select id="fess_type_id" name="fess_type_id" class="block mt-1 w-full">
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                {{ isset($transactions) && $class->fess_type_id == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('fess_type_id')" class="mt-2" />
                                </div>

                                <!-- transactions_type_id -->
                                <div class="mt-5">
                                    <x-input-label for="transactions_type_id" :value="__('Transactions Type Id')" />
                                    <select id="transactions_type_id" name="transactions_type_id"
                                        class="block mt-1 w-full">
                                        @foreach ($transactionss as $transaction)
                                            <option value="{{ $transaction->id }}"
                                                {{ isset($transactions) && $transaction->transactions_type_id == $transaction->id ? 'selected' : '' }}>
                                                {{ $transaction->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('transactions_type_id')" class="mt-2" />
                                </div>

                                <!-- student_book_number -->
                                <div class="mt-5">
                                    <x-input-label for="student_book_number" :value="__('Student Book Number')" />
                                    <x-text-input id="student_book_number" class="block mt-1 w-full" type="text"
                                        name="student_book_number" :value="isset($transactions)
                                            ? $transactions->student_book_number
                                            : old('student_book_number')" required />
                                    <x-input-error :messages="$errors->get('student_book_number')" class="mt-2" />
                                </div>

                                <!-- recipt_no -->
                                <div class="mt-5">
                                    <x-input-label for="recipt_no" :value="__('Recipt No')" />
                                    <x-text-input id="recipt_no" class="block mt-1 w-full" type="text"
                                        name="recipt_no" :value="isset($transactions) ? $transactions->recipt_no : old('recipt_no')" required />
                                    <x-input-error :messages="$errors->get('recipt_no')" class="mt-2" />
                                </div>

                                <!-- monthly_fees -->
                                <div class="mt-5">
                                    <x-input-label for="monthly_fees" :value="__('Monthly Fees')" />
                                    <x-text-input id="monthly_fees" class="block mt-1 w-full" type="text"
                                        name="monthly_fees" :value="isset($transactions)
                                            ? $transactions->monthly_fees
                                            : old('monthly_fees')" required />
                                    <x-input-error :messages="$errors->get('monthly_fees')" class="mt-2" />
                                </div>

                                <!-- boarding_fees -->
                                <div class="mt-5">
                                    <x-input-label for="boarding_fees" :value="__('Boarding Fees')" />
                                    <x-text-input id="boarding_fees" class="block mt-1 w-full" type="text"
                                        name="boarding_fees" :value="isset($transactions)
                                            ? $transactions->boarding_fees
                                            : old('boarding_fees')" />
                                    <x-input-error :messages="$errors->get('boarding_fees')" class="mt-2" />
                                </div>

                                <!-- management_fees -->
                                <div class="mt-5">
                                    <x-input-label for="management_fees" :value="__('Management Fees')" />
                                    <x-text-input id="management_fees" class="block mt-1 w-full" type="text"
                                        name="management_fees" :value="isset($transactions)
                                            ? $transactions->management_fees
                                            : old('management_fees')" required />
                                    <x-input-error :messages="$errors->get('management_fees')" class="mt-2" />
                                </div>

                                <!-- exam_fees -->
                                <div class="mt-5">
                                    <x-input-label for="exam_fees" :value="__('Exam Fees')" />
                                    <x-text-input id="exam_fees" class="block mt-1 w-full" type="text"
                                        name="exam_fees" :value="isset($transactions) ? $transactions->exam_fees : old('exam_fees')" required />
                                    <x-input-error :messages="$errors->get('exam_fees')" class="mt-2" />
                                </div>

                                <!-- others_fees -->
                                <div class="mt-5">
                                    <x-input-label for="others_fees" :value="__('Others Fees')" />
                                    <x-text-input id="others_fees" class="block mt-1 w-full" type="text"
                                        name="others_fees" :value="isset($transactions)
                                            ? $transactions->others_fees
                                            : old('others_fees')" required />
                                    <x-input-error :messages="$errors->get('others_fees')" class="mt-2" />

                                </div>

                                <!-- total_fees -->
                                <div class="mt-5">
                                    <x-input-label for="total_fees" :value="__('Total Fees')" />
                                    <x-text-input id="total_fees" class="block mt-1 w-full" type="text"
                                        name="total_fees" :value="isset($transactions) ? $transactions->total_fees : old('total_fees')" required />
                                    <x-input-error :messages="$errors->get('total_fees')" class="mt-2" />
                                </div>
                            </div>
                            <div>



                                <!-- debit -->
                                <div class="mt-5">
                                    <x-input-label for="debit" :value="__('Debit')" />
                                    <x-text-input id="debit" class="block mt-1 w-full" type="text" name="debit"
                                        :value="isset($transactions) ? $transactions->debit : old('debit')" required />
                                    <x-input-error :messages="$errors->get('debit')" class="mt-2" />
                                </div>


                                <!-- credit -->
                                <div class="mt-5">
                                    <x-input-label for="credit" :value="__('Credit')" />
                                    <x-text-input id="credit" class="block mt-1 w-full" type="text" name="credit"
                                        :value="isset($transactions) ? $transactions->credit : old('credit')" required />
                                    <x-input-error :messages="$errors->get('credit')" class="mt-2" />
                                </div>

                                <!-- transactions_date -->
                                <div class="mt-5">
                                    <x-input-label for="transactions_date" :value="__('Transactions Date')" />
                                    <x-text-input id="transactions_date" class="block mt-1 w-full" type="date"
                                        name="transactions_date" :value="isset($transactions)
                                            ? $transactions->transactions_date
                                            : old('transactions_date')" required />
                                    <x-input-error :messages="$errors->get('transactions_date')" class="mt-2" />
                                </div>


                                <!-- Acount Id -->
                                <div class="mt-5">
                                    <x-input-label for="account_id" :value="__('Acount Id')" />
                                    <select id="account_id" name="account_id" class="block mt-1 w-full">
                                        @foreach ($accounts as $account)
                                            <option value="{{ $account->id }}"
                                                {{ isset($transactions) && $account->account_id == $account->id ? 'selected' : '' }}>
                                                {{ $account->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('student_id')" class="mt-2" />
                                </div>


                                <!-- Class Id -->
                                <div class="mt-5">
                                    <x-input-label for="class_id" :value="__('Class Id')" />
                                    <select id="class_id" name="class_id" class="block mt-1 w-full">
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                {{ isset($transactions) && $class->class_id == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('class_id')" class="mt-2" />
                                </div>

                                <!-- Section Id -->
                                <div class="mt-5">
                                    <x-input-label for="section_id" :value="__('Section Id')" />
                                    <select id="section_id" name="section_id" class="block mt-1 w-full">
                                        @foreach ($Sections as $Section)
                                            <option value="{{ $Section->id }}"
                                                {{ isset($transactions) && $Section->section_id == $Section->id ? 'selected' : '' }}>
                                                {{ $Section->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('section_id')" class="mt-2" />
                                </div>

                                <!-- months_id -->
                                <div class="mt-5">
                                    <x-input-label for="months_id" :value="__('Months Id')" />
                                    <select id="months_id" name="months_id" class="block mt-1 w-full">
                                        @foreach ($classes as $class)
                                            <option value="{{ $class->id }}"
                                                {{ isset($transactions) && $transactions->months_id == $class->id ? 'selected' : '' }}>
                                                {{ $class->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('months_id')" class="mt-2" />
                                </div>

                                <!-- Academic Year Id' -->
                                <div class="mt-5">
                                    <x-input-label for="academic_year_id" :value="__('Academic Year Id')" />
                                    <select id="academic_year_id" name="academic_year_id" class="block mt-1 w-full">
                                        @foreach ($years as $year)
                                            <option value="{{ $year->id }}"
                                                {{ isset($transactions) && $year->academic_year_id == $year->id ? 'selected' : '' }}>
                                                {{ $year->year }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('academic_year_id')" class="mt-2" />
                                </div>

                                <!-- users id-->
                                <div class="mt-5">
                                    <x-input-label for="created_by_id" :value="__('Created By Id')" />
                                    <select id="created_by_id" name="created_by_id" class="block mt-1 w-full">
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}"
                                                {{ isset($transactions) && $transactions->created_by_id == $user->id ? 'selected' : '' }}>
                                                {{ $user->id }}

                                            </option>
                                        @endforeach
                                    </select>
                                    <x-input-error :messages="$errors->get('created_by_id')" class="mt-2" />
                                </div>
                                <!-- note -->
                                <div class="mt-5">
                                    <x-input-label for="note" :value="__('Note')" />
                                    <x-text-input id="note" class="block mt-1 w-full" type="text"
                                        name="note" :value="isset($transactions) ? $transactions->note : old('note')" required />
                                    <x-input-error :messages="$errors->get('note')" class="mt-2" />
                                </div>

                                <!-- Status -->
                                <div class="mt-5">
                                    <x-input-label for="isActived" :value="__('Active Status')" />
                                    <select id="isActived" name="isActived" class="block mt-1 w-full">
                                        <option value="1"
                                            {{ isset($transactions) && $transactions->isActived ? 'selected' : '' }}>
                                            Active
                                        </option>
                                        <option value="0"
                                            {{ isset($transactions) && !$transactions->isActived ? 'selected' : '' }}>
                                            Inactive
                                        </option>
                                    </select>
                                    <x-input-error :messages="$errors->get('isActived')" class="mt-2" />
                                </div>
                                <!-- Save/Update Button -->
                                <div class="flex items-center justify-end mt-4">
                                    <x-primary-button>
                                        @if (isset($transactions))
                                            {{ __('Update') }}
                                        @else
                                            {{ __('Save') }}
                                        @endif
                                    </x-primary-button>
                                </div>
                            </div>
                            <div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
