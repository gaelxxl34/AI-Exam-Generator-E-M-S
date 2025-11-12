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
                <div class="col-span-full flex flex-col items-center justify-center py-12">
                    <!-- No exams uploaded yet -->
                    <div class="text-center max-w-md">
                        <div class="mb-6">
                            <i class="fas fa-file-upload text-gray-300 text-8xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-700 mb-3">No Exams Uploaded Yet</h3>
                        <p class="text-gray-600 mb-6">
                            You haven't uploaded any exam questions yet. Get started by uploading your first exam template
                            to begin managing your course assessments.
                        </p>
                        <a href="{{ route('lecturer.list') }}"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg shadow-lg hover:from-blue-700 hover:to-indigo-700 font-medium transition-all transform hover:scale-105">
                            <i class="fas fa-plus mr-2"></i> Upload Your First Exam
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </div>









</body>

</html>