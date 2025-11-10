<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Lecturer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>

<body class="bg-gray-100">

    @include('partials.admin-navbar')

    <!-- Success Notification -->
    @if(session('success'))
        <div id="successNotification" class="fixed top-20 right-4 z-50 animate-slide-in">
            <div class="bg-green-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3">
                <i class="fas fa-check-circle text-2xl"></i>
                <div>
                    <p class="font-semibold">Success!</p>
                    <p class="text-sm">{{ session('success') }}</p>
                </div>
                <button onclick="closeNotification()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <!-- Error Notification -->
    @if($errors->any())
        <div id="errorNotification" class="fixed top-20 right-4 z-50 animate-slide-in">
            <div class="bg-red-500 text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3">
                <i class="fas fa-exclamation-circle text-2xl"></i>
                <div>
                    <p class="font-semibold">Error!</p>
                    <p class="text-sm">{{ $errors->first() }}</p>
                </div>
                <button onclick="closeErrorNotification()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    @endif

    <div class="p-4 sm:ml-64 mt-20">
        <div class="container mx-auto mt-3 mb-3">
            <div class="flex justify-center items-center">
                <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-2xl">

                    <h2 class="text-2xl font-semibold mb-6 text-gray-800">Edit Lecturer</h2>

                    <!-- ✅ UPDATE FORM -->
                    <form id="editLecturerForm"
                        action="{{ route('admin.update-lecturer-data', ['lecturerId' => $lecturer['id']]) }}"
                        method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <!-- First Name -->
                        <div>
                            <label for="firstName" class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-user mr-2"></i>First Name
                            </label>
                            <input type="text" id="firstName" name="firstName" value="{{ $lecturer['firstName'] }}"
                                required
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="lastName" class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-user mr-2"></i>Last Name
                            </label>
                            <input type="text" id="lastName" name="lastName" value="{{ $lecturer['lastName'] }}"
                                required
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-envelope mr-2"></i>Email Address
                            </label>
                            <input type="email" id="email" name="email" value="{{ $lecturer['email'] }}" required
                                class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Faculties Multi-Select -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-university mr-2"></i>Select Faculties:
                            </label>
                            <div class="border border-gray-300 rounded-md p-3">
                                @foreach($availableFaculties as $faculty)
                                    <label class="inline-flex items-center mr-4">
                                        <input type="checkbox" name="faculties[]" value="{{ $faculty }}"
                                            @if(in_array($faculty, $lecturer['faculties'])) checked @endif
                                            class="form-checkbox h-5 w-5 text-blue-600 rounded focus:ring-blue-500">
                                        <span class="ml-2 text-gray-700">{{ $faculty }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Courses Multi-Select -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-book mr-2"></i>Teaching Courses
                            </label>

                            <!-- Display Currently Assigned Courses with Remove Button -->
                            <div class="mb-2" id="selectedCoursesDisplay">
                                @if(count($lecturer['courses']) > 0)
                                    @foreach($lecturer['courses'] as $courseName)
                                        @php
                                            $course = collect($courseNames)->firstWhere('name', $courseName);
                                        @endphp
                                        @if($course)
                                            <span
                                                class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 mr-2 mb-2 shadow-sm course-tag"
                                                data-course="{{ $course['name'] }}">
                                                {{ $course['name'] }}
                                                <button type="button" onclick="removeCourse('{{ $course['name'] }}')"
                                                    class="ml-1.5 inline-flex items-center justify-center w-4 h-4 text-indigo-600 hover:bg-indigo-200 hover:text-indigo-900 rounded-full transition-colors">
                                                    <i class="fas fa-times text-xs"></i>
                                                </button>
                                            </span>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="text-gray-500 text-sm italic" id="noCoursesText">No courses assigned
                                        yet.</span>
                                @endif
                            </div>

                            <div id="courseDropdown" class="relative">
                                <button type="button"
                                    class="relative block w-full p-3 border border-gray-300 rounded-md shadow-sm text-left cursor-pointer focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                                    onclick="toggleDropdown()">
                                    <span>Select Courses</span>
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-500"></i>
                                    </span>
                                </button>
                                <div id="courseList"
                                    class="absolute z-10 w-full bg-white rounded-md shadow-lg mt-1 hidden max-h-64 overflow-y-auto">
                                    <!-- Search Bar -->
                                    <div class="sticky top-0 bg-white border-b border-gray-200 p-2">
                                        <input type="text" id="courseSearch" placeholder="Search courses..."
                                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 text-sm"
                                            onkeyup="filterCourses()">
                                    </div>
                                    <!-- Course Options -->
                                    <div id="courseOptions">
                                        @foreach($courseNames as $course)
                                            <label
                                                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 course-option"
                                                data-course-name="{{ strtolower($course['name']) }}"
                                                data-course-code="{{ strtolower($course['code']) }}">
                                                <input type="checkbox" name="courses[]" value="{{ $course['name'] }}"
                                                    @if(in_array($course['name'], $lecturer['courses'])) checked @endif
                                                    class="mr-2 h-5 w-5 text-blue-600 rounded focus:ring-blue-500 course-checkbox"
                                                    onchange="updateCourseDisplay()">
                                                <span>{{ $course['name'] }} ({{ $course['code'] }})</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div id="courseError" class="text-red-500 text-sm mt-2 hidden">Please select at least one
                                course.</div>
                        </div>

                        <!-- ✅ BUTTONS CONTAINER (FULL WIDTH) -->
                        <div class="flex flex-col mt-6">
                            <!-- Update Button -->
                            <button type="submit" id="updateButton"
                                class="w-full px-6 py-3 bg-gray-800 text-white rounded-md hover:bg-gray-900 text-center font-semibold flex items-center justify-center">
                                <span id="buttonText">Update</span>
                                <span id="loadingSpinner" class="hidden ml-2">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </span>
                            </button>
                        </div>
                    </form> <!-- ✅ CLOSE UPDATE FORM -->

                    <!-- ✅ DELETE FORM (OUTSIDE THE UPDATE FORM) -->
                    <form action="{{ route('lecturer.delete', ['lecturerId' => $lecturer['id']]) }}" method="POST"
                        class="mt-2">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="w-full px-6 py-3 bg-red-600 text-white rounded-md hover:bg-red-800 text-center font-semibold"
                            onclick="return confirm('Are you sure you want to delete this lecturer? This action cannot be undone.')">
                            Delete Lecturer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>



    <script>
        function toggleDropdown() {
            const courseList = document.getElementById('courseList');
            courseList.classList.toggle('hidden');

            // Clear search when opening dropdown
            if (!courseList.classList.contains('hidden')) {
                document.getElementById('courseSearch').value = '';
                filterCourses();
            }
        }

        // Close dropdown when clicking outside
        document.addEventListener('click', function (event) {
            const dropdown = document.getElementById('courseDropdown');
            const courseList = document.getElementById('courseList');

            if (!dropdown.contains(event.target)) {
                courseList.classList.add('hidden');
            }
        });

        // Filter courses based on search input
        function filterCourses() {
            const searchInput = document.getElementById('courseSearch').value.toLowerCase();
            const courseOptions = document.querySelectorAll('.course-option');

            courseOptions.forEach(option => {
                const courseName = option.getAttribute('data-course-name');
                const courseCode = option.getAttribute('data-course-code');

                if (courseName.includes(searchInput) || courseCode.includes(searchInput)) {
                    option.style.display = 'flex';
                } else {
                    option.style.display = 'none';
                }
            });
        }

        // Remove course from selection
        function removeCourse(courseName) {
            // Uncheck the checkbox
            const checkboxes = document.querySelectorAll('.course-checkbox');
            checkboxes.forEach(checkbox => {
                if (checkbox.value === courseName) {
                    checkbox.checked = false;
                }
            });

            // Update the display
            updateCourseDisplay();
        }

        // Update course display when checkboxes change
        function updateCourseDisplay() {
            const checkboxes = document.querySelectorAll('.course-checkbox');
            const displayArea = document.getElementById('selectedCoursesDisplay');
            const courseData = @json($courseNames);

            // Get all checked courses
            const selectedCourses = [];
            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedCourses.push(checkbox.value);
                }
            });

            // Update display
            if (selectedCourses.length > 0) {
                let html = '';
                selectedCourses.forEach(courseName => {
                    const course = courseData.find(c => c.name === courseName);
                    if (course) {
                        html += `<span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 mr-2 mb-2 shadow-sm course-tag" data-course="${course.name}">
                        ${course.name}
                        <button type="button" onclick="removeCourse('${course.name}')" class="ml-1.5 inline-flex items-center justify-center w-4 h-4 text-indigo-600 hover:bg-indigo-200 hover:text-indigo-900 rounded-full transition-colors">
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </span>`;
                    }
                });
                displayArea.innerHTML = html;
            } else {
                displayArea.innerHTML = '<span class="text-gray-500 text-sm italic" id="noCoursesText">No courses assigned yet.</span>';
            }
        }

        document.getElementById('editLecturerForm').addEventListener('submit', function (e) {
            // Remove course validation - allow updating even with no courses
            document.getElementById('courseError').classList.add('hidden');

            // Show loading indicator
            const updateButton = document.getElementById('updateButton');
            const buttonText = document.getElementById('buttonText');
            const loadingSpinner = document.getElementById('loadingSpinner');

            updateButton.disabled = true;
            updateButton.classList.add('opacity-75', 'cursor-not-allowed');
            buttonText.textContent = 'Updating...';
            loadingSpinner.classList.remove('hidden');
        });

        // Notification functions
        function closeNotification() {
            const notification = document.getElementById('successNotification');
            if (notification) {
                notification.classList.add('animate-slide-out');
                setTimeout(() => notification.remove(), 300);
            }
        }

        function closeErrorNotification() {
            const notification = document.getElementById('errorNotification');
            if (notification) {
                notification.classList.add('animate-slide-out');
                setTimeout(() => notification.remove(), 300);
            }
        }

        // Auto-hide notifications after 5 seconds
        window.addEventListener('DOMContentLoaded', function () {
            const successNotification = document.getElementById('successNotification');
            const errorNotification = document.getElementById('errorNotification');

            if (successNotification) {
                setTimeout(() => {
                    successNotification.classList.add('animate-slide-out');
                    setTimeout(() => successNotification.remove(), 300);
                }, 5000);
            }

            if (errorNotification) {
                setTimeout(() => {
                    errorNotification.classList.add('animate-slide-out');
                    setTimeout(() => errorNotification.remove(), 300);
                }, 5000);
            }
        });
    </script>

    <style>
        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }

            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .animate-slide-in {
            animation: slideIn 0.3s ease-out forwards;
        }

        .animate-slide-out {
            animation: slideOut 0.3s ease-in forwards;
        }
    </style>
</body>

</html>