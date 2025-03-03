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
<body class="bg-gray-100">

    @include('partials.admin-navbar')

    <div class="p-4 sm:ml-64 mt-20">
        <h1 class="text-3xl font-bold text-center mt-6 mb-6 text-gray-800">Past Exams List</h1>

        <!-- 🔍 Search Bar -->
        <div class="mb-6 flex justify-center">
            <input type="text" id="searchInput" onkeyup="filterExams()"
                placeholder="Search by Course Unit, Program, or Exam Period..."
                class="w-full max-w-lg p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        @if (count($examsData) > 0)
            <div class="overflow-x-auto bg-white p-6 rounded-lg shadow-md">
                @foreach ($examsData as $year => $examPeriods)
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ $year }}</h2>

                        @foreach ($examPeriods as $examPeriod => $exams)
                            @if (count($exams) > 0)
                                <h3 class="text-lg font-semibold text-gray-700 mb-2">{{ $examPeriod }} Exams</h3>
                                <table class="min-w-full border border-gray-300 rounded-lg shadow-md mb-4" id="examTable">
                                    <thead class="bg-gray-800 text-white">
                                        <tr>
                                            <th class="py-3 px-4 text-left">Program</th>
                                            <th class="py-3 px-4 text-left">Course Unit</th>
                                            <th class="py-3 px-4 text-center">Download</th>
                                            <th class="py-3 px-4 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($exams as $data)
                                            <tr class="border-b hover:bg-gray-100 transition exam-row">
                                                <td class="py-3 px-4 font-semibold text-gray-700 program-name">{{ $data['program'] }}</td>
                                                <td class="py-3 px-4 text-gray-700 course-name">{{ $data['courseUnit'] }}</td>
                                                <td class="py-3 px-4 text-center">
                                                    <a href="data:application/pdf;base64,{{ $data['file'] }}"
                                                        download="Exam_{{ $data['courseUnit'] }}_{{ $year }}_{{ $examPeriod }}.pdf"
                                                        class="bg-blue-500 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition text-sm">
                                                        <i class="fa fa-download"></i> Download
                                                    </a>
                                                </td>
                                                <td class="py-3 px-4 text-center">
                                                    <a href="{{ route('delete-past-exam', ['id' => $data['id']]) }}"
                                                        onclick="return confirm('Are you sure you want to delete this exam?')"
                                                        class="text-red-600 hover:text-red-800 transition text-lg">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
        @else
            <div class="mt-8 flex flex-col items-center justify-center">
                <img src="../assets/img/404.jpeg" alt="No Data Available" class="w-1/2 max-w-sm mx-auto">
                <p class="mt-4 text-lg font-semibold text-gray-600">No past exams available.</p>
            </div>
        @endif
    </div>

    <!-- Search Functionality -->
<script>
    function filterExams() {
        let input = document.getElementById("searchInput").value.toUpperCase();
        let tables = document.querySelectorAll("#examTable"); // All tables (year groups)
        let foundAny = false; // Track if we found any matching row

        tables.forEach(table => {
            let rows = table.getElementsByTagName("tr");
            let yearHeader = table.previousElementSibling; // Get the year header
            let examPeriodHeader = null; // For exam periods like April, August, December

            let foundInTable = false; // Track matches in this table

            for (let i = 1; i < rows.length; i++) { // Skip the header row
                let columns = rows[i].getElementsByTagName("td");
                let matchFound = false;

                for (let j = 0; j < columns.length - 1; j++) { // Ignore last column (actions)
                    let txtValue = columns[j].textContent || columns[j].innerText;
                    if (txtValue.toUpperCase().indexOf(input) > -1) {
                        matchFound = true;
                        foundAny = true;
                        foundInTable = true;
                        break;
                    }
                }

                // Show or hide row based on match
                rows[i].style.display = matchFound ? "" : "none";

                // Detect if this row belongs to a new exam period (April, August, December)
                if (rows[i].classList.contains("exam-period-header")) {
                    examPeriodHeader = rows[i];
                }
            }

            // Show or hide the exam period header
            if (examPeriodHeader) {
                let nextRow = examPeriodHeader.nextElementSibling;
                let hasVisibleRow = false;

                while (nextRow && !nextRow.classList.contains("exam-period-header")) {
                    if (nextRow.style.display !== "none") {
                        hasVisibleRow = true;
                        break;
                    }
                    nextRow = nextRow.nextElementSibling;
                }

                examPeriodHeader.style.display = hasVisibleRow ? "" : "none";
            }

            // Show or hide the entire year section based on matches
            if (yearHeader && yearHeader.classList.contains("year-header")) {
                yearHeader.style.display = foundInTable ? "" : "none";
            }
        });

        // If no matches were found, show a message
        let noResultsMessage = document.getElementById("noResultsMessage");
        if (!foundAny) {
            noResultsMessage.style.display = "block";
        } else {
            noResultsMessage.style.display = "none";
        }
    }
</script>

</body>
</html>
