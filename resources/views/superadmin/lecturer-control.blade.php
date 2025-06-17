<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Control - Super Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body class="bg-gray-50">

    @include('partials.super-admin-navbar')

    <div class="p-4 sm:ml-64 mt-20">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold flex items-center">
                            <i class="fas fa-chalkboard-teacher mr-3"></i>Lecturer Control Center
                        </h1>
                        <p class="text-indigo-100 mt-2">Manage all lecturers, their permissions, and course assignments
                        </p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <div class="text-sm text-indigo-100">Total Lecturers</div>
                            <div class="text-2xl font-bold">{{ collect($sortedFaculties)->flatten(1)->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Lecturers</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ collect($sortedFaculties)->flatten(1)->where('status', false)->count() }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-check text-blue-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Disabled</p>
                        <p class="text-2xl font-bold text-gray-900">
                            {{ collect($sortedFaculties)->flatten(1)->where('status', true)->count() }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-times text-red-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-2xl font-bold text-gray-900">{{ collect($sortedFaculties)->flatten(1)->count() }}</p>
                    </div>
                    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-green-600"></i>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Faculties</p>
                        <p class="text-2xl font-bold text-gray-900">{{ count($sortedFaculties) }}</p>
                    </div>
                    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-university text-yellow-600"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Faculty Filter Tabs -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex flex-wrap gap-2 mb-4">
                <button onclick="showAllFaculties()" class="faculty-filter-btn active px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm">
                    All Faculties
                </button>
                @foreach($sortedFaculties as $faculty => $lecturers)
                    <button onclick="showFaculty('{{ $faculty }}')" class="faculty-filter-btn px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-sm">
                        {{ $faculty }} ({{ count($lecturers) }})
                    </button>
                @endforeach
            </div>
            
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Search by name or email..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                    </div>
                </div>
                <div class="flex gap-2">
                    <select id="statusFilter"
                        class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500">
                        <option value="">All Status</option>
                        <option value="active">Active</option>
                        <option value="disabled">Disabled</option>
                    </select>
                    <button onclick="clearFilters()"
                        class="px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all">
                        <i class="fas fa-times mr-1"></i>Clear
                    </button>
                </div>
            </div>
        </div>

        <!-- Lecturers by Faculty -->
        @foreach($sortedFaculties as $faculty => $lecturers)
            <div class="faculty-section mb-8" data-faculty="{{ $faculty }}">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-indigo-50 to-purple-50">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <i class="fas fa-university mr-2 text-indigo-600"></i>
                                {{ $faculty }} ({{ count($lecturers) }} lecturers)
                            </h3>
                            <div class="flex items-center space-x-2">
                                <button onclick="bulkActionFaculty('enable', '{{ $faculty }}')"
                                    class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-all text-sm">
                                    <i class="fas fa-check mr-1"></i>Enable All
                                </button>
                                <button onclick="bulkActionFaculty('disable', '{{ $faculty }}')"
                                    class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-all text-sm">
                                    <i class="fas fa-ban mr-1"></i>Disable All
                                </button>
                                <button onclick="bulkClearCoursesFaculty('{{ $faculty }}')"
                                    class="px-3 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-all text-sm">
                                    <i class="fas fa-eraser mr-1"></i>Clear All Courses
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <input type="checkbox" class="select-all-faculty rounded border-gray-300" data-faculty="{{ $faculty }}">
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Lecturer
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Faculties
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Courses
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Status
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($lecturers as $lecturer)
                                    <tr class="hover:bg-gray-50 transition-all lecturer-row"
                                        data-name="{{ strtolower($lecturer['name']) }}"
                                        data-email="{{ strtolower($lecturer['email']) }}"
                                        data-status="{{ $lecturer['status'] ? 'disabled' : 'active' }}"
                                        data-faculty="{{ $faculty }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <input type="checkbox" class="lecturer-checkbox rounded border-gray-300"
                                                value="{{ $lecturer['id'] }}" data-faculty="{{ $faculty }}">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center mr-3">
                                                    <span class="font-semibold text-indigo-600">{{ strtoupper(substr($lecturer['name'], 0, 2)) }}</span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $lecturer['name'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $lecturer['email'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex flex-wrap gap-1">
                                                @foreach($lecturer['faculties'] as $lecturerFaculty)
                                                    <span class="inline-flex px-2 py-1 text-xs font-semibold bg-blue-100 text-blue-800 rounded-full">
                                                        {{ $lecturerFaculty }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                @if(count($lecturer['courses']) > 0)
                                                    <span class="text-sm text-gray-700 mr-2">{{ count($lecturer['courses']) }} courses</span>
                                                    <div class="relative">
                                                        <button onclick="toggleCourseDropdown('{{ $lecturer['id'] }}')" 
                                                            class="text-blue-600 hover:text-blue-800 transition-all">
                                                            <i class="fas fa-info-circle"></i>
                                                        </button>
                                                        <div id="courses-{{ $lecturer['id'] }}" 
                                                            class="hidden absolute left-0 top-6 z-50 w-64 bg-white border border-gray-200 rounded-lg shadow-lg p-3">
                                                            <div class="text-xs font-medium text-gray-700 mb-2">Assigned Courses:</div>
                                                            <div class="max-h-32 overflow-y-auto space-y-1">
                                                                @foreach($lecturer['courses'] as $course)
                                                                    <div class="text-xs bg-gray-50 px-2 py-1 rounded">{{ $course }}</div>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-sm text-gray-500 italic">No courses assigned</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            @if($lecturer['status'])
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold bg-red-100 text-red-800 rounded-full">
                                                    <i class="fas fa-times-circle mr-1 text-xs"></i>Disabled
                                                </span>
                                            @else
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold bg-green-100 text-green-800 rounded-full">
                                                    <i class="fas fa-check-circle mr-1 text-xs"></i>Active
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <button
                                                    onclick="toggleLecturerStatus(event, '{{ $lecturer['id'] }}', {{ $lecturer['status'] ? 'false' : 'true' }})"
                                                    class="px-3 py-1 rounded-lg text-sm transition-all {{ $lecturer['status'] ? 'bg-green-100 text-green-700 hover:bg-green-200' : 'bg-red-100 text-red-700 hover:bg-red-200' }}">
                                                    @if($lecturer['status'])
                                                        <i class="fas fa-play mr-1"></i>Enable
                                                    @else
                                                        <i class="fas fa-pause mr-1"></i>Disable
                                                    @endif
                                                </button>
                                                @if(count($lecturer['courses']) > 0)
                                                    <button onclick="clearLecturerCourses('{{ $lecturer['id'] }}', '{{ $lecturer['name'] }}')"
                                                        class="px-3 py-1 bg-orange-100 text-orange-700 rounded-lg hover:bg-orange-200 transition-all text-sm">
                                                        <i class="fas fa-eraser mr-1"></i>Clear Courses
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- Bulk Actions Bar (Hidden by default) -->
        <div id="bulkActionsBar"
            class="hidden fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-white rounded-lg shadow-lg border border-gray-200 p-4 z-50">
            <div class="flex items-center space-x-4">
                <span class="text-sm text-gray-600"><span id="selectedCount">0</span> lecturers selected</span>
                <div class="flex items-center space-x-2">
                    <button onclick="bulkToggleSelected('enable')"
                        class="px-3 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 text-sm">
                        <i class="fas fa-check mr-1"></i>Enable Selected
                    </button>
                    <button onclick="bulkToggleSelected('disable')"
                        class="px-3 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                        <i class="fas fa-ban mr-1"></i>Disable Selected
                    </button>
                    <button onclick="bulkClearCoursesSelected()"
                        class="px-3 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 text-sm">
                        <i class="fas fa-eraser mr-1"></i>Clear Courses
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Clear Confirmation Modal -->
    <div id="clearCoursesModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeClearCoursesModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-orange-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-orange-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Clear Course Assignments
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" id="clearCoursesMessage">
                                    Are you sure you want to clear all course assignments? This will remove all courses from the selected lecturer(s).
                                </p>
                                <div class="mt-3 p-3 bg-yellow-50 rounded border border-yellow-200">
                                    <p class="text-sm text-yellow-800">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        This action will allow you to reassign courses fresh to the lecturer(s).
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" onclick="confirmClearCourses()" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-orange-600 text-base font-medium text-white hover:bg-orange-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 sm:ml-3 sm:w-auto sm:text-sm">
                        <i class="fas fa-eraser mr-2"></i>Clear Courses
                    </button>
                    <button type="button" onclick="closeClearCoursesModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 flex items-center space-x-4">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-indigo-600"></div>
            <span class="text-gray-700 font-medium">Processing...</span>
        </div>
    </div>

    <script>
        // CSRF Token Setup
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Course clearing variables - moved to top to avoid hoisting issues
        let clearCoursesAction = null;
        let clearCoursesData = null;

        // Loading functions
        function showLoading() {
            document.getElementById('loadingOverlay').classList.remove('hidden');
        }

        function hideLoading() {
            document.getElementById('loadingOverlay').classList.add('hidden');
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', filterTable);
        document.getElementById('statusFilter').addEventListener('change', filterTable);

        function filterTable() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const rows = document.querySelectorAll('.lecturer-row');

            // Track which faculties have visible rows
            const facultiesWithResults = new Set();

            rows.forEach(row => {
                const name = row.getAttribute('data-name');
                const email = row.getAttribute('data-email');
                const status = row.getAttribute('data-status');
                const faculty = row.getAttribute('data-faculty');

                const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);
                const matchesStatus = !statusFilter || status === statusFilter;

                if (matchesSearch && matchesStatus) {
                    row.style.display = '';
                    facultiesWithResults.add(faculty);
                } else {
                    row.style.display = 'none';
                }
            });

            // Show/hide faculty sections based on whether they have visible rows
            document.querySelectorAll('.faculty-section').forEach(section => {
                const faculty = section.getAttribute('data-faculty');
                const isCurrentlyFiltered = document.querySelector('.faculty-filter-btn.active').textContent.trim() !== 'All Faculties';
                
                if (searchTerm || statusFilter) {
                    // When filtering, only show faculties that have matching results
                    if (facultiesWithResults.has(faculty)) {
                        section.style.display = 'block';
                    } else {
                        section.style.display = 'none';
                    }
                } else {
                    // When not filtering, respect the faculty tab selection
                    if (isCurrentlyFiltered) {
                        // Keep the current faculty filter state
                        return;
                    } else {
                        // Show all faculties
                        section.style.display = 'block';
                    }
                }
            });
        }

        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('statusFilter').value = '';
            filterTable();
        }

        // Toggle individual lecturer status
        async function toggleLecturerStatus(event, uid, newStatus) {
            showLoading();

            try {
                console.log('Toggling lecturer:', uid, 'to status:', newStatus);

                const response = await fetch(`/superadmin/lecturer-control/toggle/${uid}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });

                console.log('Response status:', response.status);
                const result = await response.json();
                console.log('Response data:', result);

                if (result.success) {
                    // Show success message briefly before reload
                    const button = event.target.closest('button');
                    const originalContent = button.innerHTML;
                    button.innerHTML = '<i class="fas fa-check mr-1"></i>Success!';
                    button.classList.add('bg-green-500', 'text-white');

                    setTimeout(() => {
                        location.reload();
                    }, 1000);
                } else {
                    hideLoading();
                    alert('Error: ' + (result.message || 'Unknown error occurred'));
                }
            } catch (error) {
                hideLoading();
                console.error('Error details:', error);
                alert('An error occurred while updating lecturer status: ' + error.message);
            }
        }

        // Bulk actions for all lecturers
        async function bulkAction(action) {
            const disable = action === 'disable';

            if (!confirm(`Are you sure you want to ${action} ALL lecturers?`)) {
                return;
            }

            showLoading();

            try {
                const response = await fetch('/superadmin/lecturer-control/toggle-all', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ disable: disable })
                });

                const result = await response.json();

                if (result.success) {
                    // Show success message
                    hideLoading();
                    alert('Successfully updated all lecturers!');
                    location.reload();
                } else {
                    hideLoading();
                    alert('Error: ' + (result.message || 'Unknown error occurred'));
                }
            } catch (error) {
                hideLoading();
                console.error('Error:', error);
                alert('An error occurred while updating lecturer statuses: ' + error.message);
            }
        }

        // Checkbox selection logic
        const selectAllCheckbox = document.getElementById('selectAll');
        const lecturerCheckboxes = document.querySelectorAll('.lecturer-checkbox');
        const bulkActionsBar = document.getElementById('bulkActionsBar');
        const selectedCountSpan = document.getElementById('selectedCount');

        selectAllCheckbox.addEventListener('change', function () {
            lecturerCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionsBar();
        });

        lecturerCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBulkActionsBar);
        });

        function updateBulkActionsBar() {
            const checkedBoxes = document.querySelectorAll('.lecturer-checkbox:checked');
            const count = checkedBoxes.length;

            selectedCountSpan.textContent = count;

            if (count > 0) {
                bulkActionsBar.classList.remove('hidden');
            } else {
                bulkActionsBar.classList.add('hidden');
            }

            // Update select all checkbox state
            selectAllCheckbox.indeterminate = count > 0 && count < lecturerCheckboxes.length;
            selectAllCheckbox.checked = count === lecturerCheckboxes.length;
        }

        // Bulk toggle for selected lecturers
        async function bulkToggleSelected(action) {
            const checkedBoxes = document.querySelectorAll('.lecturer-checkbox:checked');
            const lecturerIds = Array.from(checkedBoxes).map(cb => cb.value);

            if (lecturerIds.length === 0) {
                alert('Please select lecturers first');
                return;
            }

            if (!confirm(`Are you sure you want to ${action} ${lecturerIds.length} selected lecturers?`)) {
                return;
            }

            showLoading();

            try {
                // Toggle each selected lecturer individually
                const promises = lecturerIds.map(uid => {
                    return fetch(`/superadmin/lecturer-control/toggle/${uid}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                });

                await Promise.all(promises);

                // Show success message
                hideLoading();
                alert(`Successfully updated ${lecturerIds.length} lecturers!`);
                location.reload();
            } catch (error) {
                hideLoading();
                console.error('Error:', error);
                alert('An error occurred while updating lecturer statuses: ' + error.message);
            }
        }

        // Add loading states to buttons
        function addButtonLoadingState(button, originalText) {
            button.disabled = true;
            button.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Processing...';
            button.classList.add('opacity-75');
        }

        function removeButtonLoadingState(button, originalText) {
            button.disabled = false;
            button.innerHTML = originalText;
            button.classList.remove('opacity-75');
        }

        // Enhanced bulk action buttons with loading states
        document.addEventListener('DOMContentLoaded', function () {
            const bulkButtons = document.querySelectorAll('[onclick^="bulkAction"]');
            bulkButtons.forEach(button => {
                const originalOnClick = button.getAttribute('onclick');
                const originalText = button.innerHTML;

                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    addButtonLoadingState(this, originalText);

                    // Execute original function after slight delay
                    setTimeout(() => {
                        eval(originalOnClick);
                    }, 100);
                });
            });
        });

        // Faculty filtering functions
        function showAllFaculties() {
            document.querySelectorAll('.faculty-section').forEach(section => {
                section.style.display = 'block';
            });
            updateActiveTab(event.target);
            // Reapply current search/status filters
            filterTable();
        }

        function showFaculty(faculty) {
            document.querySelectorAll('.faculty-section').forEach(section => {
                if (section.getAttribute('data-faculty') === faculty) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
            updateActiveTab(event.target);
            // Reapply current search/status filters
            filterTable();
        }

        function updateActiveTab(activeButton) {
            document.querySelectorAll('.faculty-filter-btn').forEach(btn => {
                btn.classList.remove('active', 'bg-indigo-600', 'text-white');
                btn.classList.add('bg-gray-200', 'text-gray-700');
            });
            activeButton.classList.add('active', 'bg-indigo-600', 'text-white');
            activeButton.classList.remove('bg-gray-200', 'text-gray-700');
        }

        // Bulk action for specific faculty
        async function bulkActionFaculty(action, faculty) {
            const disable = action === 'disable';
            
            if (!confirm(`Are you sure you want to ${action} all lecturers in ${faculty}?`)) {
                return;
            }

            showLoading();

            try {
                const facultyCheckboxes = document.querySelectorAll(`.lecturer-checkbox[data-faculty="${faculty}"]`);
                const lecturerIds = Array.from(facultyCheckboxes).map(cb => cb.value);
                
                const promises = lecturerIds.map(uid => {
                    return fetch(`/superadmin/lecturer-control/toggle/${uid}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    });
                });

                await Promise.all(promises);
                hideLoading();
                alert(`Successfully updated all lecturers in ${faculty}!`);
                location.reload();
            } catch (error) {
                hideLoading();
                console.error('Error:', error);
                alert('An error occurred while updating lecturer statuses: ' + error.message);
            }
        }

        // Course dropdown functionality
        function toggleCourseDropdown(lecturerId) {
            const dropdown = document.getElementById(`courses-${lecturerId}`);
            const allDropdowns = document.querySelectorAll('[id^="courses-"]');
            
            // Close all other dropdowns
            allDropdowns.forEach(dd => {
                if (dd.id !== `courses-${lecturerId}`) {
                    dd.classList.add('hidden');
                }
            });
            
            // Toggle current dropdown
            dropdown.classList.toggle('hidden');
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('[onclick^="toggleCourseDropdown"]') && 
                !event.target.closest('[id^="courses-"]')) {
                const allDropdowns = document.querySelectorAll('[id^="courses-"]');
                allDropdowns.forEach(dd => dd.classList.add('hidden'));
            }
        });

        // Clear courses for individual lecturer
        function clearLecturerCourses(lecturerId, lecturerName) {
            console.log('clearLecturerCourses called with:', lecturerId, lecturerName);
            
            clearCoursesAction = 'single';
            clearCoursesData = { lecturerId, lecturerName };
            
            document.getElementById('clearCoursesMessage').innerHTML = 
                `Are you sure you want to clear all course assignments for <strong>${lecturerName}</strong>? This will remove all courses from this lecturer.`;
            
            const modal = document.getElementById('clearCoursesModal');
            console.log('Modal element found:', modal !== null);
            modal.classList.remove('hidden');
            console.log('Modal should now be visible');
        }

        // Function to close the modal
        function closeClearCoursesModal() {
            console.log('Closing modal');
            document.getElementById('clearCoursesModal').classList.add('hidden');
            clearCoursesAction = null;
            clearCoursesData = null;
        }

        // Function to confirm clearing courses
        async function confirmClearCourses() {
            console.log('Confirm button clicked');
            
            // Store action and data in local variables before they are reset
            const currentAction = clearCoursesAction;
            const currentData = clearCoursesData;

            console.log('Current action (local):', currentAction);
            console.log('Current data (local):', currentData);
            
            if (!currentAction || !currentData) {
                console.log('No action or data found (from local copies), returning');
                alert('Error: No action data found. Please try again.');
                // Ensure global state is clear if it wasn't already
                clearCoursesAction = null;
                clearCoursesData = null;
                return;
            }
            
            // Hide the modal directly
            document.getElementById('clearCoursesModal').classList.add('hidden');
            showLoading();

            try {
                let response, url, requestOptions;
                
                if (currentAction === 'single') {
                    console.log('Making single clear request for:', currentData.lecturerId);
                    url = `{{ route('superadmin.clear-lecturer-courses', ['uid' => 'PLACEHOLDER']) }}`;
                    url = url.replace('PLACEHOLDER', currentData.lecturerId);
                    requestOptions = {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        }
                    };
                } else if (currentAction === 'bulk' || currentAction === 'faculty') {
                    console.log('Making bulk clear request for IDs:', currentData.lecturerIds);
                    url = '{{ route("superadmin.clear-all-lecturer-courses") }}';
                    requestOptions = {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ lecturer_ids: currentData.lecturerIds })
                    };
                } else {
                    // Should not happen if initial check passed, but as a safeguard:
                    throw new Error('Invalid clearCoursesAction: ' + currentAction);
                }

                console.log('Making request to:', url);
                console.log('Request options:', requestOptions);
                
                if (!url) {
                    throw new Error('URL is undefined. Action: ' + currentAction);
                }

                response = await fetch(url, requestOptions);
                
                console.log('Response received:', response);
                
                if (!response) { // Should be caught by fetch errors, but good to have
                    throw new Error('No response received from server');
                }
                
                if (!response.ok) {
                    const errorText = await response.text();
                    console.log('Error response text:', errorText);
                    throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
                }
                
                const result = await response.json();
                console.log('Result:', result);
                hideLoading();
                
                if (result.success) {
                    let successMessage = result.message;
                    if (currentAction === 'single') {
                        successMessage = `Successfully cleared courses for ${currentData.lecturerName}!`;
                    } else if (currentAction === 'faculty') {
                        successMessage = `Successfully cleared courses for all lecturers in ${currentData.faculty}!`;
                    } else if (currentAction === 'bulk') {
                        successMessage = `Successfully cleared courses for ${currentData.lecturerIds.length} selected lecturers!`;
                    }
                    
                    alert(successMessage);
                    location.reload();
                } else {
                    alert('Error: ' + (result.message || 'Unknown error occurred'));
                }
            } catch (error) {
                hideLoading();
                console.error('Error occurred in confirmClearCourses:', error);
                alert('An error occurred while clearing courses: ' + error.message);
            }
            
            // Reset global state variables
            clearCoursesAction = null;
            clearCoursesData = null;
        }

        // Bulk clear courses for faculty
        function bulkClearCoursesFaculty(faculty) {
            const facultyCheckboxes = document.querySelectorAll(`.lecturer-checkbox[data-faculty="${faculty}"]`);
            const lecturerIds = Array.from(facultyCheckboxes).map(cb => cb.value);
            const lecturerCount = lecturerIds.length;
            
            clearCoursesAction = 'faculty';
            clearCoursesData = { faculty, lecturerIds, lecturerCount };
            
            document.getElementById('clearCoursesMessage').innerHTML = 
                `Are you sure you want to clear all course assignments for <strong>all ${lecturerCount} lecturers in ${faculty}</strong>? This will remove all courses from these lecturers.`;
            document.getElementById('clearCoursesModal').classList.remove('hidden');
        }

        // Bulk clear courses for selected lecturers
        function bulkClearCoursesSelected() {
            const checkedBoxes = document.querySelectorAll('.lecturer-checkbox:checked');
            const lecturerIds = Array.from(checkedBoxes).map(cb => cb.value);
            
            if (lecturerIds.length === 0) {
                alert('Please select lecturers first');
                return;
            }

            clearCoursesAction = 'bulk';
            clearCoursesData = { lecturerIds };
            
            document.getElementById('clearCoursesMessage').innerHTML = 
                `Are you sure you want to clear all course assignments for <strong>${lecturerIds.length} selected lecturers</strong>? This will remove all courses from the selected lecturers.`;
            document.getElementById('clearCoursesModal').classList.remove('hidden');
        }
    </script>

</body>

</html>