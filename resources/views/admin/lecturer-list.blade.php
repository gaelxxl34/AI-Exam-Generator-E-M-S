<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lecturer List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100">

    @include('partials.admin-navbar')

    <div class="p-4 sm:ml-64 mt-20">
        <h1 class="text-3xl font-bold text-gray-800 mb-4 text-center">Lecturer List</h1>

        <!-- 🔎 Search Bar -->
        <div class="mb-6 flex justify-center">
            <input type="text" id="searchInput" onkeyup="filterLecturers()" placeholder="Search by name or email..."
                class="w-full max-w-lg p-3 border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:outline-none">
        </div>

        @if (count($lecturersByFaculty) > 0)
            <div class="container mx-auto mt-3">
                <div class="table-responsive">
                    @foreach ($lecturersByFaculty as $faculty => $lecturers)
                        <div class="mb-6">

                            <div class="overflow-x-auto shadow-md rounded-lg">
                                <table class="min-w-full bg-white border border-gray-200 rounded-lg shadow-md lecturer-table">
                                    <thead class="bg-gray-800 text-white">
                                        <tr>
                                            <th class="py-3 px-4 text-left">First Name</th>
                                            <th class="py-3 px-4 text-left">Last Name</th>
                                            <th class="py-3 px-4 text-left">Email Address</th>
                                            <th class="py-3 px-4 text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse ($lecturers as $lecturer)
                                            <tr class="border-b hover:bg-gray-100 transition lecturer-row">
                                                <td class="py-3 px-4 lecturer-name">{{ $lecturer['firstName'] }}</td>
                                                <td class="py-3 px-4 lecturer-lastname">{{ $lecturer['lastName'] }}</td>
                                                <td class="py-3 px-4 lecturer-email">{{ $lecturer['email'] }}</td>
                                                <td class="py-3 px-4 text-center">
                                                    <a href="{{ route('editLecturer', ['id' => $lecturer['id']]) }}"
                                                        class="text-red-500 hover:text-red-700 font-semibold">
                                                        <i class="fas fa-pen"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="py-5 text-center border-b border-gray-200 bg-white text-sm">
                                                    <div class="flex flex-col items-center justify-center">
                                                        <img src="../assets/img/404.jpeg" alt="No Data Available"
                                                            class="w-1/2 max-w-md mx-auto">
                                                        <p class="mt-4 text-lg font-semibold text-gray-600">No lecturer data found.
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="mt-8 flex flex-col items-center justify-center">
                <img src="../assets/img/404.jpeg" alt="No Data Available" class="w-1/2 max-w-sm mx-auto">
                <p class="mt-4 text-lg font-semibold text-gray-600">No lecturer available.</p>
            </div>
        @endif
    </div>

    <!-- 🔎 JavaScript for Search Filtering -->
    <script>
        function filterLecturers() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll(".lecturer-row");

            rows.forEach(row => {
                let firstName = row.querySelector(".lecturer-name").textContent.toLowerCase();
                let lastName = row.querySelector(".lecturer-lastname").textContent.toLowerCase();
                let email = row.querySelector(".lecturer-email").textContent.toLowerCase();

                if (firstName.includes(input) || lastName.includes(input) || email.includes(input)) {
                    row.style.display = "";
                } else {
                    row.style.display = "none";
                }
            });
        }
    </script>

</body>

</html>