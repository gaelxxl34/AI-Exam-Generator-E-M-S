<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add Courses</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

</head>
<body>
    
    @include('partials.admin-navbar')
<div class="p-4 sm:ml-64 mt-20 flex justify-center">
    <form action="{{ route('upload.courses') }}" method="post" enctype="multipart/form-data" class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 w-2/5">

        @csrf

        <div class="mb-4">
            <label for="courseUnit" class="block text-gray-700 text-sm font-bold mb-2">Course Unit:</label>
            <input type="text" id="courseUnit" name="courseUnit" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        <div class="mb-4">
            <label for="courseCode" class="block text-gray-700 text-sm font-bold mb-2">Course Code:</label>
            <input type="text" id="courseCode" name="courseCode" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        <div class="mb-4">
            <label for="year" class="block text-gray-700 text-sm font-bold mb-2">Year:</label>
            <select id="year" name="year" class="block appearance-none w-full bg-white border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" required>
                <option value="">Select Year</option>
                <option value="1">1</option>
                <option value="2">2</option>
                <option value="3">3</option>
                <option value="4">4</option>
                <option value="5">5</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="semester" class="block text-gray-700 text-sm font-bold mb-2">Semester:</label>
            <select id="semester" name="semester" class="block appearance-none w-full bg-white border border-gray-200 text-gray-700 py-3 px-4 pr-8 rounded leading-tight focus:outline-none focus:bg-white focus:border-gray-500" required>
                <option value="">Select Semester</option>
                <option value="1">Semester 1</option>
                <option value="2">Semester 2</option>
            </select>
        </div>
        <div class="mb-6">
            <label for="program" class="block text-gray-700 text-sm font-bold mb-2">Program:</label>
            <input type="text" id="program" name="program" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
        </div>
        <div class="flex justify-center">
            <button type="submit" class="bg-gray-700 text-white hover:bg-red-700 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                Submit
            </button>
        </div>
    </form>
</div>


</body>
</html>