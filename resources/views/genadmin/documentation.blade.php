<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Documentation | General Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-50">

    @include('partials.gen-navbar')

    <div class="p-4 sm:ml-64 mt-24">
        <div class="max-w-5xl mx-auto">

            {{-- Header --}}
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-amber-700"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">General Admin Documentation</h1>
                </div>
                <p class="text-gray-600">Complete guide to generating randomized exams from the question bank.</p>
            </div>

            {{-- Role Overview --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-3">
                    <i class="fas fa-magic text-amber-600 mr-2"></i>Your Role: General Administrator (GenAdmin)
                </h2>
                <p class="text-gray-600 leading-relaxed">
                    As a <strong>General Administrator</strong>, your primary responsibility is to generate the final randomized 
                    exam papers from the approved question bank. Once a Dean approves an exam's question set, you select the course 
                    and the system automatically picks the required number of questions randomly from each section, ensuring every 
                    generated exam is unique.
                </p>
            </div>

            {{-- Workflow Overview --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-project-diagram text-blue-600 mr-2"></i>Exam Generation Workflow
                </h2>

                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1 bg-gray-50 rounded-lg p-4 text-center border-2 border-gray-200">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center mx-auto mb-2 text-white font-bold">1</div>
                        <p class="font-medium text-gray-700 text-sm">Lecturer uploads questions</p>
                        <p class="text-gray-400 text-xs mt-1">Status: Pending Review</p>
                    </div>
                    <div class="hidden md:flex items-center"><i class="fas fa-arrow-right text-gray-300"></i></div>
                    <div class="flex-1 bg-gray-50 rounded-lg p-4 text-center border-2 border-gray-200">
                        <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center mx-auto mb-2 text-white font-bold">2</div>
                        <p class="font-medium text-gray-700 text-sm">Dean reviews & approves</p>
                        <p class="text-gray-400 text-xs mt-1">Status: Approved</p>
                    </div>
                    <div class="hidden md:flex items-center"><i class="fas fa-arrow-right text-gray-300"></i></div>
                    <div class="flex-1 bg-amber-50 rounded-lg p-4 text-center border-2 border-amber-400">
                        <div class="w-10 h-10 bg-amber-500 rounded-full flex items-center justify-center mx-auto mb-2 text-white font-bold">3</div>
                        <p class="font-medium text-amber-800 text-sm">You generate the exam</p>
                        <p class="text-amber-600 text-xs mt-1">Your step!</p>
                    </div>
                    <div class="hidden md:flex items-center"><i class="fas fa-arrow-right text-gray-300"></i></div>
                    <div class="flex-1 bg-green-50 rounded-lg p-4 text-center border-2 border-green-200">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center mx-auto mb-2 text-white font-bold">4</div>
                        <p class="font-medium text-green-700 text-sm">Download final PDF</p>
                        <p class="text-green-500 text-xs mt-1">Ready for printing</p>
                    </div>
                </div>
            </div>

            {{-- How to Generate --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-cogs text-purple-600 mr-2"></i>How to Generate an Exam
                </h2>

                <div class="space-y-4">
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">1</div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Navigate to AI Exam Generator</h3>
                            <p class="text-gray-600 text-sm">Click <strong>"AI Exam Generator"</strong> in the sidebar. This shows all courses across the faculties you manage.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">2</div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Select a Course</h3>
                            <p class="text-gray-600 text-sm">Choose the course unit you want to generate an exam for. The system will fetch all approved exam questions for that course.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-purple-600 rounded-full flex items-center justify-center text-white font-bold text-sm">3</div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Review the Generated Exam</h3>
                            <p class="text-gray-600 text-sm">The system randomly selects questions from each section based on faculty rules (see table below). Review the selected questions on screen.</p>
                        </div>
                    </div>

                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm">4</div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Download the PDF</h3>
                            <p class="text-gray-600 text-sm">Click <strong>"Download Exam"</strong> to generate a professionally formatted PDF with the university header, exam instructions, and all selected questions. The PDF is ready for printing.</p>
                        </div>
                    </div>
                </div>

                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 mt-4">
                    <p class="text-amber-800 text-sm"><i class="fas fa-lightbulb mr-1"></i> <strong>Tip:</strong> Each time you generate an exam for the same course, the questions are randomly shuffled. You can generate multiple versions of the same exam by repeating the process.</p>
                </div>
            </div>

            {{-- Question Selection Rules --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-random text-orange-600 mr-2"></i>Random Selection Rules per Faculty
                </h2>
                <p class="text-gray-600 mb-4">
                    The number of questions selected for the final exam varies by faculty. The system automatically applies these rules:
                </p>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3">Faculty</th>
                                <th class="px-4 py-3">Section A</th>
                                <th class="px-4 py-3">Section B</th>
                                <th class="px-4 py-3">Section C</th>
                                <th class="px-4 py-3">Total Questions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr>
                                <td class="px-4 py-3 font-medium">FST (Science & Technology)</td>
                                <td class="px-4 py-3">1</td>
                                <td class="px-4 py-3">6</td>
                                <td class="px-4 py-3 text-gray-400">N/A</td>
                                <td class="px-4 py-3 font-semibold">7</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium">FBM (Business & Management)</td>
                                <td class="px-4 py-3">1</td>
                                <td class="px-4 py-3">6</td>
                                <td class="px-4 py-3 text-gray-400">N/A</td>
                                <td class="px-4 py-3 font-semibold">7</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium">FOE (Engineering)</td>
                                <td class="px-4 py-3">3</td>
                                <td class="px-4 py-3">3</td>
                                <td class="px-4 py-3 text-gray-400">N/A</td>
                                <td class="px-4 py-3 font-semibold">6</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium">HEC (Higher Education Certificate)</td>
                                <td class="px-4 py-3">10</td>
                                <td class="px-4 py-3">6</td>
                                <td class="px-4 py-3 text-gray-400">N/A</td>
                                <td class="px-4 py-3 font-semibold">16</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium">FOL (Law)</td>
                                <td class="px-4 py-3">1</td>
                                <td class="px-4 py-3">2</td>
                                <td class="px-4 py-3">4</td>
                                <td class="px-4 py-3 font-semibold">7</td>
                            </tr>
                            <tr class="bg-gray-50">
                                <td class="px-4 py-3 font-medium text-gray-500">Other / Default</td>
                                <td class="px-4 py-3">4</td>
                                <td class="px-4 py-3">6</td>
                                <td class="px-4 py-3 text-gray-400">N/A</td>
                                <td class="px-4 py-3 font-semibold">10</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Dashboard --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-chart-pie text-indigo-600 mr-2"></i>Dashboard
                </h2>
                <p class="text-gray-600 text-sm">
                    Your dashboard displays summary counts for your faculty: total lecturers, past exam papers uploaded, and courses registered. 
                    Use this as a quick reference to understand the current state of exam preparation.
                </p>
            </div>

            {{-- Support --}}
            <div class="bg-gradient-to-r from-amber-600 to-amber-800 rounded-xl p-6 text-white mb-8">
                <h2 class="text-xl font-semibold mb-2"><i class="fas fa-headset mr-2"></i>Need Help?</h2>
                <p class="text-amber-100 text-sm">
                    If an exam cannot be generated (e.g., not enough questions), ensure the exam has been approved by the Dean and the lecturer has uploaded enough questions. Contact your Super Administrator for system issues.
                </p>
            </div>

        </div>
    </div>

</body>
</html>
