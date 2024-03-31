<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

    
</head>
<body>
    
    @include('partials.lecturer-navbar')

    <div class="p-4 sm:ml-64 mt-20">
        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 mt-2">
            <div class="flex-1 bg-gray-200 rounded-lg p-4 shadow">
                <div class="text-gray-900 text-lg">Courses</div>
                <div class="text-gray-600 text-2xl">43</div>
            </div>
            <div class="flex-1 bg-gray-200 rounded-lg p-4 shadow">
                <div class="text-gray-900 text-lg">Past Exams</div>
                <div class="text-gray-600 text-2xl">456</div>
            </div>
            <div class="flex-1 bg-gray-200 rounded-lg p-4 shadow">
                <div class="text-gray-900 text-lg">Uploaded Exams</div>
                <div class="text-gray-600 text-2xl">789</div>
            </div>
           
        </div>
    </div>
    
</body>
</html>