<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Courses List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</head>
<body class="bg-gray-100">
    
    @include('partials.admin-navbar')

    <div class="p-4 sm:ml-64 mt-20 flex justify-center">
        <div class="container max-w-6xl">
            <h1 class="text-3xl font-bold text-gray-800 mb-4 text-center">Courses List</h1>

            <!-- 🔎 Search Bar -->
            <div class="mb-6 flex justify-center">
                <input type="text" id="searchInput" onkeyup="filterCourses()" placeholder="Search by course name or code..."
                    class="w-full max-w-lg p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
            </div>

            @if (!empty($courses))
                @foreach ($courses as $facultyName => $programs)
                    <div class="mb-6">
                        <div class="bg-blue-600 text-white p-3 rounded-lg shadow-md text-lg font-semibold">{{ $facultyName }}</div>
                        
                        @foreach ($programs as $programName => $programCourses)
                            <div class="mt-4">
                                <h2 class="text-gray-700 text-lg font-semibold mb-2">{{ $programName }}</h2>

                                <div class="overflow-x-auto shadow-md rounded-lg">
                                    <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md course-table">
                                        <thead class="bg-gray-300 text-gray-700">
                                            <tr>
                                                <th class="py-3 px-4 text-left">Course Name</th>
                                                <th class="py-3 px-4 text-left">Course Code</th>
                                                <th class="py-3 px-4 text-left">Year/Semester</th>
                                                <th class="py-3 px-4 text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($programCourses as $course)
                                                <tr class="border-b hover:bg-gray-100 transition course-row">
                                                    <td class="py-3 px-4 course-name">{{ $course['name'] }}</td>
                                                    <td class="py-3 px-4 course-code">{{ $course['code'] }}</td>
                                                    <td class="py-3 px-4">{{ $course['year_sem'] }}</td>
                                                    <td class="py-3 px-4 text-center">
                                                        <a href="{{ route('edit.course', ['id' => $course['id']]) }}" class="text-blue-500 hover:text-blue-700 font-semibold">
                                                            Edit
                                                        </a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            @else
                <div class="mt-8 flex flex-col items-center justify-center">
                    <img src="../assets/img/404.jpeg" alt="No Data Available" class="w-1/2 max-w-sm mx-auto">
                    <p class="mt-4 text-lg font-semibold text-gray-600">No course details available.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- 🔎 JavaScript for Search Filtering -->
    <script>
        function filterCourses() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll(".course-row");

            rows.forEach(row => {
                let courseName = row.querySelector(".course-name").textContent.toLowerCase();
                let courseCode = row.querySelector(".course-code").textContent.toLowerCase();

                if (courseName.includes(input) || courseCode.includes(input)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }
    </script>

</body>
</html>
