<nav class="fixed top-0 z-50 w-full border-b border-gray-200 dark:bg-black dark:border-gray-700"
    style="background-color: #7a0000;">
    <div class="px-3 py-4 lg:px-5 lg:pl-3">
        <div class="flex items-center justify-between">
            <div class="flex items-center justify-start rtl:justify-end">
                <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar"
                    type="button"
                    class="inline-flex items-center p-2 text-sm text-gray-500 rounded-lg sm:hidden hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                    <span class="sr-only">Open sidebar</span>
                    <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                        xmlns="http://www.w3.org/2000/svg">
                        <path clip-rule="evenodd" fill-rule="evenodd"
                            d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                        </path>
                    </svg>
                </button>
                <a href="{{ route('dean.dashboard') }}" class="flex ms-2 md:me-24">
                    <img src="https://online.iuea.ac.ug/pluginfile.php/1/theme_remui/logo/1709968828/IUEA%20Logo%20-%20Moodle%201280x525.png"
                        class="h-12 me-3" alt="IUEA Logo" />
                </a>
            </div>

            <div class="flex items-center">
                <!-- Notification Bell -->
                <div class="relative mr-4">
                    <button type="button" id="deanNotificationBtn" data-dropdown-toggle="dean-notification-dropdown"
                        class="relative p-2 text-white hover:bg-white/10 rounded-lg focus:outline-none focus:ring-2 focus:ring-white/20">
                        <span class="sr-only">View notifications</span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                        <!-- Notification Badge -->
                        <span id="deanNotificationBadge"
                            class="hidden absolute -top-1 -right-1 inline-flex items-center justify-center w-5 h-5 text-xs font-bold text-white bg-yellow-500 rounded-full">0</span>
                    </button>

                    <!-- Notification Dropdown -->
                    <div class="z-50 hidden w-80 max-h-96 overflow-y-auto bg-white divide-y divide-gray-100 rounded-lg shadow-lg dark:bg-gray-800 dark:divide-gray-700"
                        id="dean-notification-dropdown">
                        <div class="flex items-center justify-between px-4 py-3 bg-gray-50 dark:bg-gray-700">
                            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Notifications</h3>
                            <button type="button" id="deanMarkAllReadBtn"
                                class="text-xs text-blue-600 hover:underline dark:text-blue-400">
                                Mark all as read
                            </button>
                        </div>
                        <div id="deanNotificationList" class="divide-y divide-gray-100 dark:divide-gray-700">
                            <div class="px-4 py-3 hover:bg-gray-50 cursor-pointer"
                                onclick="window.location='{{ route('dean.moderation') }}'">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <span
                                            class="inline-flex items-center justify-center w-8 h-8 bg-yellow-100 text-yellow-600 rounded-full">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </span>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">Exams Pending Review</p>
                                        <p class="text-xs text-gray-500">You have exams waiting for your approval</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <a href="{{ route('dean.moderation') }}"
                            class="block px-4 py-2 text-sm text-center text-blue-600 bg-gray-50 hover:bg-gray-100 dark:bg-gray-700 dark:text-blue-400 dark:hover:bg-gray-600">
                            View all pending exams
                        </a>
                    </div>
                </div>

                <!-- User Menu -->
                <div class="flex items-center ms-3">
                    <div>
                        <button type="button"
                            class="flex items-center justify-center text-sm bg-gray-800 rounded-full focus:ring-4 focus:ring-gray-300 dark:focus:ring-gray-600"
                            aria-expanded="false" data-dropdown-toggle="dropdown-user">
                            <span class="sr-only">Open user menu</span>
                            <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20"
                                xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                    d="M10 4a4 4 0 100 8 4 4 0 000-8zM5 18a6.978 6.978 0 015-2c1.67 0 3.26.65 4.47 1.72A7.963 7.963 0 0010 20a7.963 7.963 0 00-4.47-1.28C6.74 17.65 8.33 18 10 18z"
                                    clip-rule="evenodd"></path>
                            </svg>
                        </button>
                    </div>
                    <div class="z-50 hidden my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-800 dark:divide-gray-600"
                        id="dropdown-user">
                        @if(session('user_email'))
                            <div class="px-4 py-3" role="none">
                                <p class="text-sm text-gray-900 dark:text-white" role="none">
                                    {{ session('user_firstName') }}
                                </p>
                                <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-300" role="none">
                                    {{ session('user_email') }}
                                </p>
                                <p class="text-xs text-blue-600 mt-1">
                                    <i class="fas fa-user-shield mr-1"></i> Dean
                                </p>
                            </div>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <ul class="py-1" role="none">
                                    <li>
                                        <button type="submit"
                                            class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-600 dark:hover:text-white"
                                            role="menuitem">
                                            Sign out
                                        </button>
                                    </li>
                                </ul>
                            </form>
                        @else
                            <p>No user logged in.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<aside id="logo-sidebar"
    class="fixed top-5 left-0 z-40 w-64 h-screen pt-20 transition-transform -translate-x-full bg-white border-r border-black sm:translate-x-0 dark:bg-black dark:border-black"
    aria-label="Sidebar">
    <div class="h-full px-3 pb-4 overflow-y-auto bg-white dark:bg-black">
        <ul class="space-y-2 font-medium">
            <!-- Dashboard -->
            <li>
                <a href="{{ route('dean.dashboard') }}"
                    class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('dean.dashboard') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('dean.dashboard') ? 'text-blue-600' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 22 21">
                        <path
                            d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                        <path
                            d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                    </svg>
                    <span class="ms-3 font-medium">Dashboard</span>
                </a>
            </li>

            <!-- Exam Moderation -->
            <li>
                <a href="{{ route('dean.moderation') }}"
                    class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('dean.moderation') || request()->routeIs('dean.review.exam') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                    <svg class="w-5 h-5 transition duration-75 {{ request()->routeIs('dean.moderation') || request()->routeIs('dean.review.exam') ? 'text-yellow-600' : 'text-gray-500 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white' }}"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z" />
                        <path fill-rule="evenodd"
                            d="M4 5a2 2 0 012-2 3 3 0 003 3h2a3 3 0 003-3 2 2 0 012 2v11a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3zm-3 4a1 1 0 100 2h.01a1 1 0 100-2H7zm3 0a1 1 0 100 2h3a1 1 0 100-2h-3z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Exam Moderation</span>
                    @if(isset($pendingCount) && $pendingCount > 0)
                        <span
                            class="inline-flex items-center justify-center w-5 h-5 text-xs font-semibold text-yellow-800 bg-yellow-200 rounded-full">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </a>
            </li>

            <!-- Divider -->
            <li class="pt-4 mt-4 border-t border-gray-200 dark:border-gray-700">
                <span class="flex items-center p-2 text-xs font-semibold text-gray-400 uppercase dark:text-gray-500">
                    Quick Help
                </span>
            </li>

            <!-- Help & Guide -->
            <li>
                <a href="#" onclick="showDeanHelpModal(); return false;"
                    class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-8-3a1 1 0 00-.867.5 1 1 0 11-1.731-1A3 3 0 0113 8a3.001 3.001 0 01-2 2.83V11a1 1 0 11-2 0v-1a1 1 0 011-1 1 1 0 100-2zm0 8a1 1 0 100-2 1 1 0 000 2z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Help & Guide</span>
                </a>
            </li>

            <!-- Keyboard Shortcuts -->
            <li>
                <a href="#" onclick="showKeyboardShortcuts(); return false;"
                    class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group">
                    <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                        aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M3 5a2 2 0 012-2h10a2 2 0 012 2v8a2 2 0 01-2 2h-2.22l.123.489.804.804A1 1 0 0113 18H7a1 1 0 01-.707-1.707l.804-.804L7.22 15H5a2 2 0 01-2-2V5zm5.771 7H5V5h10v7H8.771z"
                            clip-rule="evenodd" />
                    </svg>
                    <span class="flex-1 ms-3 whitespace-nowrap">Keyboard Shortcuts</span>
                </a>
            </li>
        </ul>
    </div>
