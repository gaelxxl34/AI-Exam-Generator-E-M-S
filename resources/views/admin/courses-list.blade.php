<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Courses List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

</head>
<body>
    
    @include('partials.admin-navbar')
    <div class="p-4 sm:ml-64 mt-20 flex justify-center">
<div class="container">
   
@if (!empty($courses))
    @foreach ($courses as $facultyName => $programs)
        <div class="mt-4 p-5 bg-white rounded shadow">
            <h2 class="text-center font-bold text-xl mb-4">{{ $facultyName }}</h2>
            @foreach ($programs as $programName => $programCourses)
                <h3>{{ $programName }}</h3>
                <table class="min-w-full table-auto mb-6">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-4 py-2 text-left">Course Name</th>
                            <th class="px-4 py-2 text-left">Course Code</th>
                            <th class="px-4 py-2 text-left">Year/Semester</th>
                            <th class="px-4 py-2 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($programCourses as $course)
                            <tr>
                                <td class="border px-4 py-2">{{ $course['name'] }}</td>
                                <td class="border px-4 py-2">{{ $course['code'] }}</td>
                                <td class="border px-4 py-2">{{ $course['year_sem'] }}</td>
                                <td class="border px-4 py-2">
                                    <a href="{{ route('edit.course', ['id' => $course['id']]) }}" class="text-blue-500 hover:text-blue-700">Edit</a>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            </div>
        @endforeach
@else
        <div class="mt-8 flex flex-col items-center justify-center">
            <img src="../assets/img/404.jpeg" alt="No Data Available" class="w-1/2 max-w-sm mx-auto">
            <p class="mt-4 text-lg font-semibold text-gray-600">No course details available.</p>
        </div>
    @endif
</div>

    </div>
</body>
</html>