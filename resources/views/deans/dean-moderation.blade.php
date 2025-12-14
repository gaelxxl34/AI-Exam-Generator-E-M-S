<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dean Moderation</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>

    <style>
        .search-container {
            display: flex;
            justify-content: center;
            margin-bottom: 1rem;
        }

        .search-bar {
            width: 50%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
            font-size: 16px;
        }

        .highlight {
            background-color: #f0f9ff !important;
        }

        /* Skeleton loading animation */
        .skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: skeleton-loading 1.5s infinite;
        }

        @keyframes skeleton-loading {
            0% {
                background-position: 200% 0;
            }

            100% {
                background-position: -200% 0;
            }
        }

        .skeleton-row td {
            padding: 12px 16px;
        }

        .skeleton-text {
            height: 16px;
            border-radius: 4px;
        }

        .skeleton-btn {
            height: 28px;
            width: 70px;
            border-radius: 4px;
            display: inline-block;
        }

        /* Status filter tabs */
        .status-tab {
            padding: 8px 16px;
            border-radius: 20px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 14px;
        }

        .status-tab:hover {
            background-color: #e5e7eb;
        }

        .status-tab.active {
            background-color: #3b82f6;
            color: white;
        }

        .status-tab .count {
            background-color: rgba(0, 0, 0, 0.1);
            padding: 2px 8px;
            border-radius: 10px;
            margin-left: 4px;
            font-size: 12px;
        }

        .status-tab.active .count {
            background-color: rgba(255, 255, 255, 0.2);
        }

        /* Bulk action bar styles */
        .bulk-action-bar {
            position: sticky;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(59, 130, 246, 0.95), rgba(59, 130, 246, 0.9));
            backdrop-filter: blur(8px);
            transform: translateY(100%);
            transition: transform 0.3s ease-out;
            z-index: 40;
        }

        .bulk-action-bar.visible {
            transform: translateY(0);
        }

        /* Checkbox styling */
        .exam-checkbox {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #3b82f6;
        }

        .exam-checkbox:checked+td {
            background-color: #eff6ff;
        }

        tr.selected {
            background-color: #eff6ff !important;
        }
    </style>
</head>

