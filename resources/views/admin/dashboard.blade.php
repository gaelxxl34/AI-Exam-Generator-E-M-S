<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
    <link rel="stylesheet" href="{{ asset('assets/css/navbar.css') }}">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .stats-card {
            transition: all 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        }

        .gradient-red {
            background: linear-gradient(135deg, #7a0000 0%, #dc2626 100%);
        }

        .gradient-blue {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
        }

        .gradient-green {
            background: linear-gradient(135deg, #10b981 0%, #065f46 100%);
        }

        .gradient-purple {
            background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%);
        }

        .gradient-orange {
            background: linear-gradient(135deg, #f97316 0%, #ea580c 100%);
        }

        .quick-action-card {
            transition: all 0.3s ease;
        }

        .quick-action-card:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .chart-container {
            background: linear-gradient(145deg, #ffffff 0%, #f8fafc 100%);
        }
    </style>
</head>

<body class="bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-100 min-h-screen">

    @include('partials.admin-navbar')

    <div class="p-4 sm:ml-64 mt-20">
        <div class="max-w-7xl mx-auto">

            <!-- Welcome Header -->
            <div class="mb-8 animate__animated animate__fadeIn">
                <div class="bg-white rounded-2xl shadow-lg p-8 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-4xl font-bold text-gray-800 mb-2">
                                Welcome to Admin Dashboard
                            </h1>
                            <p class="text-gray-600 text-lg">
                                <i class="fas fa-calendar-alt mr-2"></i>
                                {{ date('l, F j, Y') }}
                            </p>
                            <p class="text-gray-500 mt-2">
                                <i class="fas fa-university mr-2"></i>
                                Managing: {{ $faculty }}
                            </p>
                        </div>
                        <div class="hidden md:block">
                            <div class="w-24 h-24 bg-gradient-to-br from-red-500 to-red-700 rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-tachometer-alt text-3xl text-white"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Loading State -->
            <div id="loadingState" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                @for($i = 0; $i < 4; $i++)
                    <div class="bg-white rounded-2xl p-6 shadow-lg animate-pulse">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <div class="h-4 bg-gray-300 rounded mb-2"></div>
                                <div class="h-8 bg-gray-300 rounded mb-2"></div>
                                <div class="h-3 bg-gray-300 rounded"></div>
                            </div>
                            <div class="w-12 h-12 bg-gray-300 rounded-full"></div>
                        </div>
                    </div>
                @endfor
            </div>

            <!-- Statistics Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Lecturers -->
                <div class="stats-card gradient-blue rounded-2xl p-6 text-white shadow-lg animate__animated animate__fadeInUp">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Total Lecturers</p>
                            <p class="text-3xl font-bold">{{ $lecturerCount ?? 0 }}</p>
                            <p class="text-blue-100 text-xs mt-1">
                                <i class="fas fa-arrow-up mr-1"></i>
                                Active users
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-chalkboard-teacher text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Courses -->
                <div class="stats-card gradient-green rounded-2xl p-6 text-white shadow-lg animate__animated animate__fadeInUp animate__delay-1s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Total Courses</p>
                            <p class="text-3xl font-bold">{{ $coursesCount ?? 0 }}</p>
                            <p class="text-green-100 text-xs mt-1">
                                <i class="fas fa-book mr-1"></i>
                                Available courses
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-graduation-cap text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Exams -->
                <div class="stats-card gradient-purple rounded-2xl p-6 text-white shadow-lg animate__animated animate__fadeInUp animate__delay-2s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Total Exams</p>
                            <p class="text-3xl font-bold">{{ $examsCount ?? 0 }}</p>
                            <p class="text-purple-100 text-xs mt-1">
                                <i class="fas fa-file-alt mr-1"></i>
                                Exam papers
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-file-text text-2xl"></i>
                        </div>
                    </div>
                </div>

                <!-- Total Past Papers -->
                <div class="stats-card gradient-orange rounded-2xl p-6 text-white shadow-lg animate__animated animate__fadeInUp animate__delay-3s">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-orange-100 text-sm font-medium">Past Papers</p>
                            <p class="text-3xl font-bold">{{ $pastExamsCount ?? 0 }}</p>
                            <p class="text-orange-100 text-xs mt-1">
                                <i class="fas fa-archive mr-1"></i>
                                Archived papers
                            </p>
                        </div>
                        <div class="bg-white bg-opacity-20 rounded-full p-3">
                            <i class="fas fa-folder-open text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="mb-8 animate__animated animate__fadeIn animate__delay-4s">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-rocket text-blue-600 mr-3"></i>
                    Quick Actions
                </h2>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <a href="/admin/add-lecturer"
                        class="quick-action-card bg-white rounded-xl p-6 shadow-lg border border-gray-100 text-center hover:border-blue-300 transition-all">
                        <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-user-plus text-blue-600 text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 text-sm">Add Lecturer</h3>
                    </a>

                    <a href="/admin/upload-course"
                        class="quick-action-card bg-white rounded-xl p-6 shadow-lg border border-gray-100 text-center hover:border-green-300 transition-all">
                        <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-plus-circle text-green-600 text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 text-sm">Add Course</h3>
                    </a>

                    <a href="/admin/add-past-exams"
                        class="quick-action-card bg-white rounded-xl p-6 shadow-lg border border-gray-100 text-center hover:border-orange-300 transition-all">
                        <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-upload text-orange-600 text-xl"></i>
                        </div>
                        <h3 class="font-semibold text-gray-800 text-sm">Upload Past Papers</h3>
                    </a>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                <!-- Faculty Distribution Chart -->
                <div
                    class="chart-container bg-white rounded-2xl shadow-lg p-6 border border-gray-100 animate__animated animate__fadeInLeft animate__delay-5s">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-chart-pie text-indigo-600 mr-3"></i>
                        Faculty Distribution
                    </h3>
                    <div class="relative h-64">
                        <canvas id="facultyChart"></canvas>
                    </div>
                </div>

                <!-- Recent Activities -->
                <div
                    class="bg-white rounded-2xl shadow-lg p-6 border border-gray-100 animate__animated animate__fadeInRight animate__delay-5s">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fas fa-clock text-red-600 mr-3"></i>
                        Recent Activities
                    </h3>
                    <div class="space-y-3 max-h-64 overflow-y-auto">
                        <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user-plus text-blue-600 text-sm"></i>
                            </div>
                            <div class="flex-1">
                                <p class="text-gray-800 font-medium text-sm">System Activity</p>
                                <p class="text-gray-600 text-xs">Dashboard loaded successfully</p>
                            </div>
                            <span class="text-gray-400 text-xs">now</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Load dashboard data asynchronously
        document.addEventListener('DOMContentLoaded', function () {
            loadDashboardData();
        });

        async function loadDashboardData() {
            try {
                // Show loading state
                document.getElementById('loadingState').classList.remove('hidden');
                document.getElementById('loadingState').classList.add('grid');

                // Simulate API call or use AJAX to get data
                const response = await fetch('/admin/dashboard-data', {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Content-Type': 'application/json'
                    }
                });

                if (response.ok) {
                    const data = await response.json();
                    updateDashboardStats(data);
                } else {
                    // Fallback to default values
                    updateDashboardStats({
                        totalLecturers: {{ $totalLecturers ?? 0 }},
                        totalCourses: {{ $totalCourses ?? 0 }},
                        totalExams: {{ $totalExams ?? 0 }},
                        totalPastPapers: {{ $totalPastPapers ?? 0 }},
                        facultyLabels: {!! json_encode($facultyLabels ?? ['FST', 'FOL', 'FOE']) !!},
                        facultyData: {!! json_encode($facultyData ?? [1, 1, 1]) !!}
                    });
                }
            } catch (error) {
                console.error('Error loading dashboard data:', error);
                // Use fallback data
                updateDashboardStats({
                    totalLecturers: {{ $totalLecturers ?? 0 }},
                    totalCourses: {{ $totalCourses ?? 0 }},
                    totalExams: {{ $totalExams ?? 0 }},
                    totalPastPapers: {{ $totalPastPapers ?? 0 }},
                    facultyLabels: {!! json_encode($facultyLabels ?? ['FST', 'FOL', 'FOE']) !!},
                    facultyData: {!! json_encode($facultyData ?? [1, 1, 1]) !!}
                });
            }
        }

        function updateDashboardStats(data) {
            // Hide loading state
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('loadingState').classList.remove('grid');

            // Show stats cards
            document.getElementById('statsCards').classList.remove('hidden');
            document.getElementById('statsCards').classList.add('grid');

            // Update counter values with animation
            animateValue('totalLecturersCount', 0, data.totalLecturers, 1000);
            animateValue('totalCoursesCount', 0, data.totalCourses, 1200);
            animateValue('totalExamsCount', 0, data.totalExams, 1400);
            animateValue('totalPastPapersCount', 0, data.totalPastPapers, 1600);

            // Update charts
            updateFacultyChart(data.facultyLabels, data.facultyData);
        }

        function animateValue(elementId, start, end, duration) {
            const element = document.getElementById(elementId);
            if (!element) return;

            let startTimestamp = null;
            const step = (timestamp) => {
                if (!startTimestamp) startTimestamp = timestamp;
                const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                element.innerHTML = Math.floor(progress * (end - start) + start);
                if (progress < 1) {
                    window.requestAnimationFrame(step);
                }
            };
            window.requestAnimationFrame(step);
        }

        function updateFacultyChart(labels, data) {
            const facultyCtx = document.getElementById('facultyChart').getContext('2d');
            new Chart(facultyCtx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        data: data,
                        backgroundColor: ['#7a0000', '#3b82f6', '#10b981', '#8b5cf6', '#f97316'],
                        borderWidth: 0,
                        hoverBorderWidth: 3,
                        hoverBorderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                                font: {
                                    size: 12
                                }
                            }
                        }
                    }
                }
            });
        }

        // Faculty Distribution Pie Chart
        const facultyCtx = document.getElementById('facultyChart')?.getContext('2d');
        if (facultyCtx) {
            new Chart(facultyCtx, {
                type: 'doughnut',
                data: {
                    labels: ["Lecturers", "Courses", "Past Papers"],
                    datasets: [{
                        data: [{{ $lecturerCount ?? 0 }}, {{ $coursesCount ?? 0 }}, {{ $pastExamsCount ?? 0 }}],
                        backgroundColor: ['#7a0000', '#3b82f6', '#10b981'],
                        borderWidth: 0,
                        hoverBorderWidth: 3,
                        hoverBorderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        }

        // Debug: Log the data being passed to the view
        console.log('Dashboard Data:', {
            totalLecturers: {{ $totalLecturers ?? 0 }},
            totalCourses: {{ $totalCourses ?? 0 }},
            totalExams: {{ $totalExams ?? 0 }},
            totalPastPapers: {{ $totalPastPapers ?? 0 }},
            facultyLabels: {!! json_encode($facultyLabels ?? []) !!},
            facultyData: {!! json_encode($facultyData ?? []) !!}
        });
    </script>

</body>

</html>