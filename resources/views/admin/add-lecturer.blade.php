<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add Lecturer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

</head>
<body>

    @include('partials.admin-navbar')

    <div class="p-4 sm:ml-64 mt-20 flex justify-center">
        <form id="lecturerForm" action="{{ route('upload.lecturer') }}" method="post" enctype="multipart/form-data"
            class="bg-white p-6 rounded-lg shadow-lg w-3/5">
            @csrf

            @if ($errors->has('upload_error'))
                <div class="bg-red-500 text-white p-3 rounded mb-4 text-center">
                    {{ $errors->first('upload_error') }}
                </div>
            @endif

            <div class="mb-4">
                <label for="firstName" class="block text-sm font-medium text-gray-700">First Name</label>
                <input required type="text" id="firstName" name="firstName"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div class="mb-4">
                <label for="lastName" class="block text-sm font-medium text-gray-700">Last Name</label>
                <input required type="text" id="lastName" name="lastName"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input required type="email" id="email" name="email"
                    class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <!-- Multi-Select Faculty -->
            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700">Select Faculties:</label>
                <div class="border border-gray-300 rounded-md p-2">
                    <label><input type="checkbox" name="faculties[]" value="FST"> Faculty of Science and Technology
                        (FST)</label><br>
                    <label><input type="checkbox" name="faculties[]" value="FBM"> Faculty of Business Management
                        (FBM)</label><br>
                    <label><input type="checkbox" name="faculties[]" value="FOE"> Faculty of Engineering
                        (FOE)</label><br>
                    <label><input type="checkbox" name="faculties[]" value="FOL"> Faculty of Law
                        (FOL)</label><br
                    <label><input type="checkbox" name="faculties[]" value="HEC"> Higher Education Certificate
                        (HEC)</label>
                </div>
                <div class="text-red-500 mt-1 hidden" id="facultyError">Please select at least one faculty.</div>
            </div>

            <!-- Multi-Select Course Dropdown -->
            <div class="mb-4">
                <label for="courseDropdown" class="block text-sm font-medium text-gray-700">Teaching Courses</label>
                <div id="courseDropdown" class="relative">
                    <button type="button"
                        class="block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-left"
                        onclick="toggleDropdown()">
                        Select Courses
                    </button>
                    <div id="courseList" class="hidden absolute z-10 w-full bg-white mt-1 border border-gray-300 rounded-md shadow-lg">
                        @foreach($courseNames as $course)
                            <label class="block px-4 py-2 text-sm text-gray-700">
                                <input type="checkbox" name="courses[]" value="{{ $course['name'] }}">
                                <span class="font-semibold">{{ $course['code'] ?? 'N/A' }}</span> - {{ $course['name'] }}
                            </label>
                        @endforeach
                    </div>

                </div>
                <div class="text-red-500 mt-1 hidden" id="courseError">Please select at least one course.</div>
            </div>

            <input type="hidden" id="password" name="password" value="000000">

            <div class="flex justify-center">
                <button type="submit"
                    class="px-4 py-2 bg-black text-white rounded-md bg-gray-800 hover:bg-red-700">Submit</button>
            </div>

        </form>
    </div>

    <script>
        function toggleDropdown() {
            const courseList = document.getElementById('courseList');
            courseList.classList.toggle('hidden');
        }

        document.getElementById('lecturerForm').addEventListener('submit', function (e) {
            const facultyCheckboxes = document.querySelectorAll('input[name="faculties[]"]:checked');
            const courseCheckboxes = document.querySelectorAll('#courseList input[type="checkbox"]:checked');

            if (facultyCheckboxes.length === 0) {
                e.preventDefault();
                document.getElementById('facultyError').classList.remove('hidden');
            } else {
                document.getElementById('facultyError').classList.add('hidden');
            }

            if (courseCheckboxes.length === 0) {
                e.preventDefault();
                document.getElementById('courseError').classList.remove('hidden');
            } else {
                document.getElementById('courseError').classList.add('hidden');
            }
        });
    </script>

</body>

</html>