<body>

    @include('partials.dean-navbar')

    <div class="p-6 sm:ml-64 mt-20">
        <h1 class="text-3xl font-bold mb-6 text-center text-gray-800">📘 Dean Dashboard - Exam Moderation</h1>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6" id="statsCards">
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-blue-500">
                <div class="text-gray-500 text-sm">Total Exams</div>
                <div class="text-2xl font-bold text-gray-800" id="statTotal">
                    <span class="skeleton skeleton-text inline-block w-12"></span>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-yellow-500">
                <div class="text-gray-500 text-sm">Pending Review</div>
                <div class="text-2xl font-bold text-yellow-600" id="statPending">
                    <span class="skeleton skeleton-text inline-block w-12"></span>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-green-500">
                <div class="text-gray-500 text-sm">Approved</div>
                <div class="text-2xl font-bold text-green-600" id="statApproved">
                    <span class="skeleton skeleton-text inline-block w-12"></span>
                </div>
            </div>
            <div class="bg-white p-4 rounded-lg shadow border-l-4 border-red-500">
                <div class="text-gray-500 text-sm">Declined</div>
                <div class="text-2xl font-bold text-red-600" id="statDeclined">
                    <span class="skeleton skeleton-text inline-block w-12"></span>
                </div>
            </div>
        </div>

        <!-- Status Filter Tabs -->
        <div class="flex justify-center gap-2 mb-4 flex-wrap" id="statusTabs">
            <button class="status-tab active" data-status="all">
                All <span class="count" id="tabCountAll">-</span>
            </button>
            <button class="status-tab" data-status="pending">
                📌 Pending <span class="count" id="tabCountPending">-</span>
            </button>
            <button class="status-tab" data-status="approved">
                ✅ Approved <span class="count" id="tabCountApproved">-</span>
            </button>
            <button class="status-tab" data-status="declined">
                ❌ Declined <span class="count" id="tabCountDeclined">-</span>
            </button>
        </div>

        <!-- 🔍 Search Bar with Refresh and Show All -->
        <div class="search-container flex items-center gap-3 flex-wrap">
            <div class="relative flex-1 min-w-[200px]">
                <input type="text" id="searchInput" placeholder="Search all courses by name, code, or lecturer..."
                    class="search-bar focus:ring-2 focus:ring-blue-400 focus:outline-none w-full pr-10">
                <span id="searchIndicator" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hidden">
                    <svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                </span>
            </div>
            <button onclick="toggleShowAll()" id="showAllBtn"
                class="bg-blue-50 hover:bg-blue-100 text-blue-700 px-4 py-2 rounded-lg border border-blue-300 flex items-center gap-2 transition-all"
                title="Show all courses without pagination">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
                <span id="showAllText">Show All</span>
            </button>
            <button onclick="refreshExams()" id="refreshBtn"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg border border-gray-300 flex items-center gap-2 transition-all"
                title="Refresh data (clears cache)">
                <svg id="refreshIcon" class="w-5 h-5 transition-transform" fill="none" stroke="currentColor"
                    viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                </svg>
                <span class="hidden sm:inline">Refresh</span>
            </button>
        </div>
        
        <!-- Search Results Info -->
        <div id="searchResultsInfo" class="hidden mb-3 p-3 bg-blue-50 border border-blue-200 rounded-lg text-blue-800 flex items-center justify-between">
            <span id="searchResultsText"></span>
            <button onclick="clearSearch()" class="text-blue-600 hover:text-blue-800 font-medium">
                <i class="fas fa-times mr-1"></i> Clear Search
            </button>
        </div>

        @if (session('error'))
            <div class="bg-red-500 text-white p-3 rounded mb-4 text-center">
                {{ session('error') }}
            </div>
        @endif

        @if (session('success'))
            <div class="bg-green-500 text-white p-3 rounded mb-4 text-center">
                {{ session('success') }}
            </div>
        @endif

        <!-- Table Container -->
        <div class="overflow-x-auto bg-white p-3 rounded-lg shadow-lg">
            <table class="min-w-full border border-gray-300 rounded-lg shadow-md" id="courseTable">
                <thead class="bg-gray-800 text-white">
                    <tr>
                        <th class="py-3 px-4 text-center w-12">
                            <input type="checkbox" id="selectAllCheckbox" class="exam-checkbox" title="Select All">
                        </th>
                        <th class="py-3 px-4 text-left">Course Name</th>
                        <th class="py-3 px-4 text-left">Course Code</th>
                        <th class="py-3 px-4 text-left">Lecturer Email</th>
                        <th class="py-3 px-4 text-center">Created At</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="courseTableBody">
                    <!-- Skeleton rows while loading -->
                    @for ($i = 0; $i < 10; $i++)
                        <tr class="skeleton-row border-b">
                            <td class="text-center">
                                <div class="skeleton skeleton-text w-4 h-4 mx-auto"></div>
                            </td>
                            <td>
                                <div class="skeleton skeleton-text w-48"></div>
                            </td>
                            <td>
                                <div class="skeleton skeleton-text w-24"></div>
                            </td>
                            <td>
                                <div class="skeleton skeleton-text w-40"></div>
                            </td>
                            <td class="text-center">
                                <div class="skeleton skeleton-text w-32 mx-auto"></div>
                            </td>
                            <td class="text-center">
                                <div class="skeleton skeleton-text w-24 mx-auto"></div>
                            </td>
                            <td class="text-center">
                                <div class="flex justify-center gap-2">
                                    <div class="skeleton skeleton-btn"></div>
                                    <div class="skeleton skeleton-btn"></div>
                                    <div class="skeleton skeleton-btn"></div>
                                </div>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center mt-4" id="paginationContainer">
            <div class="text-gray-600 text-sm" id="paginationInfo">
                Loading exams...
            </div>
            <div class="flex gap-2" id="paginationButtons">
                <!-- Pagination buttons will be inserted here -->
            </div>
        </div>

        <!-- No Results Message -->
        <div id="noResultsMessage" class="text-center text-gray-600 font-semibold mt-4 hidden">
            No exams found for the given search query.
        </div>

        <!-- Empty State -->
        <div id="emptyState" class="text-center py-12 hidden">
            <div class="text-6xl mb-4">📭</div>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Exams Available</h3>
            <p class="text-gray-500">There are no exams to moderate for your faculty at this time.</p>
        </div>
    </div>

    <!-- Decline Modal -->
    <div id="declineModal" class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded shadow-lg w-96">
            <h2 class="text-xl font-bold mb-4">Decline Exam</h2>
            <form id="declineForm" method="POST">
                @csrf
                <textarea name="comment" placeholder="Enter reason for declining..." required
                    class="w-full p-2 border rounded mb-4"></textarea>
                <input type="hidden" id="declineExamId" name="exam_id">
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeDeclineModal()"
                        class="bg-gray-400 hover:bg-gray-600 text-white px-4 py-2 rounded">
                        Cancel
                    </button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                        Submit Decline
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Decline Modal -->
    <div id="bulkDeclineModal"
        class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded shadow-lg w-96">
            <h2 class="text-xl font-bold mb-4">
                <i class="fas fa-times-circle text-red-500 mr-2"></i>
                Decline Selected Exams
            </h2>
            <p class="text-gray-600 mb-3" id="bulkDeclineCount">You are about to decline 0 exams.</p>
            <form id="bulkDeclineForm">
                @csrf
                <textarea id="bulkDeclineComment" name="comment"
                    placeholder="Enter reason for declining all selected exams..." required
                    class="w-full p-2 border rounded mb-4" rows="3"></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="closeBulkDeclineModal()"
                        class="bg-gray-400 hover:bg-gray-600 text-white px-4 py-2 rounded">
                        Cancel
                    </button>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                        <i class="fas fa-times mr-1"></i> Decline All Selected
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Approve Confirmation Modal -->
    <div id="bulkApproveModal"
        class="hidden fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
        <div class="bg-white p-6 rounded shadow-lg w-96">
            <h2 class="text-xl font-bold mb-4">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                Approve Selected Exams
            </h2>
            <p class="text-gray-600 mb-3" id="bulkApproveCount">You are about to approve 0 exams.</p>
            <p class="text-sm text-gray-500 mb-4">This action will mark all selected exams as approved and make them
                available for download.</p>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="closeBulkApproveModal()"
                    class="bg-gray-400 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    Cancel
                </button>
                <button type="button" onclick="executeBulkApprove()"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    <i class="fas fa-check mr-1"></i> Approve All Selected
                </button>
            </div>
        </div>
    </div>

    <!-- Bulk Action Bar (Fixed at bottom) -->
    <div id="bulkActionBar"
        class="bulk-action-bar fixed bottom-0 left-64 right-0 p-4 shadow-lg border-t border-blue-600">
        <div class="flex items-center justify-between max-w-6xl mx-auto">
            <div class="flex items-center text-white">
                <span class="text-lg font-semibold">
                    <span id="selectedCount">0</span> exam(s) selected
                </span>
                <button onclick="clearSelection()"
                    class="ml-4 text-sm underline hover:no-underline opacity-80 hover:opacity-100">
                    Clear selection
                </button>
            </div>
            <div class="flex gap-3">
                <button onclick="showBulkApproveModal()"
                    class="bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded-lg font-medium shadow-md transition transform hover:scale-105">
                    <i class="fas fa-check-circle mr-2"></i>
                    Approve Selected
                </button>
                <button onclick="showBulkDeclineModal()"
                    class="bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-lg font-medium shadow-md transition transform hover:scale-105">
                    <i class="fas fa-times-circle mr-2"></i>
                    Decline Selected
                </button>
            </div>
        </div>
    </div>

    <script>
        // State management
        let currentPage = 1;
        let currentStatus = 'all';
        let allCourses = [];
        let filteredCourses = [];
        let selectedExams = new Set();
        let showAllMode = false;
        let currentSearch = '';
        const perPage = 20;

        // DOM elements
        const tbody = document.getElementById('courseTableBody');
        const searchInput = document.getElementById('searchInput');
        const paginationInfo = document.getElementById('paginationInfo');
        const paginationButtons = document.getElementById('paginationButtons');
        const noResultsMessage = document.getElementById('noResultsMessage');
        const emptyState = document.getElementById('emptyState');
        const bulkActionBar = document.getElementById('bulkActionBar');
        const selectAllCheckbox = document.getElementById('selectAllCheckbox');

        // Load exams on page load
        document.addEventListener('DOMContentLoaded', function () {
            loadExams();
            setupStatusTabs();
            setupSearch();
            setupSelectAll();
        });

        // Setup select all checkbox
        function setupSelectAll() {
            selectAllCheckbox.addEventListener('change', function () {
                const checkboxes = document.querySelectorAll('.row-checkbox');
                checkboxes.forEach(cb => {
                    cb.checked = this.checked;
                    const examId = cb.dataset.examId;
                    if (this.checked) {
                        selectedExams.add(examId);
                        cb.closest('tr').classList.add('selected');
                    } else {
                        selectedExams.delete(examId);
                        cb.closest('tr').classList.remove('selected');
                    }
                });
                updateBulkActionBar();
            });
        }

        // Handle individual checkbox change
        function handleCheckboxChange(checkbox, examId) {
            if (checkbox.checked) {
                selectedExams.add(examId);
                checkbox.closest('tr').classList.add('selected');
            } else {
                selectedExams.delete(examId);
                checkbox.closest('tr').classList.remove('selected');
            }

            // Update select all checkbox state
            const checkboxes = document.querySelectorAll('.row-checkbox');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            const someChecked = Array.from(checkboxes).some(cb => cb.checked);
            selectAllCheckbox.checked = allChecked;
            selectAllCheckbox.indeterminate = someChecked && !allChecked;

            updateBulkActionBar();
        }

        // Update bulk action bar visibility
        function updateBulkActionBar() {
            const count = selectedExams.size;
            document.getElementById('selectedCount').textContent = count;

            if (count > 0) {
                bulkActionBar.classList.add('visible');
            } else {
                bulkActionBar.classList.remove('visible');
            }
        }

        // Clear selection
        function clearSelection() {
            selectedExams.clear();
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = false;
                cb.closest('tr').classList.remove('selected');
            });
            selectAllCheckbox.checked = false;
            selectAllCheckbox.indeterminate = false;
            updateBulkActionBar();
        }

        // Show bulk approve modal
        function showBulkApproveModal() {
            document.getElementById('bulkApproveCount').textContent =
                `You are about to approve ${selectedExams.size} exam(s).`;
            document.getElementById('bulkApproveModal').classList.remove('hidden');
        }

        function closeBulkApproveModal() {
            document.getElementById('bulkApproveModal').classList.add('hidden');
        }

        // Show bulk decline modal
        function showBulkDeclineModal() {
            document.getElementById('bulkDeclineCount').textContent =
                `You are about to decline ${selectedExams.size} exam(s).`;
            document.getElementById('bulkDeclineModal').classList.remove('hidden');
        }

        function closeBulkDeclineModal() {
            document.getElementById('bulkDeclineModal').classList.add('hidden');
        }

        // Execute bulk approve
        async function executeBulkApprove() {
            closeBulkApproveModal();

            const examIds = Array.from(selectedExams);
            let successCount = 0;
            let failCount = 0;

            // Show loading state
            showToast('Processing...', 'info');

            for (const examId of examIds) {
                try {
                    const response = await fetch(`/deans/course/${examId}/approve`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });

                    if (response.ok) {
                        successCount++;
                    } else {
                        failCount++;
                    }
                } catch (error) {
                    failCount++;
                }
            }

            // Show result and reload
            if (failCount === 0) {
                showToast(`Successfully approved ${successCount} exam(s)!`, 'success');
            } else {
                showToast(`Approved ${successCount}, failed ${failCount}`, 'warning');
            }

            clearSelection();
            setTimeout(() => loadExams(), 1000);
        }

        // Execute bulk decline
        document.getElementById('bulkDeclineForm').addEventListener('submit', async function (e) {
            e.preventDefault();

            const comment = document.getElementById('bulkDeclineComment').value;
            if (!comment.trim()) {
                alert('Please enter a reason for declining.');
                return;
            }

            closeBulkDeclineModal();

            const examIds = Array.from(selectedExams);
            let successCount = 0;
            let failCount = 0;

            showToast('Processing...', 'info');

            for (const examId of examIds) {
                try {
                    const formData = new FormData();
                    formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                    formData.append('comment', comment);

                    const response = await fetch(`/deans/course/${examId}/decline`, {
                        method: 'POST',
                        body: formData
                    });

                    if (response.ok) {
                        successCount++;
                    } else {
                        failCount++;
                    }
                } catch (error) {
                    failCount++;
                }
            }

            if (failCount === 0) {
                showToast(`Successfully declined ${successCount} exam(s)!`, 'success');
            } else {
                showToast(`Declined ${successCount}, failed ${failCount}`, 'warning');
            }

            document.getElementById('bulkDeclineComment').value = '';
            clearSelection();
            setTimeout(() => loadExams(), 1000);
        });

        // Toast notification helper
        function showToast(message, type = 'info') {
            const colors = {
                success: 'bg-green-500',
                error: 'bg-red-500',
                warning: 'bg-yellow-500',
                info: 'bg-blue-500'
            };

            const toast = document.createElement('div');
            toast.className = `fixed top-20 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 transition-opacity duration-300`;
            toast.textContent = message;
            document.body.appendChild(toast);

            setTimeout(() => {
                toast.style.opacity = '0';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Setup status filter tabs
        function setupStatusTabs() {
            document.querySelectorAll('.status-tab').forEach(tab => {
                tab.addEventListener('click', function () {
                    document.querySelectorAll('.status-tab').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    currentStatus = this.dataset.status;
                    currentPage = 1;
                    loadExams();
                });
            });
        }

        // Setup search with debounce - now searches ALL courses via backend
        function setupSearch() {
            let debounceTimer;
            searchInput.addEventListener('input', function () {
                clearTimeout(debounceTimer);
                const searchValue = this.value.trim();
                
                // Show loading indicator
                document.getElementById('searchIndicator').classList.remove('hidden');
                
                debounceTimer = setTimeout(() => {
                    currentSearch = searchValue;
                    currentPage = 1;
                    loadExams(); // Reload from server with search parameter
                }, 400);
            });
            
            // Enter key for immediate search
            searchInput.addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    clearTimeout(debounceTimer);
                    currentSearch = this.value.trim();
                    currentPage = 1;
                    loadExams();
                }
            });
        }

        // Toggle Show All mode
        function toggleShowAll() {
            showAllMode = !showAllMode;
            const btn = document.getElementById('showAllBtn');
            const text = document.getElementById('showAllText');
            
            if (showAllMode) {
                btn.classList.remove('bg-blue-50', 'text-blue-700', 'border-blue-300');
                btn.classList.add('bg-blue-600', 'text-white', 'border-blue-600');
                text.textContent = 'Paginated';
            } else {
                btn.classList.add('bg-blue-50', 'text-blue-700', 'border-blue-300');
                btn.classList.remove('bg-blue-600', 'text-white', 'border-blue-600');
                text.textContent = 'Show All';
            }
            
            currentPage = 1;
            loadExams();
        }
        
        // Clear search
        function clearSearch() {
            searchInput.value = '';
            currentSearch = '';
            document.getElementById('searchResultsInfo').classList.add('hidden');
            currentPage = 1;
            loadExams();
        }

        // Refresh exams with cache clear
        async function refreshExams() {
            const refreshBtn = document.getElementById('refreshBtn');
            const refreshIcon = document.getElementById('refreshIcon');

            // Show loading state
            refreshBtn.disabled = true;
            refreshIcon.classList.add('animate-spin');

            try {
                await loadExams(true); // Force refresh
                showToast('Data refreshed successfully!', 'success');
            } catch (error) {
                showToast('Failed to refresh data', 'error');
            } finally {
                refreshBtn.disabled = false;
                refreshIcon.classList.remove('animate-spin');
            }
        }

        // Load exams via AJAX
        async function loadExams(forceRefresh = false) {
            showSkeleton();

            try {
                let url = `{{ route('dean.moderation.exams') }}?page=${currentPage}&per_page=${perPage}&status=${currentStatus}`;
                
                if (forceRefresh) {
                    url += '&refresh=1';
                }
                
                // Add search parameter if searching
                if (currentSearch) {
                    url += `&search=${encodeURIComponent(currentSearch)}`;
                }
                
                // Add show_all parameter
                if (showAllMode) {
                    url += '&show_all=1';
                }

                const response = await fetch(url, {
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                
                // Hide search indicator
                document.getElementById('searchIndicator').classList.add('hidden');

                if (data.success) {
                    allCourses = data.courses;
                    updateStats(data.stats);
                    updateTabCounts(data.stats);
                    filterAndRender();
                    updatePagination(data.pagination);
                    
                    // Show search results info
                    const searchInfo = document.getElementById('searchResultsInfo');
                    const searchText = document.getElementById('searchResultsText');
                    
                    if (currentSearch) {
                        searchInfo.classList.remove('hidden');
                        searchText.innerHTML = `<i class="fas fa-search mr-2"></i>Found <strong>${data.courses.length}</strong> results for "<strong>${currentSearch}</strong>"`;
                    } else {
                        searchInfo.classList.add('hidden');
                    }
                } else {
                    showError(data.error || 'Failed to load exams');
                }
            } catch (error) {
                console.error('Error loading exams:', error);
                document.getElementById('searchIndicator').classList.add('hidden');
                showError('Network error. Please try again.');
            }
        }

        // Toast notification helper
        function showToast(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `fixed bottom-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transition-all transform translate-y-2 opacity-0 ${type === 'success' ? 'bg-green-500 text-white' :
                    type === 'error' ? 'bg-red-500 text-white' : 'bg-blue-500 text-white'
                }`;
            toast.textContent = message;
            document.body.appendChild(toast);

            requestAnimationFrame(() => {
                toast.classList.remove('translate-y-2', 'opacity-0');
            });

            setTimeout(() => {
                toast.classList.add('translate-y-2', 'opacity-0');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }
        // Update stats cards
        function updateStats(stats) {
            document.getElementById('statTotal').textContent = stats.total;
            document.getElementById('statPending').textContent = stats.pending;
            document.getElementById('statApproved').textContent = stats.approved;
            document.getElementById('statDeclined').textContent = stats.declined;
        }

        // Update tab counts
        function updateTabCounts(stats) {
            document.getElementById('tabCountAll').textContent = stats.total;
            document.getElementById('tabCountPending').textContent = stats.pending;
            document.getElementById('tabCountApproved').textContent = stats.approved;
            document.getElementById('tabCountDeclined').textContent = stats.declined;
        }

        // Filter courses based on search and render
        // Note: Search is now done server-side for better performance across ALL courses
        function filterAndRender() {
            // Server-side search now handles filtering
            // Just render whatever we received from the server
            filteredCourses = [...allCourses];
            renderCourses(filteredCourses);

            // Show/hide no results message
            const hasResults = filteredCourses.length > 0;
            noResultsMessage.classList.toggle('hidden', hasResults || !currentSearch);
            
            // Update no results message text
            if (!hasResults && currentSearch) {
                noResultsMessage.innerHTML = `
                    <div class="text-center py-8">
                        <i class="fas fa-search text-4xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600">No exams found matching "<strong>${currentSearch}</strong>"</p>
                        <button onclick="clearSearch()" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Clear Search
                        </button>
                    </div>
                `;
            }
        }

        // Render courses to table
        function renderCourses(courses) {
            tbody.innerHTML = '';

            if (courses.length === 0 && !searchInput.value.trim()) {
                emptyState.classList.remove('hidden');
                document.getElementById('courseTable').classList.add('hidden');
                return;
            }

            emptyState.classList.add('hidden');
            document.getElementById('courseTable').classList.remove('hidden');

            courses.forEach(course => {
                const row = createCourseRow(course);
                tbody.appendChild(row);
            });

            // Sort alphabetically by course name
            sortTableAlphabetically();
        }

        // Create table row for a course
        function createCourseRow(course) {
            const row = document.createElement('tr');
            row.className = 'border-b hover:bg-gray-100 transition course-row';
            row.dataset.examId = course.id;

            const statusHtml = getStatusHtml(course.status);
            const dateFormatted = formatDate(course.created_at);
            const hasEdits = course.last_dean_edit ? '<span class="ml-1 text-xs bg-yellow-100 text-yellow-800 px-1 rounded">Edited</span>' : '';
            const isChecked = selectedExams.has(course.id);

            if (isChecked) {
                row.classList.add('selected');
            }

            row.innerHTML = `
                <td class="py-3 px-4 text-center">
                    <input type="checkbox" class="row-checkbox exam-checkbox" data-exam-id="${course.id}" 
                        ${isChecked ? 'checked' : ''} 
                        onchange="handleCheckboxChange(this, '${course.id}')">
                </td>
                <td class="py-3 px-4 font-semibold text-gray-700 course-name">${escapeHtml(course.courseUnit)}${hasEdits}</td>
                <td class="py-3 px-4 text-gray-700 course-code">${escapeHtml(course.courseCode || 'N/A')}</td>
                <td class="py-3 px-4 text-gray-700">${escapeHtml(course.lecturerEmail || 'N/A')}</td>
                <td class="py-3 px-4 text-center text-gray-600">${dateFormatted}</td>
                <td class="py-3 px-4 text-center font-semibold">${statusHtml}</td>
                <td class="py-3 px-4 text-center">
                    <div class="flex justify-center flex-wrap gap-1">
                        <a href="/deans/review/${course.id}"
                            class="bg-purple-500 hover:bg-purple-700 text-white px-3 py-1 rounded text-sm" title="Review & Edit Questions">
                            <i class="fas fa-edit"></i> Review
                        </a>
                        <form method="POST" action="/deans/course/${course.id}/approve" class="inline">
                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <button type="submit"
                                class="bg-green-500 hover:bg-green-700 text-white px-3 py-1 rounded text-sm">
                                <i class="fas fa-check"></i> Approve
                            </button>
                        </form>
                        <button onclick="openDeclineModal('${course.id}')"
                            class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded text-sm">
                            <i class="fas fa-times"></i> Decline
                        </button>
                    </div>
                </td>
            `;

            return row;
        }

        // Get status HTML
        function getStatusHtml(status) {
            switch (status) {
                case 'Approved':
                    return '<span class="text-green-600">✅ Approved</span>';
                case 'Declined':
                    return '<span class="text-red-600">❌ Declined</span>';
                default:
                    return '<span class="text-gray-600">📌 Pending Review</span>';
            }
        }

        // Format date
        function formatDate(dateStr) {
            if (!dateStr) return 'N/A';
            try {
                const date = new Date(dateStr);
                return date.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short',
                    year: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
            } catch (e) {
                return dateStr;
            }
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }

        // Sort table alphabetically
        function sortTableAlphabetically() {
            const rows = Array.from(tbody.querySelectorAll('tr.course-row'));
            rows.sort((a, b) => {
                const nameA = a.querySelector('.course-name').textContent.trim().toLowerCase();
                const nameB = b.querySelector('.course-name').textContent.trim().toLowerCase();
                return nameA.localeCompare(nameB);
            });
            rows.forEach(row => tbody.appendChild(row));
        }

        // Update pagination UI
        function updatePagination(pagination) {
            const { current_page, total, total_pages, has_more, show_all } = pagination;

            // Show different info based on mode
            if (show_all || currentSearch) {
                paginationInfo.innerHTML = total > 0
                    ? `<span class="text-green-600 font-medium">Showing all ${total} exams</span>` + 
                      (currentSearch ? ` <span class="text-blue-600">(search results)</span>` : '')
                    : 'No exams found';
            } else {
                const start = ((current_page - 1) * perPage) + 1;
                const end = Math.min(current_page * perPage, total);
                paginationInfo.textContent = total > 0
                    ? `Showing ${start}-${end} of ${total} exams`
                    : 'No exams found';
            }

            paginationButtons.innerHTML = '';

            // Only show pagination buttons if not in show_all mode and not searching
            if (total_pages > 1 && !show_all && !currentSearch) {
                // Previous button
                const prevBtn = document.createElement('button');
                prevBtn.className = `px-3 py-1 rounded ${current_page === 1 ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-blue-500 text-white hover:bg-blue-700'}`;
                prevBtn.textContent = '← Previous';
                prevBtn.disabled = current_page === 1;
                prevBtn.onclick = () => {
                    if (current_page > 1) {
                        currentPage = current_page - 1;
                        loadExams();
                    }
                };
                paginationButtons.appendChild(prevBtn);

                // Page indicator
                const pageIndicator = document.createElement('span');
                pageIndicator.className = 'px-3 py-1 text-gray-600';
                pageIndicator.textContent = `Page ${current_page} of ${total_pages}`;
                paginationButtons.appendChild(pageIndicator);

                // Next button
                const nextBtn = document.createElement('button');
                nextBtn.className = `px-3 py-1 rounded ${!has_more ? 'bg-gray-200 text-gray-400 cursor-not-allowed' : 'bg-blue-500 text-white hover:bg-blue-700'}`;
                nextBtn.textContent = 'Next →';
                nextBtn.disabled = !has_more;
                nextBtn.onclick = () => {
                    if (has_more) {
                        currentPage = current_page + 1;
                        loadExams();
                    }
                };
                paginationButtons.appendChild(nextBtn);
            }
        }

        // Show skeleton loading
        function showSkeleton() {
            tbody.innerHTML = '';
            for (let i = 0; i < 10; i++) {
                tbody.innerHTML += `
                    <tr class="skeleton-row border-b">
                        <td class="py-3 px-4 text-center"><div class="skeleton skeleton-text w-4 h-4 mx-auto"></div></td>
                        <td class="py-3 px-4"><div class="skeleton skeleton-text w-48 h-4"></div></td>
                        <td class="py-3 px-4"><div class="skeleton skeleton-text w-24 h-4"></div></td>
                        <td class="py-3 px-4"><div class="skeleton skeleton-text w-40 h-4"></div></td>
                        <td class="py-3 px-4 text-center"><div class="skeleton skeleton-text w-32 h-4 mx-auto"></div></td>
                        <td class="py-3 px-4 text-center"><div class="skeleton skeleton-text w-24 h-4 mx-auto"></div></td>
                        <td class="py-3 px-4 text-center">
                            <div class="flex justify-center gap-2">
                                <div class="skeleton skeleton-btn"></div>
                                <div class="skeleton skeleton-btn"></div>
                                <div class="skeleton skeleton-btn"></div>
                            </div>
                        </td>
                    </tr>
                `;
            }
            emptyState.classList.add('hidden');
            document.getElementById('courseTable').classList.remove('hidden');

            // Reset select all checkbox
            if (selectAllCheckbox) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            }
        }

        // Show error message
        function showError(message) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="7" class="py-8 text-center text-red-500">
                        <div class="text-4xl mb-2">⚠️</div>
                        <div>${escapeHtml(message)}</div>
                        <button onclick="loadExams()" class="mt-4 bg-blue-500 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Try Again
                        </button>
                    </td>
                </tr>
            `;
        }

        // Modal functions
        function openDeclineModal(examId) {
            document.getElementById('declineModal').classList.remove('hidden');
            document.getElementById('declineExamId').value = examId;
            document.getElementById('declineForm').action = `/deans/course/${examId}/decline`;
        }

        function closeDeclineModal() {
            document.getElementById('declineModal').classList.add('hidden');
        }
    </script>

</body>

</html>