<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Download Logs | IUEA Admin</title>
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
            <div class="bg-gradient-to-r from-blue-600 to-cyan-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                            <i class="fas fa-download text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold">Download Logs</h1>
                            <p class="text-blue-100 mt-1">Track all file downloads and views</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-blue-200">Total Downloads</p>
                        <p class="text-2xl font-bold">{{ $summary['total_downloads'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 bg-green-100 rounded-full mr-4">
                        <i class="fas fa-calendar-day text-green-600"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Today</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $summary['today'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-3 bg-blue-100 rounded-full mr-4">
                        <i class="fas fa-calendar-week text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">This Week</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $summary['this_week'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="p-3 bg-purple-100 rounded-full mr-4">
                        <i class="fas fa-calendar-alt text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">This Month</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $summary['this_month'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4 border-l-4 border-orange-500">
                <div class="flex items-center">
                    <div class="p-3 bg-orange-100 rounded-full mr-4">
                        <i class="fas fa-file-pdf text-orange-600"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Past Exams</p>
                        <p class="text-2xl font-bold text-gray-800">{{ $summary['by_file_type']['past_exam'] ?? 0 }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Downloads by Program -->
        @if(isset($summary['by_program']) && count($summary['by_program']) > 0)
            <div class="bg-white rounded-lg shadow-md p-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Downloads by Program</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($summary['by_program'] as $program => $count)
                        <span class="px-3 py-1 bg-blue-100 text-blue-800 rounded-full text-sm font-medium">
                            {{ $program }}: {{ $count }}
                        </span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Downloads Table -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-800">Recent Downloads</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Timestamp</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                User</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                File Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Course / File</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Program</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                IP Address</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($downloads as $download)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if(isset($download['timestamp']))
                                        {{ $download['timestamp']->get()->format('Y-m-d H:i:s') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                            <span class="text-blue-600 font-medium text-sm">
                                                {{ strtoupper(substr($download['user_name'] ?? 'U', 0, 1)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">
                                                {{ $download['user_name'] ?? 'Unknown' }}</p>
                                            <p class="text-xs text-gray-500">{{ $download['user_email'] ?? 'Anonymous' }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $typeColors = [
                                            'past_exam' => 'bg-blue-100 text-blue-800',
                                            'marking_guide' => 'bg-green-100 text-green-800',
                                            'generated_pdf' => 'bg-purple-100 text-purple-800',
                                            'pdf_preview' => 'bg-gray-100 text-gray-800',
                                        ];
                                        $type = $download['file_type'] ?? 'unknown';
                                        $color = $typeColors[$type] ?? 'bg-gray-100 text-gray-800';
                                    @endphp
                                    <span
                                        class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                                        {{ str_replace('_', ' ', ucfirst($type)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $download['course_unit'] ?? $download['file_name'] ?? 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $download['program'] ?? '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $download['ip_address'] ?? 'N/A' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-download text-4xl mb-4 text-gray-300"></i>
                                    <p>No download logs found</p>
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