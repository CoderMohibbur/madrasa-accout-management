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

                    <form id="fees_form" method="POST" action="{{ route('fees.bulk_store') }}">
                        @csrf

                        <!-- Academic Year -->
                        <select id="academic_year_id" name="academic_year_id">
                            @foreach ($years as $year)
                                <option value="{{ $year->id }}">{{ $year->year }}</option>
                            @endforeach
                        </select>

                        <!-- Month -->
                        <select id="months_id" name="months_id">
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
                        <x-primary-button-button onclick="fetchStudents()">
                            {{ __('Search') }}
                        </x-primary-button-button>


                    </form>
                    <br><br>

                    <div class="col-span-3" id="myTable">
                        <div class="text-center bg-gray-100 p-8 rounded-lg shadow-lg">
                            <p class="font-solaiman text-3xl text-blue-800 font-bold mb-2">
                                আত-তাওহীদ ইসলামী কমপ্লেক্স
                            </p>
                            <p class="font-nikosh text-2xl text-gray-700 mb-2">
                                পুলেরহাট, সদর, যশোর
                            </p>
                            <p class="font-kalpurush text-2xl text-gray-600 italic">
                                ছাত্রছাত্রীদের বেতনের হিসাব
                            </p>
                        </div>

                        <br><br>

                        <table class="border-collapse table-auto w-full text-sm">
                            <thead>
                                <tr>
                                    <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">ক্রমিক নং</th>
                                    <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">নাম</th>
                                    <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">বই নং</th>
                                    <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">বেতন</th>
                                    <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">বোডিং ফী</th>
                                    <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">ব্যবস্থাপনা ফী</th>
                                    <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">অন্যান্য ফী</th>
                                    <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 dark:text-slate-200 text-left">সর্বমোট ফী</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-slate-800">
                                @foreach ($transactionss as $transaction)
                                    <tr>
                                        <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">{{ $transaction->id }}</td>
                                        <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">{{ $transaction->student_name }}</td>
                                        <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">{{ $transaction->student_book_number }}</td>
                                        <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">{{ $transaction->monthly_fees }}</td>
                                        <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">{{ $transaction->boarding_fees }}</td>
                                        <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">{{ $transaction->management_fees }}</td>
                                        <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">{{ $transaction->other_fees }}</td>
                                        <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 dark:text-slate-400">{{ $transaction->total_fees }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function printTable() {
            var printContents = document.getElementById('myTable').outerHTML;
            var styles = `
                <style>
                    body { font-family: sans-serif; padding: 20px; }
                    .text-center { text-align: center; }
                    .bg-gray-100 { background-color: #f7fafc; }
                    table { width: 100%; border-collapse: collapse; }
                    th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                    th { background-color: #f4f4f4; }
                    @media print {
                        body { -webkit-print-color-adjust: exact; }
                        th { color: #fff; background-color: #4a5568 !important; }
                        .text-center { text-align: center !important; }
                        .bg-gray-100 { background-color: #f7fafc !important; }
                    }
                </style>
            `;
            var win = window.open('', '', 'height=500,width=800');
            win.document.write('<html><head><title>Print Table</title>');
            win.document.write(styles);
            win.document.write('</head><body>');
            win.document.write(printContents);
            win.document.write('</body></html>');
            win.document.close();
            win.print();
        }
    </script>
</x-app-layout>
