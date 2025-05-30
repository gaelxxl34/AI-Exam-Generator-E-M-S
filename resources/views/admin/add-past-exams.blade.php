<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Upload Past Exams</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
</head>

<body>
    @include('partials.admin-navbar')

    <div class="p-4 sm:ml-64 flex justify-center items-center min-h-screen">
        <form action="/upload-past-exam" method="post" enctype="multipart/form-data"
            class="w-3/5 bg-white rounded-lg p-6 shadow-lg mx-auto" id="uploadForm">
            @csrf

            <div class="mb-4">
                <label for="courseUnit" class="block text-sm font-medium text-gray-900">Choose a course unit</label>
                <select id="courseUnit" name="courseUnit"
                    class="block w-full mt-1 p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                    required>
                    <option value="">Select a course</option>
                    @foreach($courseNames as $course)
                        <option value="{{ $course['name'] }}">{{ $course['name'] }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label for="program" class="block text-sm font-medium text-gray-700">Program:</label>
                <input type="text" id="program" name="program"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                    placeholder="Enter program name" required>
            </div>

            <div class="mb-4">
                <label for="year" class="block text-sm font-medium text-gray-700">Year:</label>
                <select name="year" id="year"
                    class="mt-1 mb-3 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                    required>
                    <option value="">Select a year</option>
                    @for($y = 2022; $y <= 2030; $y++)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>

            <!-- New Exam Period Selection -->
            <div class="mb-4">
                <label for="examPeriod" class="block text-sm font-medium text-gray-700">Examination Period:</label>
                <select name="examPeriod" id="examPeriod"
                    class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md"
                    required>
                    <option value="">Select Exam Period</option>
                    <option value="April">April</option>
                    <option value="August">August</option>
                    <option value="December">December</option>
                </select>
            </div>

            <div class="mb-4">
                <label for="fileUpload" class="block text-sm font-medium text-gray-900">Upload file</label>
                <input type="file" id="fileUpload" name="fileUpload" accept=".pdf"
                    class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer focus:outline-none focus:border-indigo-500 focus:ring-indigo-500"
                    required>
            </div>

            <div class="flex justify-center mt-4">
                <button type="submit" class="px-4 py-2 rounded-md bg-gray-700 text-white hover:bg-red-700">
                    Upload past exam
                </button>
            </div>
        </form>
    </div>
</body>

</html>