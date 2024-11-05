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

                        <div class=" overflow-auto mt-4 mb-4">
                            <div class="student-fee flex">
                                <input type="text" value="Name" disabled />
                                <input type="text" value="Student Book Number" disabled />
                                <input type="text" value="Receipt No" disabled />
                                <input type="text" value="Monthly Fee" disabled />
                                <input type="text" value="Boarding Fees" disabled />
                                <input type="text" value="Management Fees" disabled />
                                <input type="text" value="Exam Fees" disabled />
                                <input type="text" value="Others Fees" disabled />
                                <input type="text" value="Total Fees" disabled />
                                <input type="text" value="Note" disabled />
                            </div>
                            <div id="students_list">
                            </div>
                        </div>
                        <x-primary-button>
                            {{ __('Save All Fees') }}
                        </x-primary-button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script>
        function fetchStudents() {
            let academicYearId = document.getElementById('academic_year_id').value;
            let monthId = document.getElementById('months_id').value;
            let classId = document.getElementById('class_id').value;
            let sectionId = document.getElementById('section_id').value;

            axios.get('{{ route('get.students') }}', {
                    params: {
                        academic_year_id: academicYearId,
                        months_id: monthId,
                        class_id: classId,
                        section_id: sectionId
                    }
                })
                .then(function(response) {
                    document.getElementById('students_list').innerHTML = response.data;
                })
                .catch(function(error) {
                    console.log(error);
                });
        }
    </script>
</x-app-layout>