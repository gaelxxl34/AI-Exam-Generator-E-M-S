<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Administrator - Super Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-50">
    @include('partials.super-admin-navbar')

    <div class="p-4 sm:ml-64 mt-20">


        <div class="container mx-auto mb-8">
            <div class="flex justify-center">
                <div class="w-full max-w-2xl">
                    <!-- Page Title -->
                    <div class="mb-6">
                        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-lg shadow-lg p-6 text-white">
                            <h1 class="text-2xl font-bold flex items-center">
                                <i class="fas fa-user-edit mr-3"></i>Edit Administrator
                            </h1>
                            <p class="text-indigo-100 mt-2">Update administrator information and permissions</p>
                        </div>
                    </div>

                    <!-- Success/Error Messages -->
                    @if(session('success'))
                        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg"
                            role="alert">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-2"></i>
                                <span>{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg" role="alert">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <span>{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    <!-- Main Card -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <!-- Form -->
                        <form action="{{ route('admin.update-admin-data', ['adminId' => $admin['id']]) }}" method="POST"
                            class="p-6">
                            @csrf
                            @method('PUT')

                            <div class="space-y-6">
                                <!-- Admin Info Header -->
                                <div class="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                    <div class="flex items-center">
                                        <div
                                            class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center mr-4">
                                            <span class="font-semibold text-indigo-600 text-lg">
                                                {{ strtoupper(substr($admin['firstName'], 0, 1) . substr($admin['lastName'], 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-medium text-gray-900">{{ $admin['firstName'] }}
                                                {{ $admin['lastName'] }}
                                            </h3>
                                            <p class="text-sm text-gray-500">Administrator ID:
                                                {{ substr($admin['id'], 0, 8) }}...
                                            </p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Personal Information Section -->
                                <div class="border-b border-gray-200 pb-6">
                                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h2>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- First Name -->
                                        <div>
                                            <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">
                                                <i class="fas fa-user mr-2 text-gray-500"></i>First Name
                                            </label>
                                            <input type="text" id="firstName" name="firstName"
                                                value="{{ $admin['firstName'] }}" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                            @error('firstName')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Last Name -->
                                        <div>
                                            <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">
                                                <i class="fas fa-user mr-2 text-gray-500"></i>Last Name
                                            </label>
                                            <input type="text" id="lastName" name="lastName"
                                                value="{{ $admin['lastName'] }}" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                            @error('lastName')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>

                                    <!-- Email -->
                                    <div class="mt-6">
                                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-envelope mr-2 text-gray-500"></i>Email Address
                                        </label>
                                        <input type="email" id="email" name="email" value="{{ $admin['email'] }}"
                                            required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                        @error('email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                <!-- Role & Faculty Section -->
                                <div>
                                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Role & Permissions</h2>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- Role -->
                                        <div>
                                            <label for="role" class="block text-sm font-medium text-gray-700 mb-1">
                                                <i class="fas fa-shield-alt mr-2 text-gray-500"></i>Administrator Role
                                            </label>
                                            <select id="role" name="role" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                                <option value="admin" {{ $admin['role'] == 'admin' ? 'selected' : '' }}>
                                                    Admin</option>
                                                <option value="dean" {{ $admin['role'] == 'dean' ? 'selected' : '' }}>Dean
                                                </option>
                                                <option value="genadmin" {{ $admin['role'] == 'genadmin' ? 'selected' : '' }}>General Admin</option>
                                            </select>
                                            @error('role')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Faculty -->
                                        <div>
                                            <label for="faculty" class="block text-sm font-medium text-gray-700 mb-1">
                                                <i class="fas fa-university mr-2 text-gray-500"></i>Faculty Assignment
                                            </label>
                                            <select id="faculty" name="faculty" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                                                <option value="HEC" {{ $admin['faculty'] == 'HEC' ? 'selected' : '' }}>
                                                    Higher Education Certificate</option>
                                                <option value="FOE" {{ $admin['faculty'] == 'FOE' ? 'selected' : '' }}>
                                                    Faculty of Engineering</option>
                                                <option value="FOL" {{ $admin['faculty'] == 'FOL' ? 'selected' : '' }}>
                                                    Faculty of Law</option>
                                                <option value="FST" {{ $admin['faculty'] == 'FST' ? 'selected' : '' }}>
                                                    Faculty of Science and Technology</option>
                                                <option value="FBM" {{ $admin['faculty'] == 'FBM' ? 'selected' : '' }}>
                                                    Faculty of Business and Management</option>
                                            </select>
                                            @error('faculty')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Buttons -->
                            <div
                                class="mt-8 pt-6 border-t border-gray-200 flex flex-col sm:flex-row sm:justify-between gap-3">
                                <button type="submit" id="updateBtn" class="inline-flex justify-center items-center px-6 py-3 bg-indigo-600 text-white rounded-lg
                                           hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2
                                           font-medium transition-all disabled:opacity-50 disabled:cursor-not-allowed">
                                    <i class="fas fa-save mr-2" id="saveIcon"></i>
                                    <i class="fas fa-spinner fa-spin mr-2 hidden" id="loadingIcon"></i>
                                    <span id="btnText">Update Administrator</span>
                                </button>

                                <div class="flex gap-3">
                                    <a href="{{ route('superadmin.admin-list') }}" class="inline-flex justify-center items-center px-6 py-3 bg-white text-gray-700 rounded-lg
                                               border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 
                                               focus:ring-gray-200 font-medium transition-all">
                                        <i class="fas fa-arrow-left mr-2"></i>Back to List
                                    </a>

                                    <button type="button" onclick="confirmDelete()" class="inline-flex justify-center items-center px-6 py-3 bg-white text-red-600 rounded-lg
                                               border border-red-200 hover:bg-red-50 focus:outline-none focus:ring-2 
                                               focus:ring-red-200 font-medium transition-all">
                                        <i class="fas fa-trash-alt mr-2"></i>Delete
                                    </button>
                                </div>
                            </div>
                        </form>

                        <!-- Delete Form (Hidden) -->
                        <form id="deleteForm" action="{{ route('admin.delete', ['adminId' => $admin['id']]) }}"
                            method="POST" class="hidden">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>

                    <!-- Info Card -->
                    <div class="mt-6 bg-yellow-50 rounded-lg p-4 border border-yellow-100">
                        <h3 class="text-sm font-medium text-yellow-800 flex items-center">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Important Notes
                        </h3>
                        <ul class="mt-2 text-sm text-yellow-700 space-y-1">
                            <li>• Changing the email address will update login credentials</li>
                            <li>• Role changes affect system permissions immediately</li>
                            <li>• Faculty assignment determines accessible resources</li>
                            <li>• Deletion cannot be undone - use with caution</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Handle form submission with loading indicator
        const form = document.querySelector('form');
        const updateBtn = document.getElementById('updateBtn');
        const saveIcon = document.getElementById('saveIcon');
        const loadingIcon = document.getElementById('loadingIcon');
        const btnText = document.getElementById('btnText');

        form.addEventListener('submit', function (e) {
            // Show loading state
            updateBtn.disabled = true;
            saveIcon.classList.add('hidden');
            loadingIcon.classList.remove('hidden');
            btnText.textContent = 'Updating...';
        });

        function confirmDelete() {
            if (confirm('Are you sure you want to delete this administrator? This action cannot be undone.')) {
                document.getElementById('deleteForm').submit();
            }
        }

        // Role-based alerts
        document.getElementById('role').addEventListener('change', function () {
            const role = this.value;
            const alerts = {
                'admin': 'Admin role provides faculty-level management capabilities.',
                'dean': 'Dean role includes oversight and approval responsibilities.',
                'genadmin': 'General Admin role grants system-wide management access.'
            };

            // You can add dynamic alerts here if needed
        });
    </script>

</body>

</html>