<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Administrator - Super Admin</title>
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
                        <div class="bg-gradient-to-r from-green-600 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                            <h1 class="text-2xl font-bold flex items-center">
                                <i class="fas fa-user-plus mr-3"></i>Add New Administrator
                            </h1>
                            <p class="text-green-100 mt-2">Create a new administrator account with role-based
                                permissions
                            </p>
                        </div>
                    </div>

                    <!-- Main Card -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden">
                        <!-- Form -->
                        <form action="{{ route('upload.admin') }}" method="POST" class="p-6">
                            @csrf

                            <div class="space-y-6">
                                <!-- Personal Information Section -->
                                <div class="border-b border-gray-200 pb-6">
                                    <h2 class="text-lg font-semibold text-gray-800 mb-4">Personal Information</h2>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        <!-- First Name -->
                                        <div>
                                            <label for="firstName" class="block text-sm font-medium text-gray-700 mb-1">
                                                <i class="fas fa-user mr-2 text-gray-500"></i>First Name
                                            </label>
                                            <input type="text" id="firstName" name="firstName" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                                placeholder="Enter first name">
                                            @error('firstName')
                                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                            @enderror
                                        </div>

                                        <!-- Last Name -->
                                        <div>
                                            <label for="lastName" class="block text-sm font-medium text-gray-700 mb-1">
                                                <i class="fas fa-user mr-2 text-gray-500"></i>Last Name
                                            </label>
                                            <input type="text" id="lastName" name="lastName" required
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                                placeholder="Enter last name">
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
                                        <input type="email" id="email" name="email" required
                                            class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                                            placeholder="Enter email address">
                                        @error('email')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Password -->
                                    <div class="mt-6">
                                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
                                            <i class="fas fa-lock mr-2 text-gray-500"></i>Default Password
                                        </label>
                                        <div class="relative">
                                            <input type="password" id="password" name="password" value="000000" readonly
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm bg-gray-50 text-gray-600 cursor-not-allowed"
                                                placeholder="Default password">
                                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                                <i class="fas fa-info-circle text-blue-400"></i>
                                            </div>
                                        </div>
                                        <div
                                            class="mt-1 text-sm text-blue-600 bg-blue-50 p-2 rounded border border-blue-200">
                                            <i class="fas fa-info-circle mr-1"></i>
                                            <strong>Default Password:</strong> 000000 (Administrator will be prompted to
                                            change on first login)
                                        </div>
                                        @error('password')
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
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                                <option value="">Select Role</option>
                                                <option value="admin">Admin</option>
                                                <option value="dean">Dean</option>
                                                <option value="genadmin">General Admin</option>
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
                                                class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all">
                                                <option value="">Select Faculty</option>
                                                <option value="HEC">Higher Education Certificate</option>
                                                <option value="FOE">Faculty of Engineering</option>
                                                <option value="FOL">Faculty of Law</option>
                                                <option value="FST">Faculty of Science and Technology</option>
                                                <option value="FBM">Faculty of Business and Management</option>
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
                                <button type="submit" class="inline-flex justify-center items-center px-6 py-3 bg-blue-600 text-white rounded-lg
                                           hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2
                                           font-medium transition-all">
                                    <i class="fas fa-user-plus mr-2"></i>Create Administrator
                                </button>

                                <div class="flex gap-3">
                                    <a href="{{ route('superadmin.admin-list') }}" class="inline-flex justify-center items-center px-6 py-3 bg-white text-gray-700 rounded-lg
                                               border border-gray-300 hover:bg-gray-50 focus:outline-none focus:ring-2 
                                               focus:ring-gray-200 font-medium transition-all">
                                        <i class="fas fa-times mr-2"></i>Cancel
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Tips Card -->
                    <div class="mt-6 bg-blue-50 rounded-lg p-4 border border-blue-100">
                        <h3 class="text-sm font-medium text-blue-800 flex items-center">
                            <i class="fas fa-info-circle mr-2"></i>Administrator Guidelines
                        </h3>
                        <ul class="mt-2 text-sm text-blue-700 space-y-1">
                            <li>• Choose appropriate roles based on required permissions</li>
                            <li>• Deans have faculty-specific administrative rights</li>
                            <li>• General Admins have system-wide management capabilities</li>
                            <li>• All administrators can manage their assigned faculty resources</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordToggle = document.getElementById('passwordToggle');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordToggle.classList.remove('fa-eye');
                passwordToggle.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordToggle.classList.remove('fa-eye-slash');
                passwordToggle.classList.add('fa-eye');
            }
        }

        // Role-based hints
        document.getElementById('role').addEventListener('change', function () {
            const role = this.value;
            const tips = {
                'admin': 'Admins can manage lecturers, courses, and past papers within their faculty.',
                'dean': 'Deans have oversight responsibilities and approval rights for their faculty.',
                'genadmin': 'General Admins have system-wide management capabilities across all faculties.'
            };

            // You can add dynamic help text here if needed
        });
    </script>

</body>

</html>