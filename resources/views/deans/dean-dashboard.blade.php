<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dean Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    @include('partials.dean-navbar')

    <div class="p-6 sm:ml-64 mt-20">

        {{-- Main Stats Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

            {{-- Pending Exams --}}
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-6 rounded shadow-md">
                <div class="text-4xl font-bold">{{ $pendingExams }}</div>
                <div class="mt-2 text-sm font-semibold">Pending Exams</div>
            </div>

            {{-- Approved Exams --}}
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-6 rounded shadow-md">
                <div class="text-4xl font-bold">{{ $approvedExams }}</div>
                <div class="mt-2 text-sm font-semibold">Approved Exams</div>
            </div>

            {{-- Declined Exams --}}
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-6 rounded shadow-md">
                <div class="text-4xl font-bold">{{ $declinedExams }}</div>
                <div class="mt-2 text-sm font-semibold">Declined Exams</div>
            </div>

            {{-- Faculty Courses --}}
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-6 rounded shadow-md">
                <div class="text-4xl font-bold">{{ count($facultyCourses) }}</div>
                @php
                    $faculties = session('user_faculty');
                    if (!is_array($faculties)) {
                        $faculties = [$faculties];
                    }
                @endphp
                <div class="mt-2 text-sm font-semibold">
                    Courses for Faculty ({{ implode(', ', $faculties) }})
                </div>
            </div>

        </div>

        {{-- Security & Activity Overview Cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mt-6">

            {{-- Today's Activity --}}
            <div class="bg-purple-100 border-l-4 border-purple-500 text-purple-700 p-6 rounded shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold">{{ $activityStats['today'] ?? 0 }}</div>
                        <div class="mt-2 text-sm font-semibold">Actions Today</div>
                    </div>
                    <i class="fas fa-chart-line text-4xl opacity-30"></i>
                </div>
            </div>

            {{-- Downloads This Week --}}
            <div class="bg-indigo-100 border-l-4 border-indigo-500 text-indigo-700 p-6 rounded shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold">{{ $downloadStats['this_week'] ?? 0 }}</div>
                        <div class="mt-2 text-sm font-semibold">Downloads This Week</div>
                    </div>
                    <i class="fas fa-download text-4xl opacity-30"></i>
                </div>
            </div>

            {{-- Successful Logins --}}
            <div class="bg-teal-100 border-l-4 border-teal-500 text-teal-700 p-6 rounded shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold">{{ $activityStats['login_success'] ?? 0 }}</div>
                        <div class="mt-2 text-sm font-semibold">Successful Logins</div>
                    </div>
                    <i class="fas fa-sign-in-alt text-4xl opacity-30"></i>
                </div>
            </div>

            {{-- Failed Login Attempts --}}
            <div class="bg-orange-100 border-l-4 border-orange-500 text-orange-700 p-6 rounded shadow-md">
                <div class="flex items-center justify-between">
                    <div>
                        <div class="text-3xl font-bold">{{ $activityStats['login_failed'] ?? 0 }}</div>
                        <div class="mt-2 text-sm font-semibold">Failed Login Attempts</div>
                    </div>
                    <i class="fas fa-exclamation-triangle text-4xl opacity-30"></i>
                </div>
            </div>

        </div>

        <!-- Hidden iframe for download -->
        <iframe id="downloadFrame" name="downloadFrame" style="display:none;"></iframe>

        <!-- Action Buttons Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
            <!-- Download button form targets the iframe -->
            <form id="reportForm" action="{{ route('dashboard.export-report') }}" method="GET" target="downloadFrame">
                <button type="submit" id="downloadBtn"
                    class="w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow transition duration-200 flex items-center justify-center gap-2">
                    <span id="btnText">📄 Download Report</span>
                    <span id="btnSpinner"
                        class="hidden animate-spin border-2 border-white border-t-transparent rounded-full w-5 h-5"></span>
                </button>
            </form>

            <!-- Refresh Cache Button -->
            <button onclick="refreshDashboard()" id="refreshBtn"
                class="w-full text-center bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-4 rounded-lg shadow transition duration-200 flex items-center justify-center gap-2">
                <i class="fas fa-sync-alt"></i>
                <span>Refresh Dashboard Data</span>
            </button>
        </div>

        {{-- Security Monitoring Section --}}
        <div class="mt-10">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                <i class="fas fa-shield-alt text-blue-600"></i> Security & Activity Monitoring
            </h2>

            {{-- Tabs for different activity views --}}
            <div class="mb-4 border-b border-gray-200">
                <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="activityTabs" role="tablist">
                    <li class="mr-2" role="presentation">
                        <button class="inline-block p-4 border-b-2 border-blue-600 text-blue-600 rounded-t-lg"
                            id="downloads-tab" data-tab="downloads" type="button" role="tab">
                            <i class="fas fa-download mr-2"></i>Downloads & Previews
                        </button>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button
                            class="inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 rounded-t-lg"
                            id="exam-activity-tab" data-tab="exam-activity" type="button" role="tab">
                            <i class="fas fa-file-alt mr-2"></i>Exam Activity
                        </button>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button
                            class="inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 rounded-t-lg"
                            id="security-tab" data-tab="security" type="button" role="tab">
                            <i class="fas fa-lock mr-2"></i>Security Events
                        </button>
                    </li>
                    <li class="mr-2" role="presentation">
                        <button
                            class="inline-block p-4 border-b-2 border-transparent hover:text-gray-600 hover:border-gray-300 rounded-t-lg"
                            id="users-tab" data-tab="users" type="button" role="tab">
                            <i class="fas fa-users mr-2"></i>Active Users
                        </button>
                    </li>
                </ul>
            </div>

            {{-- Tab Content --}}
            <div id="activityTabContent">
                {{-- Downloads Tab --}}
                <div class="bg-white p-6 rounded shadow" id="downloads-content">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Downloads & Previews in Your Faculty</h3>
                        <button onclick="loadDownloads()" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                    <div id="downloads-list" class="overflow-x-auto">
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-spinner fa-spin text-2xl"></i>
                            <p class="mt-2">Loading download activity...</p>
                        </div>
                    </div>
                </div>

                {{-- Exam Activity Tab --}}
                <div class="bg-white p-6 rounded shadow hidden" id="exam-activity-content">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Recent Exam Activity</h3>
                        <button onclick="loadExamActivity()" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                    <div id="exam-activity-list" class="overflow-x-auto">
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-spinner fa-spin text-2xl"></i>
                            <p class="mt-2">Loading exam activity...</p>
                        </div>
                    </div>
                </div>

                {{-- Security Tab --}}
                <div class="bg-white p-6 rounded shadow hidden" id="security-content">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800">Security Events (Logins, Unauthorized Access)
                        </h3>
                        <button onclick="loadSecurityLogs()" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-sync-alt"></i> Refresh
                        </button>
                    </div>
                    <div id="security-list" class="overflow-x-auto">
                        <div class="text-center py-8 text-gray-500">
                            <i class="fas fa-spinner fa-spin text-2xl"></i>
                            <p class="mt-2">Loading security events...</p>
                        </div>
                    </div>
                </div>

                {{-- Active Users Tab --}}
                <div class="bg-white p-6 rounded shadow hidden" id="users-content">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">Most Active Users (Last 30 Days)</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border text-sm">
                            <thead class="bg-gray-100 text-left text-gray-700 font-semibold">
                                <tr>
                                    <th class="px-4 py-2 border-b">User</th>
                                    <th class="px-4 py-2 border-b">Role</th>
                                    <th class="px-4 py-2 border-b">Total Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(isset($activityStats['active_users']) && count($activityStats['active_users']) > 0)
                                    @foreach ($activityStats['active_users'] as $email => $user)
                                                            <tr class="border-b hover:bg-gray-50">
                                                                <td class="px-4 py-2">
                                                                    <div class="font-medium">{{ $user['name'] }}</div>
                                                                    <div class="text-xs text-gray-500">{{ $email }}</div>
                                                                </td>
                                                                <td class="px-4 py-2">
                                                                    <span
                                                                        class="px-2 py-1 rounded text-xs font-medium
                                                                                                        {{ $user['role'] === 'lecturer' ? 'bg-blue-100 text-blue-800' :
                                        ($user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' :
                                            ($user['role'] === 'dean' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800')) }}">
                                                                        {{ ucfirst($user['role']) }}
                                                                    </span>
                                                                </td>
                                                                <td class="px-4 py-2 font-semibold">{{ $user['actions'] }}</td>
                                                            </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="3" class="px-4 py-4 text-center text-gray-500">No activity data
                                            available.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>



        {{-- Additional Insights Section --}}
        <div class="mt-10 space-y-6">

            {{-- Top Incomplete Exams --}}
            <div class="bg-white p-6 rounded shadow">
                <h2 class="text-xl font-bold text-gray-800 mb-4">⚠️ Incomplete & Missing Exams</h2>

                @if(count($incompleteExams))
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border text-sm">
                            <thead class="bg-gray-100 text-left text-gray-700 font-semibold">
                                <tr>
                                    <th class="px-4 py-2 border-b">Course Unit</th>
                                    <th class="px-4 py-2 border-b">Lecturer</th>
                                    <th class="px-4 py-2 border-b">Email</th>
                                    <th class="px-4 py-2 border-b">Status</th>
                                    <th class="px-4 py-2 border-b">Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($incompleteExams as $exam)
                                    <tr class="border-b hover:bg-gray-50">
                                        <td class="px-4 py-2">{{ $exam['courseUnit'] ?? 'N/A' }}</td>
                                        <td class="px-4 py-2">{{ $exam['lecturerName'] ?? 'Unknown' }}</td>
                                        <td class="px-4 py-2">{{ $exam['lecturerEmail'] ?? 'N/A' }}</td>
                                        <td class="px-4 py-2">
                                            <span
                                                class="px-2 py-1 rounded text-xs font-medium
                                                                    {{ $exam['status'] === 'Not Submitted' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $exam['status'] }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-2 text-xs text-gray-600">{{ $exam['notes'] ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-600">✅ All exams are submitted and complete.</p>
                @endif
            </div>

        </div>

    </div>


    <script>
        document.getElementById('reportForm').addEventListener('submit', function () {
            const btn = document.getElementById('downloadBtn');
            document.getElementById('btnText').textContent = 'Preparing...';
            document.getElementById('btnSpinner').classList.remove('hidden');
            btn.disabled = true;

            // Reset the button after 5 seconds (adjust as needed)
            setTimeout(() => {
                document.getElementById('btnText').textContent = '📄 Download Report';
                document.getElementById('btnSpinner').classList.add('hidden');
                btn.disabled = false;
            }, 5000);
        });

        // Tab switching functionality
        document.querySelectorAll('[data-tab]').forEach(tab => {
            tab.addEventListener('click', function () {
                const tabName = this.dataset.tab;

                // Update tab styles
                document.querySelectorAll('[data-tab]').forEach(t => {
                    t.classList.remove('border-blue-600', 'text-blue-600');
                    t.classList.add('border-transparent');
                });
                this.classList.add('border-blue-600', 'text-blue-600');
                this.classList.remove('border-transparent');

                // Show/hide content
                document.querySelectorAll('[id$="-content"]').forEach(content => {
                    if (content.id.startsWith(tabName) || content.id === tabName + '-content') {
                        content.classList.remove('hidden');
                    } else if (content.id.endsWith('-content') && !content.id.includes('Tab')) {
                        content.classList.add('hidden');
                    }
                });

                // Load data for the tab
                if (tabName === 'downloads') loadDownloads();
                else if (tabName === 'exam-activity') loadExamActivity();
                else if (tabName === 'security') loadSecurityLogs();
            });
        });

        // Load downloads on page load
        document.addEventListener('DOMContentLoaded', function () {
            loadDownloads();
        });

        function loadDownloads() {
            const container = document.getElementById('downloads-list');
            container.innerHTML = '<div class="text-center py-8 text-gray-500"><i class="fas fa-spinner fa-spin text-2xl"></i><p class="mt-2">Loading...</p></div>';

            fetch('/deans/activity/downloads', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.downloads.length > 0) {
                        let html = `<table class="min-w-full bg-white border text-sm">
                    <thead class="bg-gray-100 text-left text-gray-700 font-semibold">
                        <tr>
                            <th class="px-4 py-2 border-b">User</th>
                            <th class="px-4 py-2 border-b">Action</th>
                            <th class="px-4 py-2 border-b">File/Course</th>
                            <th class="px-4 py-2 border-b">Date & Time</th>
                            <th class="px-4 py-2 border-b">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>`;

                        data.downloads.forEach(d => {
                            const actionBadge = getActionBadge(d.file_type);
                            html += `<tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">
                            <div class="font-medium">${d.user_name || 'Unknown'}</div>
                            <div class="text-xs text-gray-500">${d.user_email || 'N/A'}</div>
                        </td>
                        <td class="px-4 py-2">${actionBadge}</td>
                        <td class="px-4 py-2">${d.course_unit || d.file_name || 'N/A'}</td>
                        <td class="px-4 py-2 text-xs">${d.timestamp_formatted || 'N/A'}</td>
                        <td class="px-4 py-2 text-xs text-gray-500">${d.ip_address || 'N/A'}</td>
                    </tr>`;
                        });

                        html += '</tbody></table>';
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = '<p class="text-center py-8 text-gray-500">No download activity found for your faculty.</p>';
                    }
                })
                .catch(err => {
                    container.innerHTML = '<p class="text-center py-8 text-red-500">Failed to load downloads. Please try again.</p>';
                    console.error(err);
                });
        }

        function loadExamActivity() {
            const container = document.getElementById('exam-activity-list');
            container.innerHTML = '<div class="text-center py-8 text-gray-500"><i class="fas fa-spinner fa-spin text-2xl"></i><p class="mt-2">Loading...</p></div>';

            fetch('/deans/activity/exams', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.activity.length > 0) {
                        let html = `<table class="min-w-full bg-white border text-sm">
                    <thead class="bg-gray-100 text-left text-gray-700 font-semibold">
                        <tr>
                            <th class="px-4 py-2 border-b">User</th>
                            <th class="px-4 py-2 border-b">Action</th>
                            <th class="px-4 py-2 border-b">Course/Exam</th>
                            <th class="px-4 py-2 border-b">Date & Time</th>
                        </tr>
                    </thead>
                    <tbody>`;

                        data.activity.forEach(a => {
                            const actionBadge = getExamActionBadge(a.action);
                            const resourceName = a.resource_name || a.details?.course_unit || 'N/A';
                            html += `<tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">
                            <div class="font-medium">${a.user_name || 'Unknown'}</div>
                            <div class="text-xs text-gray-500">${a.user_email || 'N/A'}</div>
                        </td>
                        <td class="px-4 py-2">${actionBadge}</td>
                        <td class="px-4 py-2">${resourceName}</td>
                        <td class="px-4 py-2 text-xs">${a.timestamp_formatted || 'N/A'}</td>
                    </tr>`;
                        });

                        html += '</tbody></table>';
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = '<p class="text-center py-8 text-gray-500">No exam activity found.</p>';
                    }
                })
                .catch(err => {
                    container.innerHTML = '<p class="text-center py-8 text-red-500">Failed to load exam activity.</p>';
                    console.error(err);
                });
        }

        function loadSecurityLogs() {
            const container = document.getElementById('security-list');
            container.innerHTML = '<div class="text-center py-8 text-gray-500"><i class="fas fa-spinner fa-spin text-2xl"></i><p class="mt-2">Loading...</p></div>';

            fetch('/deans/activity/security', {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.logs.length > 0) {
                        let html = `<table class="min-w-full bg-white border text-sm">
                    <thead class="bg-gray-100 text-left text-gray-700 font-semibold">
                        <tr>
                            <th class="px-4 py-2 border-b">User</th>
                            <th class="px-4 py-2 border-b">Event</th>
                            <th class="px-4 py-2 border-b">Details</th>
                            <th class="px-4 py-2 border-b">Date & Time</th>
                            <th class="px-4 py-2 border-b">IP Address</th>
                        </tr>
                    </thead>
                    <tbody>`;

                        data.logs.forEach(log => {
                            const eventBadge = getSecurityBadge(log.action);
                            const details = log.details?.failure_reason || log.details?.email_attempted || '';
                            html += `<tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">
                            <div class="font-medium">${log.user_name || 'Unknown'}</div>
                            <div class="text-xs text-gray-500">${log.user_email || log.details?.email_attempted || 'N/A'}</div>
                        </td>
                        <td class="px-4 py-2">${eventBadge}</td>
                        <td class="px-4 py-2 text-xs">${details}</td>
                        <td class="px-4 py-2 text-xs">${log.timestamp_formatted || 'N/A'}</td>
                        <td class="px-4 py-2 text-xs text-gray-500">${log.ip_address || 'N/A'}</td>
                    </tr>`;
                        });

                        html += '</tbody></table>';
                        container.innerHTML = html;
                    } else {
                        container.innerHTML = '<p class="text-center py-8 text-gray-500">No security events found.</p>';
                    }
                })
                .catch(err => {
                    container.innerHTML = '<p class="text-center py-8 text-red-500">Failed to load security logs.</p>';
                    console.error(err);
                });
        }

        function getActionBadge(fileType) {
            const badges = {
                'past_exam': '<span class="px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800"><i class="fas fa-download mr-1"></i>Downloaded Past Exam</span>',
                'marking_guide': '<span class="px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800"><i class="fas fa-file-alt mr-1"></i>Downloaded Marking Guide</span>',
                'generated_pdf': '<span class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-file-pdf mr-1"></i>Generated PDF</span>',
                'pdf_preview': '<span class="px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800"><i class="fas fa-eye mr-1"></i>Previewed PDF</span>',
            };
            return badges[fileType] || `<span class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">${fileType}</span>`;
        }

        function getExamActionBadge(action) {
            const badges = {
                'exam_created': '<span class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-plus mr-1"></i>Created</span>',
                'exam_updated': '<span class="px-2 py-1 rounded text-xs font-medium bg-blue-100 text-blue-800"><i class="fas fa-edit mr-1"></i>Updated</span>',
                'exam_approved': '<span class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-check mr-1"></i>Approved</span>',
                'exam_declined': '<span class="px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-times mr-1"></i>Declined</span>',
                'question_added': '<span class="px-2 py-1 rounded text-xs font-medium bg-teal-100 text-teal-800"><i class="fas fa-plus-circle mr-1"></i>Question Added</span>',
                'question_edited': '<span class="px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800"><i class="fas fa-pencil-alt mr-1"></i>Question Edited</span>',
                'question_deleted': '<span class="px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-trash mr-1"></i>Question Deleted</span>',
                'dean_question_edit': '<span class="px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800"><i class="fas fa-user-edit mr-1"></i>Dean Edit</span>',
                'pdf_generated': '<span class="px-2 py-1 rounded text-xs font-medium bg-indigo-100 text-indigo-800"><i class="fas fa-file-pdf mr-1"></i>PDF Generated</span>',
                'marking_guide_downloaded': '<span class="px-2 py-1 rounded text-xs font-medium bg-purple-100 text-purple-800"><i class="fas fa-download mr-1"></i>Guide Downloaded</span>',
            };
            return badges[action] || `<span class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">${action}</span>`;
        }

        function getSecurityBadge(action) {
            const badges = {
                'login_success': '<span class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800"><i class="fas fa-sign-in-alt mr-1"></i>Login Success</span>',
                'login_failed': '<span class="px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-exclamation-circle mr-1"></i>Login Failed</span>',
                'logout': '<span class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800"><i class="fas fa-sign-out-alt mr-1"></i>Logout</span>',
                'unauthorized_access_attempt': '<span class="px-2 py-1 rounded text-xs font-medium bg-red-100 text-red-800"><i class="fas fa-ban mr-1"></i>Unauthorized Access</span>',
                'password_reset_request': '<span class="px-2 py-1 rounded text-xs font-medium bg-yellow-100 text-yellow-800"><i class="fas fa-key mr-1"></i>Password Reset</span>',
            };
            return badges[action] || `<span class="px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800">${action}</span>`;
        }

        function refreshDashboard() {
            const btn = document.getElementById('refreshBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Refreshing...';

            fetch('/deans/dashboard/refresh', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                })
                .catch(err => {
                    console.error(err);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-sync-alt"></i> Refresh Dashboard Data';
                });
        }
    </script>


</body>

</html>