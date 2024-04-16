<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Review Uploaded Exams</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

 
</head>
<body>
    
    @include('partials.lecturer-navbar')

<div class="p-4 sm:ml-64 mt-20">
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse ($courses as $courseUnit => $exams)
            <div class="flex flex-col items-center justify-center bg-white rounded-lg border shadow-md hover:shadow-lg">
                <a href="{{ route('lecturer.l-course-exams', ['courseUnit' => $courseUnit]) }}" class="p-6 w-full text-center">
                    <h5 class="mb-2 text-xl md:text-2xl font-bold tracking-tight text-gray-900">{{ $courseUnit }}</h5>
                </a>
            </div>
        @empty
            <div class="col-span-full flex flex-col items-center justify-center ">
                <img src="https://img.freepik.com/free-vector/error-404-concept-illustration_114360-1811.jpg" alt="No Data Available" class="w-full max-w-lg">
                <p class="mt-4 text-lg font-semibold text-gray-600">No courses available.</p>
            </div>
        @endforelse
    </div>
</div>








    
</body>
</html>