<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }} - Past Exams</title>

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="/assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="/assets/css/style.css" rel="stylesheet">
    <link href="/assets/css/navbar.css" rel="stylesheet">

    <!-- TailwindCSS (CDN) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'ui-sans-serif', 'system-ui'] }
                }
            }
        }
    </script>

    <style>
        /* Smooth transitions */
        * {
            -webkit-tap-highlight-color: transparent;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Skeleton Loading Animation */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
        }

        @keyframes skeleton-loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        /* Year badge styles */
        .year-badge {
            transition: all 0.2s ease;
        }

        .year-badge:hover {
            transform: scale(1.05);
        }

        /* Course card expand animation */
        .course-years-container {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .course-years-container.expanded {
            max-height: 500px;
        }

        /* Filter chips active state */
        .filter-chip.active {
            background-color: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        /* Load more button pulse */
        @keyframes pulse-soft {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .loading-more {
            animation: pulse-soft 1.5s infinite;
        }
    </style>
</head>

<body class="bg-gray-50">
    @include('partials.navbar')

    <!-- Breadcrumb Navigation -->
    <div class="bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3">
            <nav class="flex items-center text-sm" aria-label="Breadcrumb">
                <a href="{{ route('welcome') }}" class="text-gray-500 hover:text-blue-600 transition-colors">
                    <i class="fas fa-home mr-1"></i>
                    <span class="hidden sm:inline">Home</span>
                </a>
                <i class="fas fa-chevron-right text-gray-300 mx-2 text-xs"></i>
                <span class="text-gray-500">{{ $facultyName }}</span>
                <i class="fas fa-chevron-right text-gray-300 mx-2 text-xs"></i>
                <span class="text-gray-900 font-medium">{{ $pageTitle }}</span>
            </nav>
        </div>
    </div>

    <!-- Enhanced Header -->
    <div class="bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <div
                            class="w-12 h-12 rounded-xl bg-gradient-to-br from-{{ $facultyColor ?? 'blue' }}-500 to-{{ $facultyColor ?? 'blue' }}-600 flex items-center justify-center shadow-lg">
                            <i class="fas fa-{{ $facultyIcon ?? 'book' }} text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $pageTitle }}</h1>
                            <p class="text-sm text-gray-600">{{ $facultyName }}</p>
                        </div>
                    </div>
                </div>

                <!-- Stats Pills -->
                <div class="flex flex-wrap items-center gap-2">
                    @php
                        $totalExams = 0;
                        $allYears = [];
                        foreach ($examsData as $courses) {
                            foreach ($courses as $details) {
                                $totalExams += count($details);
                                foreach ($details as $d) {
                                    $allYears[$d['year']] = true;
                                }
                            }
                        }
                        krsort($allYears);
                    @endphp
                    <span
                        class="inline-flex items-center px-3 py-1.5 rounded-full bg-blue-50 text-blue-700 text-sm font-medium">
                        <i class="fas fa-layer-group mr-1.5 text-xs"></i>
                        {{ count($examsData) }} Programs
                    </span>
                    <span
                        class="inline-flex items-center px-3 py-1.5 rounded-full bg-green-50 text-green-700 text-sm font-medium">
                        <i class="fas fa-file-alt mr-1.5 text-xs"></i>
                        {{ $totalExams }} Papers
                    </span>
                    <span
                        class="inline-flex items-center px-3 py-1.5 rounded-full bg-purple-50 text-purple-700 text-sm font-medium">
                        <i class="fas fa-calendar mr-1.5 text-xs"></i>
                        {{ count($allYears) }} Years
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">

        @if(count($examsData) > 0)
            <!-- Enhanced Search & Filter Section -->
            <div class="bg-white rounded-xl border border-gray-200 p-4 mb-6 shadow-sm">
                <!-- Search Bar -->
                <div class="relative mb-4">
                    <input type="text" id="search" placeholder="Search by course name or code..."
                        class="w-full pl-11 pr-10 py-3.5 text-base border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <button id="clearSearch"
                        class="hidden absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>

                <!-- Filters Row -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <!-- Program Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Program</label>
                        <select id="programFilter"
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                            <option value="">All Programs</option>
                            @foreach($examsData as $program => $courses)
                                <option value="{{ $program }}">{{ $program }} ({{ count($courses) }})</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Year Filter -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Year</label>
                        <select id="yearFilter"
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                            <option value="">All Years</option>
                            @foreach(array_keys($allYears) as $year)
                                <option value="{{ $year }}">{{ $year }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Sort By -->
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1.5">Sort By</label>
                        <select id="sortBy"
                            class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white">
                            <option value="name-asc">Course Name (A-Z)</option>
                            <option value="name-desc">Course Name (Z-A)</option>
                            <option value="year-desc" selected>Year (Newest First)</option>
                            <option value="year-asc">Year (Oldest First)</option>
                        </select>
                    </div>
                </div>

                <!-- Active Filters & Results Count -->
                <div class="flex flex-wrap items-center justify-between mt-4 pt-3 border-t border-gray-100">
                    <div id="activeFilters" class="flex flex-wrap items-center gap-2">
                        <span class="text-sm text-gray-500">Active filters:</span>
                        <span id="noFiltersText" class="text-sm text-gray-400 italic">None</span>
                    </div>
                    <div class="text-sm text-gray-600 mt-2 sm:mt-0">
                        <span id="resultsCount">{{ $totalExams }}</span> papers found
                    </div>
                </div>
            </div>

            <!-- Skeleton Loading (shown during filter) -->
            <div id="skeletonLoader" class="hidden space-y-3 mb-6">
                @for($i = 0; $i < 5; $i++)
                    <div class="bg-white rounded-xl border border-gray-200 p-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1">
                                <div class="skeleton h-5 w-3/4 rounded mb-2"></div>
                                <div class="skeleton h-4 w-1/2 rounded"></div>
                            </div>
                            <div class="skeleton h-10 w-24 rounded-lg"></div>
                        </div>
                    </div>
                @endfor
            </div>

            <!-- Papers List - Grouped by Course -->
            <div class="space-y-4" id="programsContainer">
                @php
                    // Reorganize data: group all years under each course
                    $groupedCourses = [];
                    foreach ($examsData as $program => $courses) {
                        foreach ($courses as $courseUnit => $details) {
                            $key = $courseUnit . '___' . $program;
                            if (!isset($groupedCourses[$key])) {
                                $groupedCourses[$key] = [
                                    'courseUnit' => $courseUnit,
                                    'program' => $program,
                                    'years' => []
                                ];
                            }
                            foreach ($details as $data) {
                                $groupedCourses[$key]['years'][] = $data;
                            }
                        }
                    }
                    // Sort years within each course (newest first)
                    foreach ($groupedCourses as &$course) {
                        usort($course['years'], function ($a, $b) {
                            return strcmp($b['year'], $a['year']);
                        });
                    }
                    unset($course);
                @endphp

                @foreach ($groupedCourses as $key => $course)
                    <div class="course-card bg-white rounded-xl border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all duration-200 overflow-hidden"
                        data-program="{{ $course['program'] }}" data-course="{{ strtolower($course['courseUnit']) }}"
                        data-years="{{ implode(',', array_column($course['years'], 'year')) }}">

                        <!-- Course Header -->
                        <div class="p-4 cursor-pointer" onclick="toggleCourseYears('{{ md5($key) }}')">
                            <div class="flex items-start justify-between gap-3">
                                <!-- Course Info -->
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="flex-shrink-0 w-10 h-10 rounded-lg bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                                            <i class="fas fa-file-alt text-blue-600"></i>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3
                                                class="text-base md:text-lg font-semibold text-gray-900 leading-snug line-clamp-2">
                                                {{ $course['courseUnit'] }}
                                            </h3>
                                            <div class="flex flex-wrap items-center gap-2 mt-1.5">
                                                <span
                                                    class="inline-flex items-center text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">
                                                    <i class="fas fa-graduation-cap mr-1"></i>
                                                    {{ $course['program'] }}
                                                </span>
                                                <span
                                                    class="inline-flex items-center text-xs text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">
                                                    <i class="fas fa-copy mr-1"></i>
                                                    {{ count($course['years']) }}
                                                    {{ count($course['years']) === 1 ? 'paper' : 'papers' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Expand/Quick Access -->
                                <div class="flex items-center gap-2">
                                    @if(count($course['years']) === 1)
                                        <!-- Single paper - show direct view button -->
                                        <a href="{{ route('fetch.pdf', ['id' => $course['years'][0]['id']]) }}" target="_blank"
                                            onclick="event.stopPropagation()"
                                            class="inline-flex items-center justify-center px-4 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm active:scale-95">
                                            <i class="fas fa-eye mr-2"></i>
                                            <span>View {{ $course['years'][0]['year'] }}</span>
                                        </a>
                                    @else
                                        <!-- Multiple papers - show expand button -->
                                        <button
                                            class="inline-flex items-center justify-center px-4 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium rounded-lg transition-colors duration-200">
                                            <span class="mr-2">Select Year</span>
                                            <i class="fas fa-chevron-down text-xs transition-transform"
                                                id="chevron-{{ md5($key) }}"></i>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if(count($course['years']) > 1)
                            <!-- Expandable Year Selection -->
                            <div id="years-{{ md5($key) }}" class="course-years-container border-t border-gray-100 bg-gray-50">
                                <div class="p-4">
                                    <p class="text-xs text-gray-500 mb-3">
                                        <i class="fas fa-calendar-alt mr-1"></i>
                                        Select a year to view the past paper:
                                    </p>
                                    <div class="flex flex-wrap gap-2">
                                        @foreach($course['years'] as $yearData)
                                            <a href="{{ route('fetch.pdf', ['id' => $yearData['id']]) }}" target="_blank"
                                                class="year-badge inline-flex items-center px-4 py-2 bg-white border border-gray-200 hover:border-blue-500 hover:bg-blue-50 text-gray-700 hover:text-blue-700 text-sm font-medium rounded-lg transition-all duration-200 shadow-sm">
                                                <i class="fas fa-file-pdf mr-2 text-red-500"></i>
                                                {{ $yearData['year'] }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

            <!-- Load More Button -->
            <div id="loadMoreContainer" class="hidden mt-6 text-center">
                <button id="loadMoreBtn" onclick="loadMoreCourses()"
                    class="inline-flex items-center justify-center px-6 py-3 bg-white border border-gray-300 text-gray-700 font-medium rounded-xl hover:bg-gray-50 transition-all duration-200 shadow-sm">
                    <i class="fas fa-plus-circle mr-2"></i>
                    Load More Papers
                    <span id="remainingCount" class="ml-2 text-xs bg-gray-100 px-2 py-0.5 rounded-full"></span>
                </button>
            </div>

            <!-- No Results Message -->
            <div id="noResults" class="hidden bg-white rounded-xl border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-search text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No papers found</h3>
                <p class="text-gray-600 mb-4">Try adjusting your search or filters</p>
                <button onclick="clearAllFilters()"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fas fa-redo mr-2"></i>
                    Clear All Filters
                </button>
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <div class="w-20 h-20 mx-auto mb-6 rounded-full bg-gray-100 flex items-center justify-center">
                    <i class="fas fa-folder-open text-3xl text-gray-400"></i>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-2">No papers available</h3>
                <p class="text-gray-600 mb-6">Past exam papers for this program will be available soon.</p>
                <a href="{{ route('welcome') }}"
                    class="inline-flex items-center px-5 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition-colors">
                    <i class="fas fa-home mr-2"></i>
                    Back to Home
                </a>
            </div>
        @endif

        <!-- Back Button -->
        <div class="mt-8 flex items-center justify-between">
            <a href="{{ route('welcome') }}"
                class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200 shadow-sm">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Home
            </a>

            <!-- Scroll to top (mobile) -->
            <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
                class="md:hidden inline-flex items-center justify-center w-10 h-10 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-arrow-up text-gray-600"></i>
            </button>
        </div>
    </div>

    @include('partials.footer')

    <!-- Back to Top -->
    <a href="#" class="btn btn-dark back-to-top"><i class="fa fa-angle-up"></i></a>

    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/lib/easing/easing.min.js"></script>
    <script src="/assets/lib/owlcarousel/owl.carousel.min.js"></script>
    <script src="/assets/js/main.js"></script>

    <!-- Enhanced JavaScript -->
    <script>
        // Configuration
        const ITEMS_PER_PAGE = 20;
        let currentPage = 1;
        let filteredCourses = [];

        // DOM Elements
        const searchInput = document.getElementById('search');
        const clearSearchBtn = document.getElementById('clearSearch');
        const programFilter = document.getElementById('programFilter');
        const yearFilter = document.getElementById('yearFilter');
        const sortBy = document.getElementById('sortBy');
        const courseCards = document.querySelectorAll('.course-card');
        const noResults = document.getElementById('noResults');
        const programsContainer = document.getElementById('programsContainer');
        const skeletonLoader = document.getElementById('skeletonLoader');
        const resultsCount = document.getElementById('resultsCount');
        const activeFilters = document.getElementById('activeFilters');
        const noFiltersText = document.getElementById('noFiltersText');
        const loadMoreContainer = document.getElementById('loadMoreContainer');
        const loadMoreBtn = document.getElementById('loadMoreBtn');
        const remainingCount = document.getElementById('remainingCount');

        // Initialize
        document.addEventListener('DOMContentLoaded', function () {
            initializeFilters();
            applyFiltersAndSort();
        });

        function initializeFilters() {
            // Search input
            let searchTimeout;
            searchInput.addEventListener('input', function () {
                clearSearchBtn.classList.toggle('hidden', !this.value);
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(() => {
                    showLoading();
                    setTimeout(() => applyFiltersAndSort(), 300);
                }, 300);
            });

            // Clear search button
            clearSearchBtn.addEventListener('click', function () {
                searchInput.value = '';
                this.classList.add('hidden');
                applyFiltersAndSort();
            });

            // Filters
            programFilter.addEventListener('change', () => {
                showLoading();
                setTimeout(() => applyFiltersAndSort(), 200);
            });

            yearFilter.addEventListener('change', () => {
                showLoading();
                setTimeout(() => applyFiltersAndSort(), 200);
            });

            sortBy.addEventListener('change', () => {
                showLoading();
                setTimeout(() => applyFiltersAndSort(), 200);
            });
        }

        function showLoading() {
            skeletonLoader.classList.remove('hidden');
            programsContainer.classList.add('opacity-50');
        }

        function hideLoading() {
            skeletonLoader.classList.add('hidden');
            programsContainer.classList.remove('opacity-50');
        }

        function applyFiltersAndSort() {
            const searchTerm = searchInput.value.toLowerCase().trim();
            const selectedProgram = programFilter.value;
            const selectedYear = yearFilter.value;
            const sortOption = sortBy.value;

            // Convert NodeList to Array for sorting
            let cardsArray = Array.from(courseCards);
            let visibleCount = 0;

            // Filter
            cardsArray.forEach(card => {
                const programName = card.dataset.program;
                const courseName = card.dataset.course;
                const years = card.dataset.years.split(',');

                let showCard = true;

                // Filter by program
                if (selectedProgram && programName !== selectedProgram) {
                    showCard = false;
                }

                // Filter by year
                if (selectedYear && !years.includes(selectedYear)) {
                    showCard = false;
                }

                // Filter by search term
                if (searchTerm && !courseName.includes(searchTerm) && !programName.toLowerCase().includes(searchTerm)) {
                    showCard = false;
                }

                card.dataset.visible = showCard ? 'true' : 'false';

                if (showCard) {
                    visibleCount++;
                }
            });

            // Sort visible cards
            const visibleCards = cardsArray.filter(card => card.dataset.visible === 'true');
            visibleCards.sort((a, b) => {
                const nameA = a.dataset.course;
                const nameB = b.dataset.course;
                const yearA = a.dataset.years.split(',')[0];
                const yearB = b.dataset.years.split(',')[0];

                switch (sortOption) {
                    case 'name-asc':
                        return nameA.localeCompare(nameB);
                    case 'name-desc':
                        return nameB.localeCompare(nameA);
                    case 'year-desc':
                        return yearB.localeCompare(yearA);
                    case 'year-asc':
                        return yearA.localeCompare(yearB);
                    default:
                        return 0;
                }
            });

            // Re-order DOM
            visibleCards.forEach(card => programsContainer.appendChild(card));

            // Show/hide cards
            cardsArray.forEach(card => {
                card.classList.toggle('hidden', card.dataset.visible !== 'true');
            });

            // Update results count
            resultsCount.textContent = visibleCount;

            // Update active filters display
            updateActiveFilters(searchTerm, selectedProgram, selectedYear);

            // Show/hide no results
            if (visibleCount === 0) {
                noResults.classList.remove('hidden');
                programsContainer.classList.add('hidden');
            } else {
                noResults.classList.add('hidden');
                programsContainer.classList.remove('hidden');
            }

            // Handle pagination (load more)
            handlePagination(visibleCards);

            hideLoading();
        }

        function updateActiveFilters(search, program, year) {
            let filters = [];

            if (search) {
                filters.push(`<span class="inline-flex items-center px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full">
                    Search: "${search}"
                    <button onclick="clearFilter('search')" class="ml-1 hover:text-blue-900">&times;</button>
                </span>`);
            }

            if (program) {
                filters.push(`<span class="inline-flex items-center px-2 py-1 bg-green-100 text-green-700 text-xs rounded-full">
                    Program: ${program}
                    <button onclick="clearFilter('program')" class="ml-1 hover:text-green-900">&times;</button>
                </span>`);
            }

            if (year) {
                filters.push(`<span class="inline-flex items-center px-2 py-1 bg-purple-100 text-purple-700 text-xs rounded-full">
                    Year: ${year}
                    <button onclick="clearFilter('year')" class="ml-1 hover:text-purple-900">&times;</button>
                </span>`);
            }

            if (filters.length > 0) {
                noFiltersText.classList.add('hidden');
                activeFilters.innerHTML = `<span class="text-sm text-gray-500">Active filters:</span>` + filters.join('');
            } else {
                activeFilters.innerHTML = `<span class="text-sm text-gray-500">Active filters:</span>
                    <span class="text-sm text-gray-400 italic">None</span>`;
            }
        }

        function clearFilter(type) {
            switch (type) {
                case 'search':
                    searchInput.value = '';
                    clearSearchBtn.classList.add('hidden');
                    break;
                case 'program':
                    programFilter.value = '';
                    break;
                case 'year':
                    yearFilter.value = '';
                    break;
            }
            applyFiltersAndSort();
        }

        function clearAllFilters() {
            searchInput.value = '';
            clearSearchBtn.classList.add('hidden');
            programFilter.value = '';
            yearFilter.value = '';
            sortBy.value = 'year-desc';
            applyFiltersAndSort();
        }

        function handlePagination(visibleCards) {
            const totalVisible = visibleCards.length;
            const showCount = currentPage * ITEMS_PER_PAGE;

            visibleCards.forEach((card, index) => {
                if (index < showCount) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });

            // Show/hide load more button
            if (totalVisible > showCount) {
                loadMoreContainer.classList.remove('hidden');
                remainingCount.textContent = `${totalVisible - showCount} more`;
            } else {
                loadMoreContainer.classList.add('hidden');
            }
        }

        function loadMoreCourses() {
            currentPage++;
            loadMoreBtn.classList.add('loading-more');
            loadMoreBtn.disabled = true;

            setTimeout(() => {
                applyFiltersAndSort();
                loadMoreBtn.classList.remove('loading-more');
                loadMoreBtn.disabled = false;
            }, 300);
        }

        // Toggle course year expansion
        function toggleCourseYears(id) {
            const container = document.getElementById('years-' + id);
            const chevron = document.getElementById('chevron-' + id);

            if (container) {
                container.classList.toggle('expanded');
                if (chevron) {
                    chevron.classList.toggle('rotate-180');
                }
            }
        }
    </script>
</body>

</html>