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
    </style>
</head>

<body class="bg-gray-50">
    @include('partials.navbar')

    <!-- Simplified Header -->
    <div class="bg-white border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $pageTitle }}</h1>
                    <p class="text-sm md:text-base text-gray-600 mt-1">{{ $facultyName }}</p>
                </div>
                <div class="hidden md:flex items-center space-x-3 text-sm text-gray-600">
                    <span class="flex items-center">
                        <i class="fas fa-layer-group mr-1.5"></i>
                        {{ count($examsData) }} Programs
                    </span>
                    <span class="text-gray-300">•</span>
                    <span class="flex items-center">
                        <i class="fas fa-file-alt mr-1.5"></i>
                        @php
                            $totalExams = 0;
                            foreach ($examsData as $courses) {
                                foreach ($courses as $details) {
                                    $totalExams += count($details);
                                }
                            }
                        @endphp
                        {{ $totalExams }} Papers
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6 md:py-8">

        @if(count($examsData) > 0)
            <!-- Search Bar (Mobile Optimized) -->
            <div class="mb-6">
                <div class="relative">
                    <input type="text" id="search" placeholder="Search by course name..."
                        class="w-full pl-11 pr-4 py-3.5 text-base border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>

                <!-- Filter (Collapsible on mobile) -->
                <div class="mt-3">
                    <select id="programFilter"
                        class="w-full px-4 py-3 text-base border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm bg-white">
                        <option value="">All Programs ({{ count($examsData) }})</option>
                        @foreach($examsData as $program => $courses)
                            <option value="{{ $program }}">{{ $program }} ({{ count($courses) }} courses)</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Papers List (Card-based, Mobile-First) -->
            <div class="space-y-3" id="programsContainer">
                @foreach ($examsData as $program => $courses)
                    @foreach ($courses as $courseUnit => $details)
                        @foreach ($details as $index => $data)
                            <div class="program-card course-row bg-white rounded-xl border border-gray-200 hover:border-gray-300 hover:shadow-md transition-all duration-200"
                                data-program="{{ $program }}" data-course="{{ strtolower($courseUnit) }}">
                                <div class="p-3 md:p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <!-- Course Info -->
                                        <div class="flex-1 min-w-0 pr-2">
                                            @if ($index == 0)
                                                <h3 class="text-sm md:text-lg font-semibold text-gray-900 mb-1.5 leading-snug">
                                                    {{ $courseUnit }}
                                                </h3>
                                            @endif
                                            <div class="flex flex-wrap items-center gap-2 md:gap-3 text-xs md:text-sm text-gray-600">
                                                <span class="flex items-center">
                                                    <i class="fas fa-graduation-cap mr-1 text-xs"></i>
                                                    {{ $program }}
                                                </span>
                                                <span class="flex items-center">
                                                    <i class="fas fa-calendar mr-1 text-xs"></i>
                                                    {{ $data['year'] }}
                                                </span>
                                            </div>
                                        </div>

                                        <!-- Download Button (Compact on mobile) -->
                                        <a href="{{ route('fetch.pdf', ['id' => $data['id']]) }}" target="_blank"
                                            class="flex-shrink-0 inline-flex items-center justify-center px-3 py-2 md:px-5 md:py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-xs md:text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm active:scale-95">
                                            <i class="fas fa-eye md:mr-2"></i>
                                            <span class="hidden md:inline ml-2">View</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                @endforeach
            </div>

            <!-- No Results Message -->
            <div id="noResults" class="hidden bg-white rounded-xl border border-gray-200 p-12 text-center">
                <i class="fas fa-search text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No papers found</h3>
                <p class="text-gray-600">Try adjusting your search or filter</p>
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-xl border border-gray-200 p-12 text-center">
                <i class="fas fa-folder-open text-4xl text-gray-300 mb-4"></i>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">No papers available</h3>
                <p class="text-gray-600">Past exam papers for this program will be available soon.</p>
            </div>
        @endif

        <!-- Back Button -->
        <div class="mt-8">
            <a href="{{ route('welcome') }}"
                class="inline-flex items-center px-5 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                <i class="fas fa-arrow-left mr-2"></i>
                Back to Home
            </a>
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

    <!-- Custom JavaScript -->
    <script>
        // Real-time Search and Filter
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('search');
            const programFilter = document.getElementById('programFilter');
            const programCards = document.querySelectorAll('.program-card');
            const noResults = document.getElementById('noResults');
            const programsContainer = document.getElementById('programsContainer');

            function filterContent() {
                const searchTerm = searchInput.value.toLowerCase().trim();
                const selectedProgram = programFilter.value;
                let visibleCount = 0;

                programCards.forEach(card => {
                    const programName = card.dataset.program;
                    const courseName = card.dataset.course;

                    let showCard = true;

                    // Filter by program
                    if (selectedProgram && programName !== selectedProgram) {
                        showCard = false;
                    }

                    // Filter by search term
                    if (searchTerm && !courseName.includes(searchTerm) && !programName.toLowerCase().includes(searchTerm)) {
                        showCard = false;
                    }

                    if (showCard) {
                        card.classList.remove('hidden');
                        visibleCount++;
                    } else {
                        card.classList.add('hidden');
                    }
                });

                // Show/hide no results message
                if (visibleCount === 0) {
                    noResults.classList.remove('hidden');
                    programsContainer.classList.add('hidden');
                } else {
                    noResults.classList.add('hidden');
                    programsContainer.classList.remove('hidden');
                }
            }

            // Debounce search for better performance
            let searchTimeout;
            searchInput.addEventListener('input', function () {
                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(filterContent, 300);
            });

            programFilter.addEventListener('change', filterContent);
        });
    </script>
</body>

</html>