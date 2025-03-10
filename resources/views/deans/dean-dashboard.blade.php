<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dean Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>

    <style>
        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .search-bar {
            width: 50%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            font-size: 16px;
        }

        .highlight {
            background-color: #f0f9ff !important;
        }
    </style>
</head>

<body>

    @include('partials.dean-navbar')

    <div class="p-6 sm:ml-64 mt-20">
        <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">📘 Dean Dashboard - Exam Moderation</h1>

        <!-- 🔍 Search Bar -->
        <div class="search-container">
            <input type="text" id="searchInput" onkeyup="filterCourses()" placeholder="Search course name..."
                class="search-bar focus:ring-2 focus:ring-blue-400 focus:outline-none">
        </div>

        @if (session('error'))
            <div class="bg-red-500 text-white p-3 rounded mb-4 text-center">
                {{ session('error') }}
            </div>
        @endif

        @if (count($courses) > 0)
            <div class="overflow-x-auto bg-white p-6 rounded-lg shadow-lg">
                <table class="min-w-full border border-gray-300 rounded-lg shadow-md" id="courseTable">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">Course Name</th>
                            <th class="py-3 px-4 text-center">Created At</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($courses as $course)
                            <tr class="border-b hover:bg-gray-100 transition course-row">
                                <td class="py-3 px-4 font-semibold text-gray-700 course-name">{{ $course['courseUnit'] }}</td>
                                <td class="py-3 px-4 text-center text-gray-600">
                                    {{ \Carbon\Carbon::parse($course['created_at'])->format('d M Y, h:i A') }}
                                </td>
                                <td class="py-3 px-4 text-center font-semibold">
                                    @if($course['status'] == 'Approved')
                                        <span class="text-green-600">✅ Approved</span>
                                    @elseif($course['status'] == 'Declined')
                                        <span class="text-red-600">❌ Declined</span>
                                    @else
                                        <span class="text-gray-600">📌 Pending Review</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center flex justify-center space-x-2">
                                    <a href="{{ route('preview.pdf', ['courseUnit' => $course['courseUnit']]) }}"
                                        class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm">
                                        <i class="fas fa-eye"></i> Preview
                                    </a>
                                    <form method="POST" action="{{ route('course.approve', ['id' => $course['id']]) }}">
                                        @csrf
                                        <button type="submit"
                                            class="bg-green-500 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                            <i class="fas fa-check"></i> Approve
                                        </button>
                                    </form>
                                    <!-- Updated Decline Button -->
                                    <button onclick="openDeclineModal('{{ $course['id'] }}')"
                                        class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                                        <i class="fas fa-times"></i> Decline
                                    </button>
                                </td>

                                <!-- Decline Modal -->
                                <div id="declineModal"
                                    class="hidden fixed inset-0 flex items-center justify-center bg-gray-800 bg-opacity-50">
                                    <div class="bg-white p-6 rounded-lg shadow-lg w-96">
                                        <h2 class="text-xl font-bold mb-4">Decline Exam</h2>
                                        <form id="declineForm" method="POST">
                                            @csrf
                                            <input type="hidden" id="declineExamId" name="id">
                                            <textarea name="comment" id="declineComment" rows="3"
                                                class="w-full border p-2 rounded-md"
                                                placeholder="Enter a reason for declining..." required></textarea>
                                            <div class="flex justify-end mt-4 space-x-2">
                                                <button type="button" onclick="closeDeclineModal()"
                                                    class="bg-gray-500 text-white px-4 py-2 rounded">
                                                    Cancel
                                                </button>
                                                <button type="submit" class="bg-red-500 text-white px-4 py-2 rounded">
                                                    Decline
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-600 text-center mt-6">No courses available for your faculty.</p>
        @endif
    </div>


    <script>
        function openDeclineModal(examId) {
            document.getElementById('declineModal').classList.remove('hidden');
            document.getElementById('declineExamId').value = examId;
            document.getElementById('declineForm').action = `/deans/course/${examId}/decline`;
        }

        function closeDeclineModal() {
            document.getElementById('declineModal').classList.add('hidden');
        }
    </script>

    <!-- ✅ JavaScript for Search Functionality -->
    <script>
        function filterCourses() {
            let input = document.getElementById("searchInput").value.toUpperCase();
            let rows = document.querySelectorAll(".course-row");
            let foundAny = false;

            rows.forEach(row => {
                let courseName = row.querySelector(".course-name").textContent.toUpperCase();
                if (courseName.includes(input)) {
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