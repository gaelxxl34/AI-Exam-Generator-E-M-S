<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Documentation | Lecturer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-50">

    @include('partials.lecturer-navbar')

    <div class="p-4 sm:ml-64 mt-24">
        <div class="max-w-5xl mx-auto">

            {{-- Header --}}
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-green-700"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">Lecturer Documentation</h1>
                </div>
                <p class="text-gray-600">Complete guide to creating, managing, and submitting exam questions.</p>
            </div>

            {{-- Role Overview --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-3">
                    <i class="fas fa-chalkboard-teacher text-green-600 mr-2"></i>Your Role: Lecturer
                </h2>
                <p class="text-gray-600 leading-relaxed">
                    As a <strong>Lecturer</strong>, you are responsible for creating and uploading examination questions for
                    your assigned courses. You write questions for each exam section, attach a marking guide, and submit
                    the exam for the Dean's review. Once approved, your questions enter the randomized exam generation pool.
                </p>
            </div>

            {{-- Quick Navigation --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <a href="#upload" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition">
                            <i class="fas fa-upload text-blue-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Upload Exam</h3>
                            <p class="text-xs text-gray-500">Create a new exam</p>
                        </div>
                    </div>
                </a>
                <a href="#manage" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition">
                            <i class="fas fa-edit text-purple-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Manage Questions</h3>
                            <p class="text-xs text-gray-500">Edit, add & delete</p>
                        </div>
                    </div>
                </a>
                <a href="#requirements" class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:shadow-md transition group">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-200 transition">
                            <i class="fas fa-list-check text-orange-600"></i>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Requirements</h3>
                            <p class="text-xs text-gray-500">Min. questions per faculty</p>
                        </div>
                    </div>
                </a>
            </div>

            {{-- Exam Lifecycle --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-route text-blue-600 mr-2"></i>Exam Lifecycle
                </h2>
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 bg-green-50 rounded-lg p-4 text-center border-2 border-green-400">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-2 text-white font-bold">1</div>
                        <p class="font-medium text-green-800 text-sm">You upload questions</p>
                        <p class="text-green-600 text-xs mt-1">Your step!</p>
                    </div>
                    <div class="hidden md:flex items-center"><i class="fas fa-arrow-right text-gray-300"></i></div>
                    <div class="flex-1 bg-gray-50 rounded-lg p-4 text-center border-2 border-gray-200">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center mx-auto mb-2 text-white font-bold">2</div>
                        <p class="font-medium text-gray-700 text-sm">Dean reviews</p>
                        <p class="text-gray-400 text-xs mt-1">Pending Review</p>
                    </div>
                    <div class="hidden md:flex items-center"><i class="fas fa-arrow-right text-gray-300"></i></div>
                    <div class="flex-1 bg-gray-50 rounded-lg p-4 text-center border-2 border-gray-200">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center mx-auto mb-2 text-white font-bold">3</div>
                        <p class="font-medium text-gray-700 text-sm">Approved / Rejected</p>
                        <p class="text-gray-400 text-xs mt-1">Dean decision</p>
                    </div>
                    <div class="hidden md:flex items-center"><i class="fas fa-arrow-right text-gray-300"></i></div>
                    <div class="flex-1 bg-gray-50 rounded-lg p-4 text-center border-2 border-gray-200">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center mx-auto mb-2 text-white font-bold">4</div>
                        <p class="font-medium text-gray-700 text-sm">GenAdmin generates exam</p>
                        <p class="text-gray-400 text-xs mt-1">Random selection</p>
                    </div>
                </div>
            </div>

            {{-- Upload New Exam --}}
            <div id="upload" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-upload text-blue-600 mr-2"></i>Uploading a New Exam
                </h2>

                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">1</div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Click "Upload New Exam" in the sidebar</h3>
                            <p class="text-gray-600 text-sm">This opens the upload form showing only the courses assigned to you by your administrator.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">2</div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Select your course</h3>
                            <p class="text-gray-600 text-sm">Choose the course unit from the dropdown. The course code and faculty will be automatically populated.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">3</div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Write Section A questions</h3>
                            <p class="text-gray-600 text-sm">Use the rich text editor to add each question. You can include images, tables, and formatted text. Add section instructions (e.g., "Answer ALL questions").</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">4</div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Write Section B questions</h3>
                            <p class="text-gray-600 text-sm">Add your Section B questions with their instructions. For FOL (Law) faculty, a Section C will also be available.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm">5</div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Submit the exam</h3>
                            <p class="text-gray-600 text-sm">Click <strong>"Submit Exam"</strong>. The status will be set to <strong>"Pending Review"</strong> and your Dean will be notified.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-lg p-3 mt-4">
                    <p class="text-red-800 text-sm"><i class="fas fa-exclamation-triangle mr-1"></i> <strong>Important:</strong> You can only upload one exam per course. If an exam already exists for a course, you'll need to edit the existing one instead.</p>
                </div>
            </div>

            {{-- Minimum Question Requirements --}}
            <div id="requirements" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-list-ol text-orange-600 mr-2"></i>Minimum Question Requirements
                </h2>
                <p class="text-gray-600 mb-4">
                    Each faculty has specific minimum question counts that must be met before your exam can be submitted for review. 
                    You must upload <strong>at least</strong> this many questions per section:
                </p>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3">Faculty</th>
                                <th class="px-4 py-3">Section A (min)</th>
                                <th class="px-4 py-3">Section B (min)</th>
                                <th class="px-4 py-3">Section C (min)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-3 font-medium">FST (Science & Technology)</td>
                                <td class="px-4 py-3">2 case studies</td>
                                <td class="px-4 py-3">12 questions</td>
                                <td class="px-4 py-3 text-gray-400">N/A</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium">FBM (Business & Management)</td>
                                <td class="px-4 py-3">2 case studies</td>
                                <td class="px-4 py-3">12 questions</td>
                                <td class="px-4 py-3 text-gray-400">N/A</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium">FOE (Engineering)</td>
                                <td class="px-4 py-3">4 questions</td>
                                <td class="px-4 py-3">4 questions</td>
                                <td class="px-4 py-3 text-gray-400">N/A</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium">HEC (Higher Education Certificate)</td>
                                <td class="px-4 py-3">20 questions</td>
                                <td class="px-4 py-3">10 questions</td>
                                <td class="px-4 py-3 text-gray-400">N/A</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium">FOL (Law)</td>
                                <td class="px-4 py-3">2 questions</td>
                                <td class="px-4 py-3">4 questions</td>
                                <td class="px-4 py-3">5 essay questions</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-4">
                    <p class="text-blue-800 text-sm"><i class="fas fa-info-circle mr-1"></i> <strong>Why more than what's on the final exam?</strong> You upload a pool of questions. The system randomly selects a subset for the final exam. Having more questions ensures each generated exam is unique.</p>
                </div>
            </div>

            {{-- Managing Existing Questions --}}
            <div id="manage" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-edit text-purple-600 mr-2"></i>Managing Existing Questions
                </h2>
                <p class="text-gray-600 mb-4">
                    After uploading an exam, click on any course from <strong>"My Exams"</strong> on your dashboard to view and manage its questions.
                </p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="border-l-4 border-blue-500 pl-4 py-2">
                        <h3 class="font-semibold text-gray-800"><i class="fas fa-plus-circle text-blue-500 mr-1"></i> Add Questions</h3>
                        <p class="text-gray-600 text-sm">Add new questions to any section using the rich text editor at the bottom of the section. Select the target section and submit.</p>
                    </div>
                    <div class="border-l-4 border-yellow-500 pl-4 py-2">
                        <h3 class="font-semibold text-gray-800"><i class="fas fa-pencil-alt text-yellow-500 mr-1"></i> Edit Questions</h3>
                        <p class="text-gray-600 text-sm">Click the edit icon on any question to modify its content. Your changes are saved directly to the database.</p>
                    </div>
                    <div class="border-l-4 border-red-500 pl-4 py-2">
                        <h3 class="font-semibold text-gray-800"><i class="fas fa-trash text-red-500 mr-1"></i> Delete Questions</h3>
                        <p class="text-gray-600 text-sm">Remove a question by clicking the delete icon. Any images attached to the question will also be removed from storage.</p>
                    </div>
                    <div class="border-l-4 border-green-500 pl-4 py-2">
                        <h3 class="font-semibold text-gray-800"><i class="fas fa-align-left text-green-500 mr-1"></i> Update Instructions</h3>
                        <p class="text-gray-600 text-sm">Edit the instructions for Section A, Section B (and Section C for Law faculty). These appear at the top of each section on the final exam.</p>
                    </div>
                </div>
            </div>

            {{-- Marking Guide --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-file-alt text-teal-600 mr-2"></i>Marking Guide
                </h2>
                <p class="text-gray-600 mb-3">
                    You can attach a marking guide to each exam. This file is stored alongside the exam and can be downloaded later.
                </p>

                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="font-semibold text-gray-800 mb-2">Supported file types:</h3>
                    <div class="flex flex-wrap gap-2">
                        <span class="px-3 py-1 bg-red-100 text-red-700 rounded-full text-xs font-medium"><i class="fas fa-file-pdf mr-1"></i>PDF</span>
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium"><i class="fas fa-file-word mr-1"></i>DOC</span>
                        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-xs font-medium"><i class="fas fa-file-word mr-1"></i>DOCX</span>
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium"><i class="fas fa-file-excel mr-1"></i>XLS</span>
                        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-xs font-medium"><i class="fas fa-file-excel mr-1"></i>XLSX</span>
                    </div>
                    <p class="text-gray-500 text-xs mt-2"><i class="fas fa-weight-hanging mr-1"></i>Maximum file size: 3 MB</p>
                </div>
            </div>

            {{-- Preview & Submit --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-eye text-indigo-600 mr-2"></i>Preview & Submit
                </h2>

                <div class="space-y-3">
                    <div class="border-l-4 border-indigo-500 pl-4 py-2">
                        <h3 class="font-semibold text-gray-800">Preview PDF</h3>
                        <p class="text-gray-600 text-sm">Click <strong>"Preview PDF"</strong> to generate a formatted preview of your exam as it will appear when printed. This opens a PDF in your browser.</p>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <p class="text-yellow-800 text-sm"><i class="fas fa-exclamation-circle mr-1"></i> <strong>Preview Requirement:</strong> You must preview your exam at least <strong>3 times</strong> before it can be submitted for review. This ensures you have thoroughly reviewed all questions.</p>
                    </div>

                    <div class="border-l-4 border-green-500 pl-4 py-2">
                        <h3 class="font-semibold text-gray-800">Submit for Review</h3>
                        <p class="text-gray-600 text-sm">Once all minimum question requirements are met and you have previewed the exam 3 times, the <strong>"Submit for Review"</strong> button becomes active. Your exam status changes to <strong>"Pending Review"</strong> and it goes to the Dean for approval.</p>
                    </div>
                </div>
            </div>

            {{-- Exam Statuses --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-tags text-gray-600 mr-2"></i>Understanding Exam Statuses
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-yellow-50 rounded-lg p-4 text-center">
                        <span class="inline-block px-3 py-1 bg-yellow-200 text-yellow-800 rounded-full text-xs font-bold mb-2">Pending Review</span>
                        <p class="text-gray-600 text-sm">Your exam has been submitted and is waiting for the Dean to review it.</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <span class="inline-block px-3 py-1 bg-green-200 text-green-800 rounded-full text-xs font-bold mb-2">Approved</span>
                        <p class="text-gray-600 text-sm">The Dean has approved your exam. It is now in the generation pool.</p>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4 text-center">
                        <span class="inline-block px-3 py-1 bg-red-200 text-red-800 rounded-full text-xs font-bold mb-2">Rejected</span>
                        <p class="text-gray-600 text-sm">The Dean has rejected your exam with comments. Revise and resubmit.</p>
                    </div>
                </div>
            </div>

            {{-- Support --}}
            <div class="bg-gradient-to-r from-green-600 to-green-800 rounded-xl p-6 text-white mb-8">
                <h2 class="text-xl font-semibold mb-2"><i class="fas fa-headset mr-2"></i>Need Help?</h2>
                <p class="text-green-100 text-sm">
                    If you encounter issues uploading questions, check that your images are not too large and that you meet the minimum question requirements for your faculty. Contact your Faculty Administrator for course assignment issues.
                </p>
            </div>

        </div>
    </div>

</body>
</html>
