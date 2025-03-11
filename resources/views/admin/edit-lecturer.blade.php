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

                    <!-- ✅ UPDATE FORM -->
                    <form id="editLecturerForm" action="{{ route('admin.update-lecturer-data', ['lecturerId' => $lecturer['id']]) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- First Name -->
                        <div>
                            <label for="firstName" class="block text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" id="firstName" name="firstName" value="{{ $lecturer['firstName'] }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="lastName" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" id="lastName" name="lastName" value="{{ $lecturer['lastName'] }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ $lecturer['email'] }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm">
                        </div>

                        <!-- Faculties Multi-Select -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Select Faculties:</label>
                            <div class="border border-gray-300 rounded-md p-2">
                                @foreach($availableFaculties as $faculty)
                                    <label>
                                        <input type="checkbox" name="faculties[]" value="{{ $faculty }}" 
                                            @if(in_array($faculty, $lecturer['faculties'])) checked @endif> 
                                        {{ $faculty }}
                                    </label><br>
                                @endforeach
                            </div>
                        </div>

                        <!-- Courses Multi-Select -->
                        <div class="mb-4">
                            <div id="courseDropdown" class="relative">
                                <button type="button" class="block w-full p-2 border border-gray-300 rounded-md shadow-sm" onclick="toggleDropdown()">
                                    Teaching Courses
                                </button>
                                <div id="courseList"
                                    class="hidden absolute text-left z-10 w-full bg-white mt-1 border border-gray-300 rounded-md shadow-lg">
                                    @foreach($courseNames as $course)
                                        <label class="block px-4 py-2 text-sm text-gray-700 flex items-center">
                                            <input type="checkbox" name="courses[]" value="{{ $course['name'] }}" @if(in_array($course['name'], $lecturer['courses'])) checked @endif>
                                            <span class="ml-2 font-semibold">{{ $course['name'] }}</span>
                                            <span class="ml-auto text-gray-500 text-xs">({{ $course['code'] }})</span> <!-- ✅ Course Code Display -->
                                        </label>
                                    @endforeach
                                </div>

                            </div>
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