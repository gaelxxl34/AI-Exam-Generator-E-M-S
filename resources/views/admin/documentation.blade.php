<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Documentation | Faculty Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-50">

    @include('partials.admin-navbar')

    <div class="p-4 sm:ml-64 mt-24">
        <div class="max-w-5xl mx-auto">

            {{-- Header --}}
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-blue-700"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">Faculty Admin Documentation</h1>
                </div>
                <p class="text-gray-600">Complete guide to managing your faculty's lecturers, courses, and past exam papers.</p>
            </div>

            {{-- Role Overview --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-3">
                    <i class="fas fa-user-shield text-blue-600 mr-2"></i>Your Role: Faculty Administrator
                </h2>
                <p class="text-gray-600 leading-relaxed">
                    As a <strong>Faculty Administrator</strong>, you manage all operations within your assigned faculty. 
                    This includes registering new lecturers, assigning them to courses, managing the course catalog, 
                    and handling past exam paper uploads for student access.
                </p>
            </div>

            {{-- Quick Navigation --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <a href="#lecturer-management" class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition text-center">
                    <i class="fas fa-chalkboard-teacher text-blue-600 text-xl mb-2"></i>
                    <p class="text-sm font-medium text-gray-700">Lecturers</p>
                </a>
                <a href="#course-management" class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition text-center">
                    <i class="fas fa-book-open text-green-600 text-xl mb-2"></i>
                    <p class="text-sm font-medium text-gray-700">Courses</p>
                </a>
                <a href="#past-exams" class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition text-center">
                    <i class="fas fa-file-pdf text-red-600 text-xl mb-2"></i>
                    <p class="text-sm font-medium text-gray-700">Past Exams</p>
                </a>
                <a href="#dashboard" class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition text-center">
                    <i class="fas fa-chart-bar text-purple-600 text-xl mb-2"></i>
                    <p class="text-sm font-medium text-gray-700">Dashboard</p>
                </a>
            </div>

            {{-- Section 1: Dashboard --}}
            <div id="dashboard" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-chart-pie text-indigo-600 mr-2"></i>Dashboard
                </h2>
                <p class="text-gray-600 mb-4">
                    Your dashboard shows statistics specific to your faculty:
                </p>
                <div class="grid md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <i class="fas fa-users text-blue-600 text-2xl mb-2"></i>
                        <p class="font-medium text-gray-800">Lecturers</p>
                        <p class="text-gray-500 text-sm">Total lecturers in your faculty</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <i class="fas fa-book text-green-600 text-2xl mb-2"></i>
                        <p class="font-medium text-gray-800">Courses</p>
                        <p class="text-gray-500 text-sm">Courses registered in your faculty</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-4 text-center">
                        <i class="fas fa-file-alt text-purple-600 text-2xl mb-2"></i>
                        <p class="font-medium text-gray-800">Past Exams</p>
                        <p class="text-gray-500 text-sm">Uploaded past papers available</p>
                    </div>
                </div>
            </div>

            {{-- Section 2: Lecturer Management --}}
            <div id="lecturer-management" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-chalkboard-teacher text-blue-600 mr-2"></i>Lecturer Management
                </h2>

                {{-- Add Lecturer --}}
                <div class="border-l-4 border-blue-500 pl-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Registering a New Lecturer</h3>
                    <ol class="list-decimal list-inside text-gray-600 text-sm space-y-2">
                        <li>Click <strong>"Add Lecturer"</strong> in the sidebar.</li>
                        <li>Fill in the lecturer's details: first name, last name, and email.</li>
                        <li>Set a password (minimum 6 characters). Share this with the lecturer securely.</li>
                        <li>Select the <strong>faculties</strong> the lecturer belongs to (can be multiple).</li>
                        <li>Select the <strong>courses</strong> the lecturer will be responsible for.
                            <ul class="list-disc list-inside ml-4 mt-1">
                                <li>Only courses from your faculty are shown.</li>
                                <li>A lecturer can be assigned to multiple courses.</li>
                            </ul>
                        </li>
                        <li>Click <strong>"Register"</strong> to create the lecturer account.</li>
                    </ol>
                </div>

                {{-- Lecturer List --}}
                <div class="border-l-4 border-green-500 pl-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Managing Existing Lecturers</h3>
                    <ul class="list-disc list-inside text-gray-600 text-sm space-y-2">
                        <li>Click <strong>"Lecturer List"</strong> to view all lecturers in your faculty.</li>
                        <li>Each entry shows the lecturer's name, email, and assigned courses.</li>
                        <li><strong>Edit</strong>: Change a lecturer's details, faculties, or course assignments.
                            <ul class="list-disc list-inside ml-4 mt-1">
                                <li>When editing courses, only your faculty's courses are affected.</li>
                                <li>Courses from other faculties remain unchanged.</li>
                            </ul>
                        </li>
                        <li><strong>Delete</strong>: Permanently remove a lecturer from the system.</li>
                    </ul>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-3">
                        <p class="text-blue-800 text-sm"><i class="fas fa-info-circle mr-1"></i> <strong>Note:</strong> You only see lecturers who have your faculty in their faculties list. A lecturer shared across multiple faculties will appear to each faculty's admin.</p>
                    </div>
                </div>
            </div>

            {{-- Section 3: Course Management --}}
            <div id="course-management" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-book-open text-green-600 mr-2"></i>Course Management
                </h2>

                {{-- Add Course --}}
                <div class="border-l-4 border-green-500 pl-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Adding a New Course</h3>
                    <ol class="list-decimal list-inside text-gray-600 text-sm space-y-2">
                        <li>Click <strong>"Add Courses"</strong> in the sidebar.</li>
                        <li>Enter the course details:
                            <ul class="list-disc list-inside ml-4 mt-1">
                                <li><strong>Course Name</strong> &mdash; full name of the course unit.</li>
                                <li><strong>Course Code</strong> &mdash; the official course code.</li>
                                <li><strong>Faculty</strong> &mdash; automatically set to your faculty.</li>
                            </ul>
                        </li>
                        <li>Submit to register the course. It will now appear in the course list and be available for lecturer assignment.</li>
                    </ol>
                </div>

                {{-- Course List --}}
                <div class="border-l-4 border-orange-500 pl-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Managing Courses</h3>
                    <ul class="list-disc list-inside text-gray-600 text-sm space-y-2">
                        <li>Click <strong>"Courses List"</strong> to see all courses in your faculty.</li>
                        <li><strong>Edit</strong>: Modify the course name, code, or other details.</li>
                        <li><strong>Delete</strong>: Remove a course from the system.</li>
                    </ul>
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mt-3">
                        <p class="text-yellow-800 text-sm"><i class="fas fa-exclamation-triangle mr-1"></i> <strong>Warning:</strong> Deleting a course does not automatically remove exam questions associated with it. Coordinate with lecturers before deleting courses.</p>
                    </div>
                </div>
            </div>

            {{-- Section 4: Past Exam Papers --}}
            <div id="past-exams" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-file-pdf text-red-600 mr-2"></i>Past Exam Papers
                </h2>
                <p class="text-gray-600 mb-4">
                    Upload historical exam papers so students and staff can access them for reference and revision.
                </p>

                {{-- Upload --}}
                <div class="border-l-4 border-red-500 pl-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Uploading a Past Exam</h3>
                    <ol class="list-decimal list-inside text-gray-600 text-sm space-y-2">
                        <li>Click <strong>"Add Past Exams"</strong> in the sidebar.</li>
                        <li>Fill in the exam details:
                            <ul class="list-disc list-inside ml-4 mt-1">
                                <li><strong>Course Unit</strong> &mdash; the course the exam belongs to.</li>
                                <li><strong>Year</strong> &mdash; the academic year (e.g., 2024).</li>
                                <li><strong>Program</strong> &mdash; the degree program (e.g., BIT, BSCS, MIT).</li>
                                <li><strong>Exam Period</strong> &mdash; April, August, or December.</li>
                            </ul>
                        </li>
                        <li>Upload the PDF file (maximum <strong>10MB</strong>).</li>
                        <li>Click <strong>"Upload"</strong>. The file is stored securely in Firebase Storage.</li>
                    </ol>
                </div>

                {{-- View/Delete --}}
                <div class="border-l-4 border-purple-500 pl-4 mb-6">
                    <h3 class="font-semibold text-gray-800 mb-2">Managing Past Exams</h3>
                    <ul class="list-disc list-inside text-gray-600 text-sm space-y-2">
                        <li>Click <strong>"Review Past Exams"</strong> to view all uploaded past papers for your faculty.</li>
                        <li>Papers are organized by year and exam period (April, August, December).</li>
                        <li>Click <strong>Delete</strong> to remove a past exam. This also removes the file from storage.</li>
                    </ul>
                </div>

                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h3 class="font-medium text-green-800 mb-1"><i class="fas fa-globe mr-1"></i> Public Access</h3>
                    <p class="text-gray-600 text-sm">Once uploaded, past exams are publicly accessible to students through the main website under each faculty's page. Students can browse by program, course unit, and year.</p>
                </div>
            </div>

            {{-- Support --}}
            <div class="bg-gradient-to-r from-blue-700 to-blue-900 rounded-xl p-6 text-white mb-8">
                <h2 class="text-xl font-semibold mb-2"><i class="fas fa-headset mr-2"></i>Need Help?</h2>
                <p class="text-blue-100 text-sm">
                    If you encounter any issues, contact your Super Administrator or the system development team.
                </p>
            </div>

        </div>
    </div>

</body>
</html>
