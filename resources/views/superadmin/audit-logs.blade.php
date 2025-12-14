<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Audit Logs | IUEA Admin</title>
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
            <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-clipboard-list text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">Audit Logs</h1>
                            <p class="text-purple-100 mt-1">Track all system activities and user actions</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-purple-200">Total Logs</p>
                        <p class="text-2xl font-bold">{{ count($logs) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full mr-4">
                        <i class="fas fa-sign-in-alt text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Successful Logins</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['login_success'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-red-500">
                <div class="flex items-center">
                    <div class="p-3 bg-red-100 rounded-full mr-4">
                        <i class="fas fa-exclamation-triangle text-red-600"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Failed Logins</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['login_failed'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full mr-4">
                        <i class="fas fa-file-upload text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Exams Uploaded</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['exam_created'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="p-3 bg-yellow-100 rounded-full mr-4">
                        <i class="fas fa-download text-yellow-600"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Downloads</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $stats['downloads'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <form method="GET" action="{{ route('superadmin.audit-logs') }}" class="flex flex-wrap gap-4 items-end">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Action Type</label>
                    <select name="action"
                        class="w-full rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500">
                        <option value="">All Actions</option>
                        <option value="login_success" {{ request('action') == 'login_success' ? 'selected' : '' }}>Login
                            Success</option>
                        <option value="login_failed" {{ request('action') == 'login_failed' ? 'selected' : '' }}>Login
                            Failed</option>
                        <option value="logout" {{ request('action') == 'logout' ? 'selected' : '' }}>Logout</option>
                        <option value="exam_created" {{ request('action') == 'exam_created' ? 'selected' : '' }}>Exam
                            Created</option>
                        <option value="exam_approved" {{ request('action') == 'exam_approved' ? 'selected' : '' }}>Exam
                            Approved</option>
                        <option value="exam_declined" {{ request('action') == 'exam_declined' ? 'selected' : '' }}>Exam
                            Declined</option>
                        <option value="past_exam_uploaded" {{ request('action') == 'past_exam_uploaded' ? 'selected' : '' }}>Past Exam Uploaded</option>
                        <option value="user_created" {{ request('action') == 'user_created' ? 'selected' : '' }}>User
                            Created</option>
                        <option value="user_disabled" {{ request('action') == 'user_disabled' ? 'selected' : '' }}>User
                            Disabled</option>
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">User Email</label>
                    <input type="text" name="user_email" value="{{ request('user_email') }}"
                        placeholder="Filter by email..."
                        class="w-full rounded-lg border-gray-300 focus:ring-purple-500 focus:border-purple-500">
                </div>
                <div>
                    <button type="submit"
                        class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition">
                        <i class="fas fa-filter mr-2"></i>Filter
                    </button>
                </div>
                <div>
                    <a href="{{ route('superadmin.audit-logs') }}"
                        class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition">
                        <i class="fas fa-times mr-2"></i>Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Audit Logs Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Timestamp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Action</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Resource</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                IP Address</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Details</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($logs as $log)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if(isset($log['timestamp']))
                                        {{ $log['timestamp']->get()->format('Y-m-d H:i:s') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div
                                            class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-purple-600 font-medium text-sm">
                                                {{ strtoupper(substr($log['user_name'] ?? 'U', 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $log['user_name'] ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-500">{{ $log['user_email'] ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $actionColors = [
                                            'login_success' => 'bg-green-100 text-green-800',
                                            'login_failed' => 'bg-red-100 text-red-800',
                                            'logout' => 'bg-gray-100 text-gray-800',
                                            'exam_created' => 'bg-blue-100 text-blue-800',
                                            'exam_approved' => 'bg-green-100 text-green-800',
                                            'exam_declined' => 'bg-orange-100 text-orange-800',
                                            'past_exam_uploaded' => 'bg-indigo-100 text-indigo-800',
                                            'user_created' => 'bg-purple-100 text-purple-800',
                                            'user_disabled' => 'bg-red-100 text-red-800',
                                            'user_enabled' => 'bg-green-100 text-green-800',
                                        ];
                                        $color = $actionColors[$log['action'] ?? ''] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                        {{ str_replace('_', ' ', ucfirst($log['action'] ?? 'unknown')) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if(isset($log['resource_type']))
                                        <span class="font-medium">{{ ucfirst($log['resource_type']) }}</span>
                                        @if(isset($log['resource_name']))
                                            <br><span class="text-xs">{{ $log['resource_name'] }}</span>
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $log['ip_address'] ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <button onclick="showDetails('{{ json_encode($log['details'] ?? []) }}')"
                                        class="text-purple-600 hover:text-purple-900">
                                        <i class="fas fa-eye"></i> View
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-clipboard-list text-4xl mb-4 text-gray-300"></i>
                                    <p>No audit logs found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div id="detailsModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4">
            <div class="fixed inset-0 bg-black opacity-50" onclick="closeModal()"></div>
            <div class="relative bg-white rounded-lg shadow-xl max-w-2xl w-full p-6">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Log Details</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <pre id="detailsContent" class="bg-gray-100 p-4 rounded-lg overflow-x-auto text-sm"></pre>
            </div>
        </div>
    </div>

    <script>
        function showDetails(details) {
            try {
                const parsed = JSON.parse(details);
                document.getElementById('detailsContent').textContent = JSON.stringify(parsed, null, 2);
            } catch (e) {
                document.getElementById('detailsContent').textContent = details;
            }
            document.getElementById('detailsModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('detailsModal').classList.add('hidden');
        }
    </script>
</body>

</html>