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

                    <form method="POST" action="{{ route('fees.bulk_store') }}">
                        @csrf

                        <!-- Academic Year -->
                        <select id="academic_year_id" name="academic_year_id">
                            @foreach ($years as $year)
                                <option value="{{ $year->id }}">{{ $year->year }}</option>
                            @endforeach
                        </select>

                        <!-- Month -->
                        <select id="month_id" name="month_id">
                            @foreach ($months as $month)
                                <option value="{{ $month->id }}">{{ $month->name }}</option>
                            @endforeach
                        </select>

                        <!-- Class -->
                        <select id="class_id" name="class_id">
                            @foreach ($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>

                        <!-- Section -->
                        <select id="section_id" name="section_id">
                            @foreach ($sections as $section)
                                <option value="{{ $section->id }}">{{ $section->name }}</option>
                            @endforeach
                        </select>

                        <!-- Button to fetch students -->
                        {{-- <button type="button" onclick="fetchStudents()">Fetch Students</button> --}}
                        <x-primary-button-button onclick="fetchStudents()">
                            {{ __('Search') }}
                        </x-primary-button-button>
                        </br><br><br>



                        <div class="text-center" >
                            <p class="font-solaiman text-3xl">
                                আত-তাওহীদ ইসলামী কমপ্লেক্স
                              </p>
                              <p class="font-nikosh text-2xl">
                                পুলেরহাট, সদর, যশোর
                              </p>
                              <p class="font-kalpurush text-2xl">
                                ছাত্রছাত্রীদের বেতনের হিসাব
                              </p>



                            <br><br>



                        </div>



                        <br><br><br>

                        <div class="col-span-3">
                            <table class="border-collapse table-auto w-full text-sm">
                                <thead>
                                    <tr>
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            ক্রমিক নং</th>
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            নাম </th>
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            বই নং </th>
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            বেতন </th>
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            বোডিং ফী </th>
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            ব্যবস্থাপনা ফী </th>
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            অন্যান্য ফী </th>
                                        <th
                                            class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">
                                            সর্বমোট ফী </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-slate-800">
                                    @foreach ($transactionss as $transactionss)
                                        <tr>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $class->id }}</td>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $class->student_id }}</td>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $class->student_book_number }}</td>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $class->monthly_fees }}</td>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $class->boarding_fees }}</td>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $class->others_fees }}</td>
                                            <td
                                                class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">
                                                {{ $class->total_fees }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                     </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
