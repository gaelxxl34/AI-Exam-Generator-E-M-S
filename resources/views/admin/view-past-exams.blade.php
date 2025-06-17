<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Past Exams List</title>

    <!-- TailwindCSS & Flowbite -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

</head>

<body class="bg-gray-50">

    @include('partials.admin-navbar')

    <div class="p-4 sm:ml-64 mt-20">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="bg-white rounded-lg shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            <i class="fas fa-file-alt text-blue-600 mr-3"></i>Past Exams Library
                        </h1>
                        <p class="text-gray-600 mt-2">Browse and manage past examination papers</p>
                    </div>
                    <div class="flex items-center text-sm text-gray-500">
                        <i class="fas fa-database mr-2"></i>
                        <span>Total:
                            {{ array_sum(array_map(function ($periods) {
    return array_sum(array_map('count', $periods)); }, $examsData)) }}
                            exams</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="mb-6">
            <div class="bg-white rounded-lg shadow-sm p-4 border border-gray-200">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" id="searchInput" onkeyup="filterExams()"
                                placeholder="Search by Course Unit, Program, or Exam Period..."
                                class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none transition-all">
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <select id="yearFilter"
                            class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 focus:outline-none">
                            <option value="">All Years</option>
                            @foreach(array_keys($examsData) as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                        <button onclick="clearFilters()"
                            class="px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all">
                            <i class="fas fa-times mr-1"></i>Clear
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @if (count($examsData) > 0)
            <div class="space-y-8">
                @foreach ($examsData as $year => $examPeriods)
                    <div class="year-section" data-year="{{ $year }}">
                        <!-- Year Header -->
                        <div class="bg-gradient-to-r from-blue-600 to-blue-700 rounded-t-lg p-4 text-white">
                            <div class="flex items-center justify-between">
                                <h2 class="text-2xl font-bold flex items-center">
                                    <i class="fas fa-calendar-alt mr-3"></i>{{ $year }}
                                </h2>
                                <span class="bg-blue-500 px-3 py-1 rounded-full text-sm">
                                    {{ array_sum(array_map('count', $examPeriods)) }} exams
                                </span>
                            </div>
                        </div>

                        <!-- Exam Periods -->
                        <div class="bg-white rounded-b-lg shadow-sm border border-gray-200">
                            @foreach ($examPeriods as $examPeriod => $exams)
                                @if (count($exams) > 0)
                                    <div class="exam-period-section border-b border-gray-100 last:border-b-0"
                                        data-period="{{ $examPeriod }}">
                                        <!-- Period Header -->
                                        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                                            <div class="flex items-center justify-between">
                                                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                                    <i class="fas fa-clock mr-2 text-blue-500"></i>{{ $examPeriod }} Exams
                                                </h3>
                                                <span class="text-sm text-gray-600 bg-white px-2 py-1 rounded">
                                                    {{ count($exams) }} papers
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Exams Table -->
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full exam-table">
                                                <thead class="bg-gray-800 text-white">
                                                    <tr>
                                                        <th class="py-4 px-6 text-left font-semibold">
                                                            <i class="fas fa-graduation-cap mr-2"></i>Program
                                                        </th>
                                                        <th class="py-4 px-6 text-left font-semibold">
                                                            <i class="fas fa-book mr-2"></i>Course Unit
                                                        </th>
                                                        <th class="py-4 px-6 text-center font-semibold">
                                                            <i class="fas fa-download mr-2"></i>Download
                                                        </th>
                                                        <th class="py-4 px-6 text-center font-semibold">
                                                            <i class="fas fa-cog mr-2"></i>Actions
                                                        </th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach ($exams as $index => $data)
                                                        <tr
                                                            class="border-b border-gray-100 hover:bg-blue-50 transition-all duration-200 exam-row {{ $index % 2 == 0 ? 'bg-white' : 'bg-gray-50' }}">
                                                            <td class="py-4 px-6">
                                                                <div class="flex items-center">
                                                                    <div
                                                                        class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                                                        <i class="fas fa-university text-blue-600"></i>
                                                                    </div>
                                                                    <span
                                                                        class="font-semibold text-gray-800 program-name">{{ $data['program'] }}</span>
                                                                </div>
                                                            </td>
                                                            <td class="py-4 px-6">
                                                                <div class="course-name">
                                                                    <span class="font-medium text-gray-800">{{ $data['courseUnit'] }}</span>
                                                                    <div class="text-sm text-gray-500 mt-1">
                                                                        <i class="fas fa-tag mr-1"></i>{{ $examPeriod }} {{ $year }}
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td class="py-4 px-6 text-center">
                                                                <a href="data:application/pdf;base64,{{ $data['file'] }}"
                                                                    download="Exam_{{ $data['courseUnit'] }}_{{ $year }}_{{ $examPeriod }}.pdf"
                                                                    class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all duration-200 shadow-sm hover:shadow-md">
                                                                    <i class="fas fa-download mr-2"></i>Download PDF
                                                                </a>
                                                            </td>
                                                            <td class="py-4 px-6 text-center">
                                                                <div class="flex justify-center space-x-2">
                                                                    <button onclick="viewExam('{{ $data['file'] }}')"
                                                                        class="inline-flex items-center px-3 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-all duration-200">
                                                                        <i class="fas fa-eye"></i>
                                                                    </button>
                                                                    <a href="{{ route('delete-past-exam', ['id' => $data['id']]) }}"
                                                                        onclick="return confirm('Are you sure you want to delete this exam? This action cannot be undone.')"
                                                                        class="inline-flex items-center px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-all duration-200">
                                                                        <i class="fas fa-trash-alt"></i>
                                                                    </a>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- No Results Message -->
            <div id="noResultsMessage" class="hidden text-center py-12">
                <div class="bg-white rounded-lg shadow-sm p-8 border border-gray-200">
                    <i class="fas fa-search text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No Results Found</h3>
                    <p class="text-gray-500">Try adjusting your search criteria or filters</p>
                    <button onclick="clearFilters()"
                        class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all">
                        <i class="fas fa-times mr-2"></i>Clear Filters
                    </button>
                </div>
            </div>
        @else
            <div class="text-center py-12">
                <div class="bg-white rounded-lg shadow-sm p-8 border border-gray-200">
                    <i class="fas fa-file-alt text-gray-400 text-6xl mb-4"></i>
                    <h3 class="text-xl font-semibold text-gray-600 mb-2">No Past Exams Available</h3>
                    <p class="text-gray-500">There are currently no examination papers in the system</p>
                </div>
            </div>
        @endif
    </div>

    <!-- Enhanced Search Functionality -->
    <script>
        function filterExams() {
            let input = document.getElementById("searchInput").value.toUpperCase();
            let yearFilter = document.getElementById("yearFilter").value;
            let foundAny = false;

            // Get all year sections
            let yearSections = document.querySelectorAll(".year-section");

            yearSections.forEach(yearSection => {
                let year = yearSection.getAttribute("data-year");
                let yearMatch = !yearFilter || year === yearFilter;
                let foundInYear = false;

                if (yearMatch) {
                    let examRows = yearSection.querySelectorAll(".exam-row");
                    let periodSections = yearSection.querySelectorAll(".exam-period-section");

                    examRows.forEach(row => {
                        let columns = row.getElementsByTagName("td");
                        let matchFound = false;

                        for (let j = 0; j < columns.length - 1; j++) {
                            let txtValue = columns[j].textContent || columns[j].innerText;
                            if (txtValue.toUpperCase().indexOf(input) > -1) {
                                matchFound = true;
                                foundAny = true;
                                foundInYear = true;
                                break;
                            }
                        }

                        row.style.display = matchFound ? "" : "none";
                    });

                    // Hide/show period sections based on visible rows
                    periodSections.forEach(periodSection => {
                        let visibleRows = periodSection.querySelectorAll(".exam-row:not([style*='display: none'])");
                        periodSection.style.display = visibleRows.length > 0 ? "" : "none";
                    });
                }

                yearSection.style.display = (yearMatch && foundInYear) ? "" : "none";
            });

            // Show/hide no results message
            let noResultsMessage = document.getElementById("noResultsMessage");
            if (noResultsMessage) {
                noResultsMessage.style.display = foundAny ? "none" : "block";
            }
        }

        function clearFilters() {
            document.getElementById("searchInput").value = "";
            document.getElementById("yearFilter").value = "";
            filterExams();
        }

        function viewExam(fileData) {
            let newWindow = window.open();
            newWindow.document.write('<iframe src="data:application/pdf;base64,' + fileData + '" width="100%" height="100%"></iframe>');
        }

        // Add year filter functionality
        document.getElementById("yearFilter").addEventListener("change", filterExams);
    </script>

</body>

</html>