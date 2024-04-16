<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate Exam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>


</head>
<body class="bg-gray-100">

        @include('partials.admin-navbar')
<div class=" sm:ml-64">
    <div class="min-h-screen flex items-center justify-center">
        <div class="max-w-md w-full">
            <form action="{{ route('admin.view-generated-exam') }}" method="POST" class="bg-white p-6 rounded-lg shadow-lg">
                @csrf
                <div class="mb-4">
                    <label for="courseDropdown" class="block text-sm font-medium text-gray-700">Choose a course</label>
                    <select id="courseDropdown" name="course" class="block w-full mt-1 p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" required>
                        <option value="">Select a course</option> 
                    @foreach($courseNames as $course)
                        <option value="{{ $course['id'] }}">{{ $course['name'] }}</option>
                    @endforeach

                    </select>
                    <div class="text-red-500 mt-1" style="display: none;" id="courseError">Please select a course.</div>
                </div>

                <div class="flex justify-center">
                    <button type="submit" class="inline-block px-6 py-2 border border-transparent text-base font-medium rounded-md text-white bg-gray-800 hover:bg-red-700" onclick="return validateForm();">Generate Exam</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function validateForm() {
        var courseDropdown = document.getElementById('courseDropdown');
        var courseError = document.getElementById('courseError');

        if (courseDropdown.value === '') {
            courseError.style.display = 'block';
            return false;
        } else {
            courseError.style.display = 'none';
            return true;
        }
    }
</script>

</body>
</html>
