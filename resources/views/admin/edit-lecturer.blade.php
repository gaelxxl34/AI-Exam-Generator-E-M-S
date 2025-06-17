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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>
<body class="bg-gray-100">
    
    @include('partials.admin-navbar')
    <div class="p-4 sm:ml-64 mt-20">
        <div class="container mx-auto mt-3 mb-3">
            <div class="flex justify-center items-center">
                <div class="bg-white rounded-lg shadow-lg p-8 w-full max-w-2xl">

                    <h2 class="text-2xl font-semibold mb-6 text-gray-800">Edit Lecturer</h2>

                    <!-- ✅ UPDATE FORM -->
                    <form id="editLecturerForm" action="{{ route('admin.update-lecturer-data', ['lecturerId' => $lecturer['id']]) }}" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <!-- First Name -->
                        <div>
                            <label for="firstName" class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-user mr-2"></i>First Name
                            </label>
                            <input type="text" id="firstName" name="firstName" value="{{ $lecturer['firstName'] }}" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="lastName" class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-user mr-2"></i>Last Name
                            </label>
                            <input type="text" id="lastName" name="lastName" value="{{ $lecturer['lastName'] }}" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                <i class="fas fa-envelope mr-2"></i>Email Address
                            </label>
                            <input type="email" id="email" name="email" value="{{ $lecturer['email'] }}" required class="mt-1 block w-full px-4 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500">
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

                            <!-- Display Currently Assigned Courses -->
                            <div class="mb-2">
                                @if(count($lecturer['courses']) > 0)
                                    @foreach($lecturer['courses'] as $courseName)
                                        @php
                                            $course = collect($courseNames)->firstWhere('name', $courseName);
                                        @endphp
                                        @if($course)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold bg-indigo-100 text-indigo-800 mr-2 shadow-sm">
                                                {{ $course['name'] }}
                                            </span>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="text-gray-500 text-sm italic">No courses assigned yet.</span>
                                @endif
                            </div>

                            <div id="courseDropdown" class="relative">
                                <button type="button" class="relative block w-full p-3 border border-gray-300 rounded-md shadow-sm text-left cursor-pointer focus:outline-none focus:ring-blue-500 focus:border-blue-500" onclick="toggleDropdown()">
                                    <span>Select Courses</span>
                                    <span class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-500"></i>
                                    </span>
                                </button>
                                <div id="courseList" class="absolute z-10 w-full bg-white rounded-md shadow-lg mt-1 hidden">
                                    @foreach($courseNames as $course)
                                        <label class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            <input type="checkbox" name="courses[]" value="{{ $course['name'] }}" @if(in_array($course['name'], $lecturer['courses'])) checked @endif class="mr-2 h-5 w-5 text-blue-600 rounded focus:ring-blue-500">
                                            <span>{{ $course['name'] }} ({{ $course['code'] }})</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                            <div id="courseError" class="text-red-500 text-sm mt-2 hidden">Please select at least one course.</div>
                        </div>

                        <!-- ✅ BUTTONS CONTAINER (FULL WIDTH) -->
                        <div class="flex flex-col mt-6">
                            <!-- Update Button -->
                            <button type="submit"
                                class="w-full px-6 py-3 bg-gray-800 text-white rounded-md hover:bg-gray-900 text-center font-semibold">
                                Update
                            </button>
                        </div>
                    </form> <!-- ✅ CLOSE UPDATE FORM -->

                    <!-- ✅ DELETE FORM (OUTSIDE THE UPDATE FORM) -->
                    <form action="{{ route('lecturer.delete', ['lecturerId' => $lecturer['id']]) }}" method="POST" class="mt-2">
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
    }

    document.getElementById('editLecturerForm').addEventListener('submit', function(e) {
        const checkboxes = document.querySelectorAll('#courseList input[type="checkbox"]');
        const isChecked = Array.from(checkboxes).some(checkbox => checkbox.checked);

        if (!isChecked) {
            e.preventDefault(); // Stop form submission
            document.getElementById('courseError').classList.remove('hidden');
        } else {
            document.getElementById('courseError').classList.add('hidden');
        }
    });
</script>
</body>
</html>