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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">
    <style>
        .lecturer-card {
            transition: all 0.3s ease;
        }

        .lecturer-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        .faculty-gradient {
            background: linear-gradient(135deg, #7a0000 0%, #9b1c1c 50%, #dc2626 100%);
        }

        .search-focus:focus {
            box-shadow: 0 0 0 3px rgba(122, 0, 0, 0.1);
            border-color: #7a0000;
        }

        .blue-accent {
            color: #3b82f6;
        }

        .green-accent {
            color: #10b981;
        }

        .purple-accent {
            color: #8b5cf6;
        }

        .orange-accent {
            color: #f97316;
        }

        .red-primary {
            background-color: #7a0000;
        }

        .red-text {
            color: #7a0000;
        }
    </style>
</head>

<body class="bg-gradient-to-br from-gray-50 to-gray-100 min-h-screen">

    @include('partials.admin-navbar')

    <div class="p-4 sm:ml-64 mt-20">
        <div class="max-w-7xl mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-8 animate__animated animate__fadeIn">
                <h1 class="text-4xl font-bold text-gray-800 mb-2">
                    <i class="fas fa-chalkboard-teacher blue-accent mr-3"></i>
                    Lecturer Management
                </h1>
                <p class="text-gray-600">Manage lecturer profiles and course assignments</p>
            </div>

            <!-- Search and Stats Section -->
            <div class="mb-8 animate__animated animate__fadeIn animate__delay-1s">
                <div class="bg-white rounded-xl shadow-lg p-6">
                    <div class="flex flex-col lg:flex-row justify-between items-center gap-4">
                        <!-- Search Bar -->
                        <div class="relative flex-1 max-w-md">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-search text-gray-400"></i>
                            </div>
                            <input type="text" id="searchInput" onkeyup="filterLecturers()"
                                placeholder="Search by name or email..."
                                class="search-focus block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none transition-all duration-200">
                        </div>

                        <!-- Stats -->
                        <div class="flex gap-4 text-sm">
                            <div class="bg-blue-50 px-4 py-2 rounded-lg">
                                <span class="blue-accent font-semibold" id="totalLecturers">0</span>
                                <span class="text-gray-600"> Total Lecturers</span>
                            </div>
                            <div class="bg-green-50 px-4 py-2 rounded-lg">
                                <span class="green-accent font-semibold" id="visibleLecturers">0</span>
                                <span class="text-gray-600"> Showing</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (count($lecturersByFaculty) > 0)
                <!-- Lecturers by Faculty -->
                <div class="space-y-8">
                    @php $colorIndex = 0; @endphp
                    @foreach ($lecturersByFaculty as $faculty => $lecturers)
                        <div class="animate__animated animate__fadeIn animate__delay-2s faculty-section">
                            <!-- Faculty Header -->
                            <div class="faculty-gradient rounded-t-xl p-6 text-white shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h2 class="text-2xl font-bold flex items-center">
                                            <i class="fas fa-university mr-3"></i>
                                            {{ $faculty }}
                                        </h2>
                                        <p class="text-gray-200 mt-1">Faculty Lecturers</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-3xl font-bold">{{ count($lecturers) }}</div>
                                        <div class="text-sm text-gray-200">Lecturers</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Lecturers Grid -->
                            <div class="bg-white rounded-b-xl shadow-lg p-6">
                                <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                                    @forelse ($lecturers as $lecturer)
                                        @php
                                            $colors = ['blue', 'green', 'purple', 'orange'];
                                            $currentColor = $colors[$colorIndex % 4];
                                            $colorIndex++;
                                        @endphp
                                        <div
                                            class="lecturer-card lecturer-row bg-white rounded-xl p-6 border border-gray-200 shadow-sm">
                                            <!-- Avatar -->
                                            <div class="flex justify-center mb-4">
                                                <div
                                                    class="w-16 h-16 bg-{{ $currentColor }}-100 rounded-full flex items-center justify-center">
                                                    <i class="fas fa-user text-2xl {{ $currentColor }}-accent"></i>
                                                </div>
                                            </div>

                                            <!-- Lecturer Info -->
                                            <div class="text-center mb-4">
                                                <h3 class="lecturer-name lecturer-lastname text-lg font-semibold text-gray-900">
                                                    {{ $lecturer['firstName'] }} {{ $lecturer['lastName'] }}
                                                </h3>
                                                <p class="lecturer-email text-sm text-gray-600 mt-1">{{ $lecturer['email'] }}</p>

                                                <!-- Course Count Badge -->
                                                <div class="mt-3">
                                                    <span
                                                        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-{{ $currentColor }}-100 {{ $currentColor }}-accent">
                                                        <i class="fas fa-book mr-1"></i>
                                                        {{ count($lecturer['courses']) }} Courses
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Action Buttons -->
                                            <div class="flex justify-center space-x-3">
                                                <a href="{{ route('editLecturer', ['id' => $lecturer['id']]) }}"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md red-text bg-red-50 hover:bg-red-600 hover:text-white transition-colors">
                                                    <i class="fas fa-edit mr-1"></i>
                                                    Edit
                                                </a>

                                                <button
                                                    onclick="showCourses('{{ $lecturer['id'] }}', {{ json_encode($lecturer['courses']) }})"
                                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md {{ $currentColor }}-accent bg-{{ $currentColor }}-50 hover:bg-{{ $currentColor }}-600 hover:text-white transition-colors">
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                    Courses
                                                </button>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="col-span-full text-center py-8">
                                            <div
                                                class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-user-slash text-3xl text-gray-400"></i>
                                            </div>
                                            <p class="text-lg font-semibold text-gray-600">No lecturer data found.</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-12 animate__animated animate__fadeIn">
                    <div class="max-w-md mx-auto">
                        <div class="bg-white rounded-xl shadow-lg p-8">
                            <div class="w-24 h-24 mx-auto mb-4 bg-gray-100 rounded-full flex items-center justify-center">
                                <i class="fas fa-chalkboard-teacher text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">No Lecturers Available</h3>
                            <p class="text-gray-600 mb-4">Start by adding some lecturers to get started.</p>
                            <button
                                class="red-primary text-white px-6 py-2 rounded-lg hover:bg-opacity-90 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Add Lecturer
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Enhanced Modal for Showing Lecturer Courses -->
    <div id="courseModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex justify-center items-center z-50">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg m-4 animate__animated animate__zoomIn">
            <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-6 rounded-t-xl">
                <h2 class="text-xl font-semibold flex items-center">
                    <i class="fas fa-book mr-2"></i>
                    Courses Taught
                </h2>
            </div>
            <div class="p-6">
                <div id="courseList" class="space-y-3 max-h-64 overflow-y-auto"></div>
                <div class="mt-6 flex justify-end">
                    <button onclick="closeModal()"
                        class="px-6 py-2 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors">
                        <i class="fas fa-times mr-2"></i>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Initialize page
        document.addEventListener('DOMContentLoaded', function () {
            updateStats();
        });

        function filterLecturers() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll(".lecturer-row");
            let visibleCount = 0;

            rows.forEach(row => {
                let firstName = row.querySelector(".lecturer-name").textContent.toLowerCase();
                let email = row.querySelector(".lecturer-email").textContent.toLowerCase();

                if (firstName.includes(input) || email.includes(input)) {
                    row.style.display = "";
                    visibleCount++;
                } else {
                    row.style.display = "none";
                }
            });

            // Update stats
            document.getElementById('visibleLecturers').textContent = visibleCount;

            // Hide/show faculty sections if no lecturers are visible
            let facultySections = document.querySelectorAll('.faculty-section');
            facultySections.forEach(section => {
                let visibleLecturersInSection = section.querySelectorAll('.lecturer-row:not([style*="display: none"])').length;
                section.style.display = visibleLecturersInSection > 0 ? '' : 'none';
            });
        }

        function updateStats() {
            let totalLecturers = document.querySelectorAll(".lecturer-row").length;
            document.getElementById('totalLecturers').textContent = totalLecturers;
            document.getElementById('visibleLecturers').textContent = totalLecturers;
        }

        // Show Courses Modal
        function showCourses(lecturerId, courses) {
            let modal = document.getElementById("courseModal");
            let courseList = document.getElementById("courseList");

            // Clear previous list
            courseList.innerHTML = "";

            if (courses.length > 0) {
                courses.forEach((course, index) => {
                    let courseCard = document.createElement("div");
                    courseCard.className = "bg-gray-50 border border-gray-200 rounded-lg p-3 flex items-center";
                    courseCard.innerHTML = `
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-book text-blue-600 text-sm"></i>
                        </div>
                        <span class="text-gray-800 font-medium">${course}</span>
                    `;
                    courseList.appendChild(courseCard);
                });
            } else {
                let noCourses = document.createElement("div");
                noCourses.className = "text-center py-8";
                noCourses.innerHTML = `
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-book-open text-2xl text-gray-400"></i>
                    </div>
                    <p class="text-gray-600 italic">No courses assigned.</p>
                `;
                courseList.appendChild(noCourses);
            }

            modal.classList.remove("hidden");
        }

        // Close Modal
        function closeModal() {
            document.getElementById("courseModal").classList.add("hidden");
        }

        // Close modal when clicking outside
        document.getElementById("courseModal").addEventListener('click', function (e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>

</body>

</html>