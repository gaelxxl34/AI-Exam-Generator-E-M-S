<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Documentation | Dean</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-50">

    @include('partials.dean-navbar')

    <div class="p-4 sm:ml-64 mt-24">
        <div class="max-w-5xl mx-auto">

            {{-- Header --}}
            <div class="mb-8">
                <div class="flex items-center gap-3 mb-2">
                    <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-book text-emerald-700"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900">Dean Documentation</h1>
                </div>
                <p class="text-gray-600">Complete guide to exam moderation, review workflows, and faculty activity monitoring.</p>
            </div>

            {{-- Role Overview --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-3">
                    <i class="fas fa-gavel text-emerald-600 mr-2"></i>Your Role: Dean
                </h2>
                <p class="text-gray-600 leading-relaxed">
                    As a <strong>Dean</strong>, you are the quality gatekeeper for exam papers in your faculty.
                    You review all exam submissions from lecturers, ensuring they meet the required standards before 
                    approval. You can edit questions, add comments, approve or decline papers, and monitor all faculty activity.
                </p>
            </div>

            {{-- Quick Navigation --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                <a href="#moderation" class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition text-center">
                    <i class="fas fa-clipboard-check text-emerald-600 text-xl mb-2"></i>
                    <p class="text-sm font-medium text-gray-700">Moderation</p>
                </a>
                <a href="#review-process" class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition text-center">
                    <i class="fas fa-search text-blue-600 text-xl mb-2"></i>
                    <p class="text-sm font-medium text-gray-700">Review Process</p>
                </a>
                <a href="#question-requirements" class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition text-center">
                    <i class="fas fa-list-ol text-orange-600 text-xl mb-2"></i>
                    <p class="text-sm font-medium text-gray-700">Requirements</p>
                </a>
                <a href="#activity-monitoring" class="bg-white rounded-lg border border-gray-200 p-4 hover:shadow-md transition text-center">
                    <i class="fas fa-chart-line text-purple-600 text-xl mb-2"></i>
                    <p class="text-sm font-medium text-gray-700">Activity</p>
                </a>
            </div>

            {{-- Section 1: Dashboard --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-chart-pie text-indigo-600 mr-2"></i>Dashboard
                </h2>
                <p class="text-gray-600 mb-4">
                    Your dashboard provides an overview of exam activity in your faculty:
                </p>
                <ul class="list-disc list-inside text-gray-600 text-sm space-y-2">
                    <li>Total exams submitted, pending review, approved, and declined.</li>
                    <li>Recent activity summary.</li>
                    <li>Quick access to pending exams that need your attention.</li>
                </ul>
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mt-4">
                    <p class="text-blue-800 text-sm"><i class="fas fa-sync-alt mr-1"></i> <strong>Refresh Cache:</strong> If the dashboard data seems stale, use the refresh button to clear the cache and reload fresh data from the database.</p>
                </div>
            </div>

            {{-- Section 2: Exam Moderation --}}
            <div id="moderation" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-clipboard-check text-emerald-600 mr-2"></i>Exam Moderation
                </h2>
                <p class="text-gray-600 mb-4">
                    The moderation page is your primary workspace. Here you see all exam papers grouped by status.
                </p>

                <div class="grid md:grid-cols-3 gap-4 mb-4">
                    <div class="bg-yellow-50 rounded-lg p-4 text-center">
                        <i class="fas fa-clock text-yellow-600 text-2xl mb-2"></i>
                        <p class="font-medium text-gray-800">Pending</p>
                        <p class="text-gray-500 text-sm">Awaiting your review</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <i class="fas fa-check-circle text-green-600 text-2xl mb-2"></i>
                        <p class="font-medium text-gray-800">Approved</p>
                        <p class="text-gray-500 text-sm">Ready for exam generation</p>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4 text-center">
                        <i class="fas fa-times-circle text-red-600 text-2xl mb-2"></i>
                        <p class="font-medium text-gray-800">Declined</p>
                        <p class="text-gray-500 text-sm">Returned to lecturer</p>
                    </div>
                </div>

                <p class="text-gray-600 text-sm">Exams are loaded with pagination. Use the status tabs to filter between pending, approved, and declined papers.</p>
            </div>

            {{-- Section 3: Review Process --}}
            <div id="review-process" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-search text-blue-600 mr-2"></i>Reviewing an Exam
                </h2>
                <p class="text-gray-600 mb-4">
                    Click on any exam to open the detailed review page. Here's your workflow:
                </p>

                <div class="space-y-4">
                    {{-- Step 1 --}}
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">1</div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Review All Questions</h3>
                            <p class="text-gray-600 text-sm">Read through each section's questions. Questions are displayed with their content, any images, and section instructions.</p>
                        </div>
                    </div>

                    {{-- Step 2 --}}
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">2</div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Edit Questions (Optional)</h3>
                            <p class="text-gray-600 text-sm">If a question needs correction, click <strong>Edit</strong>. You must provide:
                            </p>
                            <ul class="list-disc list-inside text-gray-500 text-sm ml-4 mt-1">
                                <li>The corrected question content.</li>
                                <li>A <strong>comment</strong> explaining the change.</li>
                                <li>An <strong>edit reason</strong> for the audit trail.</li>
                            </ul>
                            <p class="text-gray-500 text-sm mt-1">All edits are tracked with the original content preserved.</p>
                        </div>
                    </div>

                    {{-- Step 3 --}}
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold text-sm">3</div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Add Comments</h3>
                            <p class="text-gray-600 text-sm">Leave general feedback for the lecturer using the comment box. All comments are stored and visible to the exam owner.</p>
                        </div>
                    </div>

                    {{-- Step 4 --}}
                    <div class="flex gap-4">
                        <div class="flex-shrink-0 w-8 h-8 bg-green-600 rounded-full flex items-center justify-center text-white font-bold text-sm">4</div>
                        <div>
                            <h3 class="font-semibold text-gray-800">Approve or Decline</h3>
                            <p class="text-gray-600 text-sm">
                                <strong class="text-green-700">Approve:</strong> Marks the exam as ready for final generation by the GenAdmin.<br>
                                <strong class="text-red-700">Decline:</strong> Returns the exam to the lecturer with your comments. The lecturer will see the decline reason and can resubmit.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 4: Question Requirements --}}
            <div id="question-requirements" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-list-ol text-orange-600 mr-2"></i>Minimum Question Requirements
                </h2>
                <p class="text-gray-600 mb-4">
                    Before approving an exam, verify that it meets the minimum question count for your faculty. 
                    The system checks these requirements and shows a validation badge on each exam.
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
                                <td class="px-4 py-3">2 questions</td>
                                <td class="px-4 py-3">12 questions</td>
                                <td class="px-4 py-3 text-gray-400">N/A</td>
                            </tr>
                            <tr>
                                <td class="px-4 py-3 font-medium">FBM (Business & Management)</td>
                                <td class="px-4 py-3">2 questions</td>
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
                                <td class="px-4 py-3">5 questions</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="bg-orange-50 border border-orange-200 rounded-lg p-3 mt-4">
                    <p class="text-orange-800 text-sm"><i class="fas fa-info-circle mr-1"></i> <strong>Important:</strong> These are the <strong>minimum</strong> questions a lecturer must upload. The final exam will be generated by randomly selecting a subset from the question bank (see the table below for selection counts).</p>
                </div>

                <h3 class="font-semibold text-gray-800 mt-6 mb-3">Final Exam Selection (by GenAdmin)</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
                            <tr>
                                <th class="px-4 py-3">Faculty</th>
                                <th class="px-4 py-3">Section A</th>
                                <th class="px-4 py-3">Section B</th>
                                <th class="px-4 py-3">Section C</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <tr><td class="px-4 py-3">FST / FBM</td><td class="px-4 py-3">1 selected</td><td class="px-4 py-3">6 selected</td><td class="px-4 py-3 text-gray-400">N/A</td></tr>
                            <tr><td class="px-4 py-3">FOE</td><td class="px-4 py-3">3 selected</td><td class="px-4 py-3">3 selected</td><td class="px-4 py-3 text-gray-400">N/A</td></tr>
                            <tr><td class="px-4 py-3">HEC</td><td class="px-4 py-3">10 selected</td><td class="px-4 py-3">6 selected</td><td class="px-4 py-3 text-gray-400">N/A</td></tr>
                            <tr><td class="px-4 py-3">FOL</td><td class="px-4 py-3">1 selected</td><td class="px-4 py-3">2 selected</td><td class="px-4 py-3">4 selected</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Section 5: Activity Monitoring --}}
            <div id="activity-monitoring" class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-chart-line text-purple-600 mr-2"></i>Activity Monitoring
                </h2>
                <p class="text-gray-600 mb-4">
                    Track all activity within your faculty through three monitoring views:
                </p>

                <div class="grid md:grid-cols-3 gap-4">
                    <div class="bg-blue-50 rounded-lg p-4">
                        <h3 class="font-semibold text-blue-800 mb-2"><i class="fas fa-download mr-1"></i> Download Activity</h3>
                        <p class="text-gray-600 text-sm">See who downloaded past exams, which files were accessed, and when. Helps track exam paper distribution.</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-4">
                        <h3 class="font-semibold text-green-800 mb-2"><i class="fas fa-file-alt mr-1"></i> Exam Activity</h3>
                        <p class="text-gray-600 text-sm">Track all exam-related actions: uploads, edits, approvals, and declines. See the complete lifecycle of each exam paper.</p>
                    </div>
                    <div class="bg-red-50 rounded-lg p-4">
                        <h3 class="font-semibold text-red-800 mb-2"><i class="fas fa-shield-alt mr-1"></i> Security Activity</h3>
                        <p class="text-gray-600 text-sm">Monitor login attempts, failed logins, and other security events within your faculty.</p>
                    </div>
                </div>
            </div>

            {{-- Export Report --}}
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">
                    <i class="fas fa-file-export text-teal-600 mr-2"></i>Export Dashboard Report
                </h2>
                <p class="text-gray-600 text-sm">
                    Generate a PDF report of your dashboard statistics for record-keeping or meetings. 
                    Click <strong>"Export Report"</strong> from your dashboard to download a formatted PDF with all current metrics.
                </p>
            </div>

            {{-- Support --}}
            <div class="bg-gradient-to-r from-emerald-700 to-emerald-900 rounded-xl p-6 text-white mb-8">
                <h2 class="text-xl font-semibold mb-2"><i class="fas fa-headset mr-2"></i>Need Help?</h2>
                <p class="text-emerald-100 text-sm">
                    If you encounter any issues with the moderation workflow, contact your Super Administrator or the system development team.
                </p>
            </div>

        </div>
    </div>

</body>
</html>
