<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>My Dashboard - Lecturer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>


</head>

<body>
    @include('partials.lecturer-navbar')
    <div class="p-4 sm:ml-64 mt-20">
        <!-- Page Header -->
        <div class="mb-8">
            <div
                class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg shadow-lg p-6 text-white flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-clipboard-list text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">My Dashboard</h1>
                        <p class="text-blue-100 mt-1">Overview of your courses and exam submissions</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
            <!-- Total Courses -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Courses</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['totalCourses'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-blue-600"></i>
                    </div>
                </div>
            </div>

            <!-- Total Exams -->
            <div class="bg-white rounded-xl shadow-md border border-gray-100 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Total Exams</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $statistics['totalExams'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-indigo-600"></i>
                    </div>
                </div>
            </div>

            <!-- Pending Review -->
            <div class="bg-white rounded-xl shadow-md border border-yellow-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-yellow-600 font-medium">Pending Review</p>
                        <p class="text-2xl font-bold text-yellow-700">{{ $statistics['pendingReview'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-clock text-yellow-600"></i>
                    </div>
                </div>
            </div>

            <!-- Approved -->
            <div class="bg-white rounded-xl shadow-md border border-green-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-green-600 font-medium">Approved</p>
                        <p class="text-2xl font-bold text-green-700">{{ $statistics['approved'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-check-circle text-green-600"></i>
                    </div>
                </div>
            </div>

            <!-- Declined -->
            <div class="bg-white rounded-xl shadow-md border border-red-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-red-600 font-medium">Declined</p>
                        <p class="text-2xl font-bold text-red-700">{{ $statistics['declined'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600"></i>
                    </div>
                </div>
            </div>

            <!-- Draft -->
            <div class="bg-white rounded-xl shadow-md border border-gray-200 p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-500 font-medium">Drafts</p>
                        <p class="text-2xl font-bold text-gray-700">{{ $statistics['draft'] ?? 0 }}</p>
                    </div>
                    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-edit text-gray-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="flex flex-wrap gap-3 mb-6">
            <a href="{{ route('lecturer.list') }}"
                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg shadow hover:from-blue-700 hover:to-indigo-700 font-medium transition-all">
                <i class="fas fa-plus mr-2"></i> Upload New Exam
            </a>
            <button onclick="filterCourses('all')"
                class="filter-btn inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition-all"
                data-filter="all">
                <i class="fas fa-th-large mr-2"></i> All
            </button>
            <button onclick="filterCourses('pending')"
                class="filter-btn inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition-all"
                data-filter="pending">
                <i class="fas fa-clock mr-2"></i> Pending
            </button>
            <button onclick="filterCourses('approved')"
                class="filter-btn inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition-all"
                data-filter="approved">
                <i class="fas fa-check mr-2"></i> Approved
            </button>
            <button onclick="filterCourses('declined')"
                class="filter-btn inline-flex items-center px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 font-medium transition-all"
                data-filter="declined">
                <i class="fas fa-times mr-2"></i> Declined
            </button>
        </div>

        <!-- Section Title -->
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-semibold text-gray-800">
                <i class="fas fa-folder-open text-blue-500 mr-2"></i>My Courses
            </h2>
            <span class="text-sm text-gray-500" id="courseCount">{{ count($courses) }} course(s)</span>
        </div>

        <!-- Courses Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6" id="coursesGrid">
            @forelse ($courses as $courseUnit => $courseData)
                @php
                    $statusColor = $courseData['statusColor'] ?? 'gray';
                    $statusLabel = $courseData['statusLabel'] ?? 'No Status';
                    $examCount = count($courseData['exams'] ?? []);
                    $status = $courseData['status'] ?? 'draft';

                    $badgeColors = [
                        'green' => 'bg-green-100 text-green-800 border-green-200',
                        'red' => 'bg-red-100 text-red-800 border-red-200',
                        'yellow' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                        'blue' => 'bg-blue-100 text-blue-800 border-blue-200',
                        'gray' => 'bg-gray-100 text-gray-800 border-gray-200',
                    ];
                    $badgeClass = $badgeColors[$statusColor] ?? $badgeColors['gray'];

                    $borderColors = [
                        'green' => 'border-green-200 hover:border-green-300',
                        'red' => 'border-red-200 hover:border-red-300',
                        'yellow' => 'border-yellow-200 hover:border-yellow-300',
                        'blue' => 'border-blue-200 hover:border-blue-300',
                        'gray' => 'border-gray-200 hover:border-gray-300',
                    ];
                    $borderClass = $borderColors[$statusColor] ?? $borderColors['gray'];
                @endphp
                <div class="course-card bg-white rounded-xl shadow-md hover:shadow-xl border-2 {{ $borderClass }} transition-all flex flex-col justify-between h-full"
                    data-status="{{ $status }}">
                    <div class="p-6 flex-1 flex flex-col">
                        <!-- Status Badge -->
                        <div class="flex justify-between items-start mb-3">
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $badgeClass }}">
                                @if($statusColor === 'green')
                                    <i class="fas fa-check-circle mr-1"></i>
                                @elseif($statusColor === 'red')
                                    <i class="fas fa-times-circle mr-1"></i>
                                @elseif($statusColor === 'yellow')
                                    <i class="fas fa-clock mr-1"></i>
                                @elseif($statusColor === 'blue')
                                    <i class="fas fa-paper-plane mr-1"></i>
                                @else
                                    <i class="fas fa-edit mr-1"></i>
                                @endif
                                {{ $statusLabel }}
                            </span>
                            <span class="text-xs text-gray-400">{{ $examCount }} exam(s)</span>
                        </div>

                        <!-- Course Name -->
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3">
                                <i class="fas fa-book text-blue-500"></i>
                            </div>
                            <span class="text-lg font-semibold text-gray-900 line-clamp-2">{{ $courseUnit }}</span>
                        </div>

                        <!-- Action Button -->
                        <div class="mt-auto pt-4">
                            <a href="{{ route('lecturer.l-course-exams', ['courseUnit' => $courseUnit]) }}"
                                class="w-full inline-flex items-center justify-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg shadow hover:from-blue-700 hover:to-indigo-700 font-medium transition-all">
                                <span>View & Edit</span>
                                <i class="fas fa-arrow-right ml-2"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-12" id="emptyState">
                    <!-- No exams uploaded yet -->
                    <div class="text-center max-w-md">
                        <div class="mb-6">
                            <i class="fas fa-file-upload text-gray-300 text-8xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-700 mb-3">No Exams Uploaded Yet</h3>
                        <p class="text-gray-600 mb-6">
                            You haven't uploaded any exam questions yet. Get started by uploading your first exam template
                            to begin managing your course assessments.
                        </p>
                        <a href="{{ route('lecturer.list') }}"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg shadow-lg hover:from-blue-700 hover:to-indigo-700 font-medium transition-all transform hover:scale-105">
                            <i class="fas fa-plus mr-2"></i> Upload Your First Exam
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- No Results Message (hidden by default) -->
        <div class="hidden col-span-full flex flex-col items-center justify-center py-12" id="noResultsState">
            <div class="text-center max-w-md">
                <div class="mb-6">
                    <i class="fas fa-search text-gray-300 text-6xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-700 mb-3">No Matching Courses</h3>
                <p class="text-gray-600 mb-4">
                    No courses match the selected filter.
                </p>
                <button onclick="filterCourses('all')" class="text-blue-600 hover:text-blue-700 font-medium">
                    <i class="fas fa-undo mr-1"></i> Show All Courses
                </button>
            </div>
        </div>
    </div>

    <script>
        function filterCourses(status) {
            const cards = document.querySelectorAll('.course-card');
            const emptyState = document.getElementById('emptyState');
            const noResultsState = document.getElementById('noResultsState');
            const courseCount = document.getElementById('courseCount');
            let visibleCount = 0;

            // Update active filter button
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('bg-blue-600', 'text-white');
                btn.classList.add('bg-gray-100', 'text-gray-700');
                if (btn.dataset.filter === status) {
                    btn.classList.remove('bg-gray-100', 'text-gray-700');
                    btn.classList.add('bg-blue-600', 'text-white');
                }
            });

            cards.forEach(card => {
                const cardStatus = card.dataset.status;
                let show = false;

                if (status === 'all') {
                    show = true;
                } else if (status === 'pending' && (cardStatus === 'pending' || cardStatus === 'pending_review')) {
                    show = true;
                } else if (status === cardStatus) {
                    show = true;
                }

                card.style.display = show ? 'flex' : 'none';
                if (show) visibleCount++;
            });

            // Update course count
            courseCount.textContent = visibleCount + ' course(s)';

            // Show/hide no results message
            if (cards.length > 0 && visibleCount === 0) {
                noResultsState.classList.remove('hidden');
            } else {
                noResultsState.classList.add('hidden');
            }
        }

        // Set "All" as default active filter on page load
        document.addEventListener('DOMContentLoaded', function () {
            const allBtn = document.querySelector('.filter-btn[data-filter="all"]');
            if (allBtn) {
                allBtn.classList.remove('bg-gray-100', 'text-gray-700');
                allBtn.classList.add('bg-blue-600', 'text-white');
            }
        });
    </script>
</body>

</html>