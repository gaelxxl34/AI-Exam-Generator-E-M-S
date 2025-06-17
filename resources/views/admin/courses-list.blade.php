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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">
    <style>
        .red-primary { background-color: #7a0000; }
        .red-text { color: #7a0000; }
        .red-bg-light { background-color: rgba(122, 0, 0, 0.05); }
        .blue-accent { color: #3b82f6; }
        .blue-bg-light { background-color: rgba(59, 130, 246, 0.1); }
        .green-accent { color: #10b981; }
        .green-bg-light { background-color: rgba(16, 185, 129, 0.1); }
        .purple-accent { color: #8b5cf6; }
        .purple-bg-light { background-color: rgba(139, 92, 246, 0.1); }
        .orange-accent { color: #f97316; }
        .orange-bg-light { background-color: rgba(249, 115, 22, 0.1); }
        
        .faculty-gradient {
            background: linear-gradient(135deg, #7a0000 0%, #9b1c1c 50%, #dc2626 100%);
        }
        
        .card-hover {
            transition: all 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }
        
        .search-focus:focus {
            box-shadow: 0 0 0 3px rgba(122, 0, 0, 0.1);
            border-color: #7a0000;
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
                    <i class="fas fa-graduation-cap blue-accent mr-3"></i>
                    Courses Management
                </h1>
                <p class="text-gray-600">Manage and organize all academic courses across faculties</p>
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
                            <input type="text" id="searchInput" onkeyup="filterCourses()" 
                                placeholder="Search courses by name or code..."
                                class="search-focus block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:outline-none transition-all duration-200">
                        </div>
                        
                        <!-- Stats -->
                        <div class="flex gap-4 text-sm">
                            <div class="blue-bg-light px-4 py-2 rounded-lg">
                                <span class="blue-accent font-semibold" id="totalCourses">0</span>
                                <span class="text-gray-600"> Total Courses</span>
                            </div>
                            <div class="green-bg-light px-4 py-2 rounded-lg">
                                <span class="green-accent font-semibold" id="visibleCourses">0</span>
                                <span class="text-gray-600"> Showing</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            @if (!empty($courses))
                <!-- Courses Grid -->
                <div class="space-y-8">
                    @php $colorIndex = 0; @endphp
                    @foreach ($courses as $facultyName => $programs)
                        <div class="animate__animated animate__fadeIn animate__delay-2s">
                            <!-- Faculty Header -->
                            <div class="faculty-gradient rounded-t-xl p-6 text-white shadow-lg">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h2 class="text-2xl font-bold flex items-center">
                                            <i class="fas fa-university mr-3"></i>
                                            {{ $facultyName }}
                                        </h2>
                                        <p class="text-gray-200 mt-1">Faculty Programs and Courses</p>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-3xl font-bold">{{ count($programs) }}</div>
                                        <div class="text-sm text-gray-200">Programs</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Programs Grid -->
                            <div class="bg-white rounded-b-xl shadow-lg p-6">
                                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                                    @foreach ($programs as $programName => $programCourses)
                                        @php 
                                            $colors = ['blue', 'green', 'purple', 'orange'];
                                            $currentColor = $colors[$colorIndex % 4];
                                            $colorIndex++;
                                        @endphp
                                        <div class="card-hover bg-gray-50 rounded-lg p-5 border border-gray-200">
                                            <div class="flex items-center justify-between mb-4">
                                                <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                                    <i class="fas fa-folder {{ $currentColor }}-accent mr-2"></i>
                                                    {{ $programName }}
                                                </h3>
                                                <span class="{{ $currentColor }}-bg-light {{ $currentColor }}-accent text-xs px-2 py-1 rounded-full font-medium">
                                                    {{ count($programCourses) }} courses
                                                </span>
                                            </div>
                                            
                                            <!-- Courses List -->
                                            <div class="space-y-3 max-h-64 overflow-y-auto">
                                                @foreach ($programCourses as $course)
                                                    <div class="course-row bg-white rounded-lg p-3 border border-gray-100 hover:border-{{ $currentColor }}-200 transition-colors">
                                                        <div class="flex items-center justify-between">
                                                            <div class="flex-1 min-w-0">
                                                                <h4 class="course-name text-sm font-medium text-gray-900 truncate">
                                                                    {{ $course['name'] }}
                                                                </h4>
                                                                <div class="flex items-center gap-2 mt-1">
                                                                    <span class="course-code inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-black">
                                                                        <i class="fas fa-code mr-1"></i>
                                                                        {{ $course['code'] }}
                                                                    </span>
                                                                    <span class="text-xs text-gray-500">
                                                                        <i class="fas fa-calendar mr-1"></i>
                                                                        {{ $course['year_sem'] }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                            <div class="ml-3">
                                                                <a href="{{ route('edit.course', ['id' => $course['id']]) }}" 
                                                                   class="inline-flex items-center px-3 py-1 border border-transparent text-xs font-medium rounded-md red-text red-bg-light hover:bg-red-600 hover:text-white transition-colors">
                                                                    <i class="fas fa-edit mr-1"></i>
                                                                    Edit
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
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
                                <i class="fas fa-graduation-cap text-3xl text-gray-400"></i>
                            </div>
                            <h3 class="text-xl font-semibold text-gray-800 mb-2">No Courses Available</h3>
                            <p class="text-gray-600 mb-4">Start by adding some courses to get started.</p>
                            <button class="red-primary text-white px-6 py-2 rounded-lg hover:bg-opacity-90 transition-colors">
                                <i class="fas fa-plus mr-2"></i>
                                Add Course
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Enhanced JavaScript -->
    <script>
        // Initialize page
        document.addEventListener('DOMContentLoaded', function() {
            updateStats();
        });

        function filterCourses() {
            let input = document.getElementById("searchInput").value.toLowerCase();
            let rows = document.querySelectorAll(".course-row");
            let visibleCount = 0;

            rows.forEach(row => {
                let courseName = row.querySelector(".course-name").textContent.toLowerCase();
                let courseCode = row.querySelector(".course-code").textContent.toLowerCase();

                if (courseName.includes(input) || courseCode.includes(input)) {
                    row.style.display = "";
                    visibleCount++;
                } else {
                    row.style.display = "none";
                }
            });

            // Update stats
            document.getElementById('visibleCourses').textContent = visibleCount;
            
            // Hide/show program containers if no courses are visible
            let programContainers = document.querySelectorAll('.card-hover');
            programContainers.forEach(container => {
                let visibleCoursesInContainer = container.querySelectorAll('.course-row:not([style*="display: none"])').length;
                container.style.display = visibleCoursesInContainer > 0 ? '' : 'none';
            });
        }

        function updateStats() {
            let totalCourses = document.querySelectorAll(".course-row").length;
            document.getElementById('totalCourses').textContent = totalCourses;
            document.getElementById('visibleCourses').textContent = totalCourses;
        }

        // Add smooth scrolling for better UX
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>

</body>
</html>