</aside>

<!-- Dean Help Modal -->
<div id="deanHelpModal" tabindex="-1" aria-hidden="true"
    class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50">
    <div class="relative p-4 w-full max-w-2xl max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-user-shield text-blue-500 mr-2"></i>
                    Dean Quick Guide
                </h3>
                <button type="button" onclick="closeDeanHelpModal()"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                </button>
            </div>
            <div class="p-4 md:p-5 space-y-4">
                <div class="space-y-3">
                    <div class="flex items-start p-3 bg-blue-50 rounded-lg">
                        <span
                            class="flex items-center justify-center w-8 h-8 bg-blue-100 text-blue-600 rounded-full mr-3 flex-shrink-0">1</span>
                        <div>
                            <h4 class="font-medium text-gray-900">Review Pending Exams</h4>
                            <p class="text-sm text-gray-600">Go to "Exam Moderation" to see all exams submitted by
                                lecturers awaiting your review.</p>
                        </div>
                    </div>
                    <div class="flex items-start p-3 bg-green-50 rounded-lg">
                        <span
                            class="flex items-center justify-center w-8 h-8 bg-green-100 text-green-600 rounded-full mr-3 flex-shrink-0">2</span>
                        <div>
                            <h4 class="font-medium text-gray-900">Review & Edit Questions</h4>
                            <p class="text-sm text-gray-600">Click "Review" on any exam to view questions. You can edit
                                questions directly - all changes are logged.</p>
                        </div>
                    </div>
                    <div class="flex items-start p-3 bg-yellow-50 rounded-lg">
                        <span
                            class="flex items-center justify-center w-8 h-8 bg-yellow-100 text-yellow-600 rounded-full mr-3 flex-shrink-0">3</span>
                        <div>
                            <h4 class="font-medium text-gray-900">Approve or Decline</h4>
                            <p class="text-sm text-gray-600">After review, approve the exam or decline with feedback.
                                Lecturers will see your comments.</p>
                        </div>
                    </div>
                    <div class="flex items-start p-3 bg-purple-50 rounded-lg">
                        <span
                            class="flex items-center justify-center w-8 h-8 bg-purple-100 text-purple-600 rounded-full mr-3 flex-shrink-0">4</span>
                        <div>
                            <h4 class="font-medium text-gray-900">Monitor Activity</h4>
                            <p class="text-sm text-gray-600">Dashboard shows downloads, previews, and security events
                                for your faculty.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex items-center p-4 md:p-5 border-t border-gray-200 rounded-b dark:border-gray-600">
                <button onclick="closeDeanHelpModal()" type="button"
                    class="text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">Got
                    it!</button>
            </div>
        </div>
    </div>
