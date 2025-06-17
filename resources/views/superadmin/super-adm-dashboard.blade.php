<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-50">

    @include('partials.super-admin-navbar')

    <div class="p-4 sm:ml-64 mt-20">
        <!-- Dashboard Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold flex items-center">
                            <i class="fas fa-crown mr-3"></i>Super Admin Dashboard
                        </h1>
                        <p class="text-purple-100 mt-2">Complete system overview and management</p>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-purple-100">Last Login</div>
                        <div class="text-lg font-semibold">{{ date('M d, Y H:i') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Users -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Users</p>
                        <p class="text-3xl font-bold text-gray-900">1,247</p>
                        <p class="text-sm text-green-600 flex items-center mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>12% this month
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-users text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Total Exams -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Past Exams</p>
                        <p class="text-3xl font-bold text-gray-900">2,856</p>
                        <p class="text-sm text-green-600 flex items-center mt-1">
                            <i class="fas fa-arrow-up mr-1"></i>8% this month
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-file-alt text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- Active Admins -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Active Admins</p>
                        <p class="text-3xl font-bold text-gray-900">23</p>
                        <p class="text-sm text-blue-600 flex items-center mt-1">
                            <i class="fas fa-circle mr-1"></i>Online now
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-user-shield text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>

            <!-- System Health -->
            <div class="bg-white rounded-lg shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">System Health</p>
                        <p class="text-3xl font-bold text-green-600">98.5%</p>
                        <p class="text-sm text-green-600 flex items-center mt-1">
                            <i class="fas fa-check-circle mr-1"></i>All systems operational
                        </p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-heartbeat text-purple-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions & Statistics Row -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Quick Actions -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-bolt text-yellow-500 mr-2"></i>Quick Actions
                </h3>
                <div class="space-y-3">
                    <button
                        class="w-full bg-blue-600 text-white p-3 rounded-lg hover:bg-blue-700 transition-all flex items-center">
                        <i class="fas fa-user-plus mr-3"></i>Create New Admin
                    </button>
                    <button
                        class="w-full bg-green-600 text-white p-3 rounded-lg hover:bg-green-700 transition-all flex items-center">
                        <i class="fas fa-university mr-3"></i>Add Faculty
                    </button>
                    <button
                        class="w-full bg-purple-600 text-white p-3 rounded-lg hover:bg-purple-700 transition-all flex items-center">
                        <i class="fas fa-cog mr-3"></i>System Settings
                    </button>
                    <button
                        class="w-full bg-red-600 text-white p-3 rounded-lg hover:bg-red-700 transition-all flex items-center">
                        <i class="fas fa-database mr-3"></i>Backup System
                    </button>
                </div>
            </div>

            <!-- System Overview -->
            <div class="bg-white rounded-lg shadow-md p-6 lg:col-span-2">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-chart-pie text-blue-500 mr-2"></i>System Overview
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-blue-600 font-medium">Daily Uploads</p>
                                <p class="text-2xl font-bold text-blue-800">24</p>
                            </div>
                            <i class="fas fa-upload text-blue-500 text-2xl"></i>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-green-600 font-medium">Downloads Today</p>
                                <p class="text-2xl font-bold text-green-800">156</p>
                            </div>
                            <i class="fas fa-download text-green-500 text-2xl"></i>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-purple-600 font-medium">Active Sessions</p>
                                <p class="text-2xl font-bold text-purple-800">42</p>
                            </div>
                            <i class="fas fa-users text-purple-500 text-2xl"></i>
                        </div>
                    </div>
                    <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-yellow-600 font-medium">Server Uptime</p>
                                <p class="text-2xl font-bold text-yellow-800">99.9%</p>
                            </div>
                            <i class="fas fa-server text-yellow-500 text-2xl"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Management Sections -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
            <!-- User Management -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center justify-between">
                    <span><i class="fas fa-users-cog text-blue-500 mr-2"></i>User Management</span>
                    <a href="#" class="text-blue-600 text-sm hover:text-blue-800">View All</a>
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user-shield text-blue-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">Admins</p>
                                <p class="text-sm text-gray-600">23 active users</p>
                            </div>
                        </div>
                        <button class="bg-blue-100 text-blue-700 px-3 py-1 rounded-lg text-sm hover:bg-blue-200">
                            Manage
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-chalkboard-teacher text-green-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">Lecturers</p>
                                <p class="text-sm text-gray-600">456 registered</p>
                            </div>
                        </div>
                        <button class="bg-green-100 text-green-700 px-3 py-1 rounded-lg text-sm hover:bg-green-200">
                            Manage
                        </button>
                    </div>
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div class="flex items-center">
                            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-user-graduate text-purple-600"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-800">Students</p>
                                <p class="text-sm text-gray-600">768 active</p>
                            </div>
                        </div>
                        <button class="bg-purple-100 text-purple-700 px-3 py-1 rounded-lg text-sm hover:bg-purple-200">
                            Manage
                        </button>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-server text-green-500 mr-2"></i>System Status
                </h3>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">Database</span>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                            <i class="fas fa-check-circle mr-1"></i>Healthy
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">File Storage</span>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                            <i class="fas fa-check-circle mr-1"></i>85% Free
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">API Response</span>
                        <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded-full text-xs font-medium">
                            <i class="fas fa-clock mr-1"></i>245ms
                        </span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">Backup Status</span>
                        <span class="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                            <i class="fas fa-check-circle mr-1"></i>Last: 2 hours ago
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities & Alerts -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Activities -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-history text-blue-500 mr-2"></i>Recent Activities
                </h3>
                <div class="space-y-4">
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-user-plus text-blue-600 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800">New admin created: John Smith</p>
                            <p class="text-xs text-gray-500">2 hours ago</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-file-alt text-green-600 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800">Exam uploaded: Mathematics 2024</p>
                            <p class="text-xs text-gray-500">4 hours ago</p>
                        </div>
                    </div>
                    <div class="flex items-start space-x-3">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-trash text-red-600 text-xs"></i>
                        </div>
                        <div class="flex-1">
                            <p class="text-sm text-gray-800">Exam deleted: Physics 2023</p>
                            <p class="text-xs text-gray-500">6 hours ago</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Alerts -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-exclamation-triangle text-yellow-500 mr-2"></i>System Alerts
                </h3>
                <div class="space-y-4">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                        <div class="flex items-center">
                            <i class="fas fa-warning text-yellow-600 mr-2"></i>
                            <span class="text-sm font-medium text-yellow-800">Storage Warning</span>
                        </div>
                        <p class="text-xs text-yellow-700 mt-1">File storage is 85% full. Consider cleanup.</p>
                    </div>
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                        <div class="flex items-center">
                            <i class="fas fa-info-circle text-blue-600 mr-2"></i>
                            <span class="text-sm font-medium text-blue-800">System Update</span>
                        </div>
                        <p class="text-xs text-blue-700 mt-1">New system update available v2.1.0</p>
                    </div>
                    <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle text-green-600 mr-2"></i>
                            <span class="text-sm font-medium text-green-800">Backup Complete</span>
                        </div>
                        <p class="text-xs text-green-700 mt-1">Daily backup completed successfully</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>