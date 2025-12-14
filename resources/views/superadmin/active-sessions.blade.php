<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Active Sessions | IUEA Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-50">
    @include('partials.superadmin-navbar')

    <div class="p-4 sm:ml-64 mt-20">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-green-600 to-teal-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-users text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">Active Sessions</h1>
                            <p class="text-green-100 mt-1">Monitor currently logged-in users</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-green-200">Currently Active</p>
                        <p class="text-2xl font-bold">{{ count($sessions) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full mr-4">
                        <i class="fas fa-desktop text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Desktop</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['by_device']['desktop'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full mr-4">
                        <i class="fas fa-mobile-alt text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Mobile</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['by_device']['mobile'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full mr-4">
                        <i class="fas fa-chalkboard-teacher text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Lecturers</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['by_role']['lecturer'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
                <div class="flex items-center">
                    <div class="p-3 bg-orange-100 rounded-full mr-4">
                        <i class="fas fa-user-shield text-orange-600"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Admins</p>
                        <p class="text-2xl font-bold text-gray-800">
                            {{ ($stats['by_role']['admin'] ?? 0) + ($stats['by_role']['superadmin'] ?? 0) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sessions Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-lg font-semibold text-gray-800">Active User Sessions</h2>
                <form method="POST" action="{{ route('superadmin.cleanup-sessions') }}" class="inline">
                    @csrf
                    <button type="submit"
                        class="px-4 py-2 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition">
                        <i class="fas fa-broom mr-2"></i>Cleanup Stale Sessions
                    </button>
                </form>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Device / Browser</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                IP Address</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Started</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Last Activity</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($sessions as $session)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-green-600 font-medium">
                                                {{ strtoupper(substr($session['user_name'] ?? 'U', 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $session['user_name'] ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-500">{{ $session['user_email'] ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $roleColors = [
                                            'superadmin' => 'bg-red-100 text-red-800',
                                            'admin' => 'bg-purple-100 text-purple-800',
                                            'dean' => 'bg-blue-100 text-blue-800',
                                            'lecturer' => 'bg-green-100 text-green-800',
                                            'genadmin' => 'bg-orange-100 text-orange-800',
                                        ];
                                        $role = $session['user_role'] ?? 'unknown';
                                        $color = $roleColors[$role] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                        {{ ucfirst($role) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center">
                                        @php
                                            $deviceIcon = match ($session['device_type'] ?? 'unknown') {
                                                'desktop' => 'fa-desktop',
                                                'mobile' => 'fa-mobile-alt',
                                                'tablet' => 'fa-tablet-alt',
                                                default => 'fa-question-circle'
                                            };
                                        @endphp
                                        <i class="fas {{ $deviceIcon }} mr-2 text-gray-400"></i>
                                        <div>
                                            <p class="font-medium">{{ $session['browser'] ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-400">{{ $session['os'] ?? '' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $session['ip_address'] ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if(isset($session['started_at']))
                                        {{ $session['started_at']->get()->format('M d, H:i') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if(isset($session['last_activity']))
                                        @php
                                            $lastActivity = $session['last_activity']->get();
                                            $diff = now()->diffInMinutes($lastActivity);
                                        @endphp
                                        <span
                                            class="{{ $diff < 5 ? 'text-green-600' : ($diff < 30 ? 'text-yellow-600' : 'text-red-600') }}">
                                            {{ $diff < 1 ? 'Just now' : $diff . ' min ago' }}
                                        </span>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <form method="POST" action="{{ route('superadmin.terminate-session', $session['id']) }}"
                                        class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Are you sure you want to terminate this session?')"
                                            class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-sign-out-alt"></i> End Session
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-users text-4xl mb-4 text-gray-300"></i>
                                    <p>No active sessions found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>