</div>

<!-- Keyboard Shortcuts Modal -->
<div id="keyboardShortcutsModal" tabindex="-1" aria-hidden="true"
    class="hidden fixed inset-0 z-[60] flex items-center justify-center bg-black bg-opacity-50">
    <div class="relative p-4 w-full max-w-md max-h-full">
        <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
            <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
                    <i class="fas fa-keyboard text-gray-500 mr-2"></i>
                    Keyboard Shortcuts
                </h3>
                <button type="button" onclick="closeKeyboardShortcuts()"
                    class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white">
                    <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                        viewBox="0 0 14 14">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6" />
                    </svg>
                </button>
            </div>
            <div class="p-4 md:p-5">
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="text-sm text-gray-700">Save Changes</span>
                        <kbd
                            class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded">Ctrl
                            + S</kbd>
                    </div>
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="text-sm text-gray-700">Next Question</span>
                        <kbd
                            class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded">↓
                            or J</kbd>
                    </div>
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="text-sm text-gray-700">Previous Question</span>
                        <kbd
                            class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded">↑
                            or K</kbd>
                    </div>
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="text-sm text-gray-700">Approve Exam</span>
                        <kbd
                            class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded">Ctrl
                            + Enter</kbd>
                    </div>
                    <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
                        <span class="text-sm text-gray-700">Show Shortcuts</span>
                        <kbd
                            class="px-2 py-1 text-xs font-semibold text-gray-800 bg-gray-100 border border-gray-200 rounded">?</kbd>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function showDeanHelpModal() {
        document.getElementById('deanHelpModal').classList.remove('hidden');
    }

    function closeDeanHelpModal() {
        document.getElementById('deanHelpModal').classList.add('hidden');
    }

    function showKeyboardShortcuts() {
        document.getElementById('keyboardShortcutsModal').classList.remove('hidden');
    }

    function closeKeyboardShortcuts() {
        document.getElementById('keyboardShortcutsModal').classList.add('hidden');
    }

    // Close modals on escape key
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') {
            closeDeanHelpModal();
            closeKeyboardShortcuts();
        }
        // Show shortcuts on ? key
        if (e.key === '?' && !e.ctrlKey && !e.metaKey) {
            const activeElement = document.activeElement;
            if (activeElement.tagName !== 'INPUT' && activeElement.tagName !== 'TEXTAREA' && !activeElement.classList.contains('note-editable')) {
                e.preventDefault();
                showKeyboardShortcuts();
            }
        }
    });

    // Close modal on background click
    document.getElementById('deanHelpModal')?.addEventListener('click', function (e) {
        if (e.target === this) closeDeanHelpModal();
    });

    document.getElementById('keyboardShortcutsModal')?.addEventListener('click', function (e) {
        if (e.target === this) closeKeyboardShortcuts();
    });
</script>