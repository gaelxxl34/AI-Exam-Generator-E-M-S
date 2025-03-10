<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Exam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>

    <style>
        .search-bar {
            width: 50%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            font-size: 16px;
            margin-bottom: 1rem;
        }

        .highlight {
            background-color: #f0f9ff !important;
        }
    </style>
</head>

<body class="bg-gray-100">

    @include('partials.gen-navbar')

    <div class="p-6 sm:ml-64 mt-20">
        <h1 class="text-3xl font-bold text-center mb-6 text-gray-800">📄 Generate Exam</h1>

        <!-- 🔍 Search Bar -->
        <div class="flex justify-center">
            <input type="text" id="searchInput" onkeyup="filterCourses()" placeholder="Search by Course Name or Code..."
                class="search-bar focus:ring-2 focus:ring-blue-400 focus:outline-none">
        </div>

        <div class="overflow-x-auto bg-white p-6 rounded-lg shadow-lg">
            @if(count($courses) > 0)
                <table class="min-w-full border border-gray-300 rounded-lg shadow-md" id="courseTable">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">Course Name</th>
                            <th class="py-3 px-4 text-left">Course Code</th>
                            <th class="py-3 px-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($courses as $course)
                            <tr class="border-b hover:bg-gray-100 transition course-row">
                                <td class="py-3 px-4 font-semibold text-gray-700 course-name">{{ $course['name'] }}</td>
                                <td class="py-3 px-4 font-semibold text-gray-700 course-code">{{ $course['code'] }}</td>
                                <td class="py-3 px-4 text-center">
                                    <form action="{{ route('genadmin.view-generated-exam') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="course" value="{{ $course['name'] }}">
                                        <button type="submit"
                                            class="bg-green-500 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm">
                                            <i class="fas fa-cogs"></i> Generate Exam
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-600 text-center mt-6">No courses available for exam generation.</p>
            @endif
        </div>
    </div>

    <!-- ✅ JavaScript for Search Functionality -->
    <script>
        function filterCourses() {
            let input = document.getElementById("searchInput").value.toUpperCase();
            let rows = document.querySelectorAll(".course-row");
            let foundAny = false;

            rows.forEach(row => {
                let courseName = row.querySelector(".course-name").textContent.toUpperCase();
                let courseCode = row.querySelector(".course-code").textContent.toUpperCase();

                if (courseName.includes(input) || courseCode.includes(input)) {
                    row.style.display = "";
                    row.classList.add("highlight");
                    foundAny = true;
                } else {
                    row.style.display = "none";
                    row.classList.remove("highlight");
                }
            });

            // Show or hide the "No results" message
            let noResultsMessage = document.getElementById("noResultsMessage");
            if (!foundAny) {
                noResultsMessage.style.display = "block";
            } else {
                noResultsMessage.style.display = "none";
            }
        }
    </script>

    <!-- No Results Message -->
    <div id="noResultsMessage" class="text-center text-gray-600 font-semibold mt-4 hidden">
        No courses found for the given search query.
    </div>

</body>

</html>