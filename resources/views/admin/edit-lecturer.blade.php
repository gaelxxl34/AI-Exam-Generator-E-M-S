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

</head>
<body>
    
    @include('partials.admin-navbar')
    <div class="p-4 sm:ml-64 mt-20">
        <div class="container mx-auto mt-3 mb-3 text-center">
            <div class="flex justify-center items-center">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                    <form action="{{ route('admin.update-lecturer-data', ['lecturerId' => $lecturer['id']] ) }}" enctype="multipart/form-data" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <!-- First Name -->
                        <div>
                            <label for="firstName" class="block text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" id="firstName" name="firstName" value="{{ $lecturer['firstName'] }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="lastName" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" id="lastName" name="lastName" value="{{ $lecturer['lastName'] }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ $lecturer['email'] }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                    <!-- Courses Dropdown (with checkboxes) -->
<div class="mb-4">
    <label for="courseDropdown" class="block text-sm font-medium text-gray-700">Teaching Courses</label>
    <div id="courseDropdown" class="relative">
        <button type="button" class="block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500 text-left" onclick="toggleDropdown()">
            Current Courses
        </button>
        <div id="courseList" class="hidden absolute z-10 w-full bg-white mt-1 border border-gray-300 rounded-md shadow-lg">
            @foreach($courseNames as $course)
                <label class="block px-4 py-2 text-sm text-gray-700 text-left"> <!-- Aligned to left -->
                    <input type="checkbox" name="courses[]" value="{{ $course['name'] }}" 
                        @if(in_array($course['name'], $lecturer['courses'])) checked @endif> 
                    {{ $course['name'] }}
                </label>
            @endforeach
        </div>
    </div>
    <div class="text-red-500 mt-1 hidden" id="courseError">Please select at least one course.</div>
</div>

                        <!-- Update Button -->
                        <div class="flex justify-center">
                            <button type="submit" class="inline-block w-full px-6 py-2 border border-transparent text-base font-medium rounded-md text-white bg-gray-800 hover:bg-gray-900" onclick="return confirm('Are you sure you want to update the lecturer data?')">Update</button>
                        </div>


                    </form>

                    <!-- Success Message -->
                    @if (session('success'))
                        <div class="mt-2 p-1 bg-green-100 border border-green-400 text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Error Message -->
                    @if (session('error'))
                        <div class="mt-2 p-4 bg-red-100 border border-red-400 text-red-700">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('lecturer.delete', ['lecturerId' => $lecturer['id']]) }}" method="POST" class="mt-2">
                        @csrf
                        @method('DELETE')
                        <div class="flex justify-center">
                            <button type="submit" class="inline-block w-full px-3 py-2 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700" onclick="return confirm('Are you sure you want to delete this lecturer?')">Delete</button>
                        </div>
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

    document.getElementById('lecturerForm').addEventListener('submit', function(e) {
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