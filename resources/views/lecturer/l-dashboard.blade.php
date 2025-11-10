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
        <!-- Page Header -->
        <div class="mb-8">
            <div
                class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg shadow-lg p-6 text-white flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-clipboard-list text-2xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">Review Uploaded Exams</h1>
                        <p class="text-blue-100 mt-1">Browse and manage your uploaded exam templates by course</p>
                    </div>
                </div>
            </div>
        </div>
        <!-- Courses Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @forelse ($courses as $courseUnit => $exams)
                <div
                    class="bg-white rounded-xl shadow-md hover:shadow-xl border border-blue-100 transition-all flex flex-col justify-between h-full">
                    <div class="p-6 flex-1 flex flex-col items-center justify-center text-center">
                        <div class="flex items-center justify-center mb-2">
                            <i class="fas fa-book text-blue-500 text-2xl mr-2"></i>
                            <span class="text-xl font-semibold text-gray-900">{{ $courseUnit }}</span>
                        </div>
                        <a href="{{ route('lecturer.l-course-exams', ['courseUnit' => $courseUnit]) }}"
                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg shadow hover:from-blue-700 hover:to-indigo-700 font-medium transition-all">
                            <span>View Exam</span>
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center ">
                    @if(isset($courses) && empty($courses))
                        <!-- No courses assigned at all -->
                        <div class="bg-blue-50 border-l-4 border-blue-400 p-6 rounded-lg max-w-2xl">
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <i class="fas fa-info-circle text-blue-400 text-3xl"></i>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-blue-900 font-bold text-xl mb-2">No Courses Assigned</h3>
                                    <p class="text-blue-800 mb-4">
                                        You currently have no courses assigned to your account. Please contact your faculty
                                        administrator to assign courses to your profile before you can upload exam questions.
                                    </p>
                                    <div class="flex items-center text-blue-700 text-sm">
                                        <i class="fas fa-envelope mr-2"></i>
                                        <span>Contact your faculty admin for assistance</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Courses assigned but no exams uploaded yet -->
                        <img src="https://img.freepik.com/free-vector/error-404-concept-illustration_114360-1811.jpg"
                            alt="No Data Available" class="w-full max-w-lg">
                        <p class="mt-4 text-lg font-semibold text-gray-600">No exams available yet.</p>
                        <a href="{{ route('lecturer.list') }}"
                            class="mt-2 px-5 py-2 bg-blue-600 text-white rounded-lg shadow hover:bg-blue-700 font-medium transition-all flex items-center">
                            <i class="fas fa-plus mr-2"></i> Upload Your First Exam
                        </a>
                    @endif
                </div>
            @endforelse
        </div>
    </div>









</body>

</html>