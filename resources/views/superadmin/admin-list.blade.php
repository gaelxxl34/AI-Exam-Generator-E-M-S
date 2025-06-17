<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Management - Super Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body class="bg-gray-50">
    @include('partials.super-admin-navbar')

    <div class="p-4 sm:ml-64 mt-20">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-bold flex items-center">
                            <i class="fas fa-users-cog mr-3"></i>Admin Management
                        </h1>
                        <p class="text-blue-100 mt-2">Manage system administrators and their roles</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <div class="text-right">
                            <div class="text-sm text-blue-100">Total Admins</div>
                            <div class="text-2xl font-bold">{{ collect($adminsByRole)->flatten(1)->count() }}</div>
                        </div>
                        <a href="{{ route('superadmin.add-admin') }}"
                            class="bg-white text-blue-600 px-4 py-2 rounded-lg hover:bg-gray-100 transition-all flex items-center">
                            <i class="fas fa-plus mr-2"></i>Add Admin
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            @foreach($adminsByRole as $role => $admins)
                <div
                    class="bg-white rounded-lg shadow-md p-6 border-l-4 {{ $role === 'admin' ? 'border-blue-500' : ($role === 'dean' ? 'border-green-500' : 'border-purple-500') }}">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">{{ ucfirst($role) }}s</p>
                            <p class="text-2xl font-bold text-gray-900">{{ count($admins) }}</p>
                        </div>
                        <div
                            class="w-10 h-10 {{ $role === 'admin' ? 'bg-blue-100' : ($role === 'dean' ? 'bg-green-100' : 'bg-purple-100') }} rounded-lg flex items-center justify-center">
                            <i
                                class="fas {{ $role === 'admin' ? 'fa-user-shield text-blue-600' : ($role === 'dean' ? 'fa-graduation-cap text-green-600' : 'fa-crown text-purple-600') }}"></i>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Search and Filter Section -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                        <input type="text" id="searchInput" placeholder="Search by name or email..."
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>
                </div>
                <div class="flex gap-2">
                    <select id="roleFilter"
                        class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        <option value="">All Roles</option>
                        @foreach(array_keys($adminsByRole) as $role)
                            <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                        @endforeach
                    </select>
                    <button onclick="clearFilters()"
                        class="px-4 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-all">
                        <i class="fas fa-times mr-1"></i>Clear
                    </button>
                </div>
            </div>
        </div>

        <!-- Admin Sections by Role -->
        @foreach ($adminsByRole as $role => $admins)
            <div class="role-section mb-8" data-role="{{ $role }}">
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <div
                        class="px-6 py-4 border-b border-gray-200 {{ $role === 'admin' ? 'bg-gradient-to-r from-blue-50 to-blue-100' : ($role === 'dean' ? 'bg-gradient-to-r from-green-50 to-green-100' : 'bg-gradient-to-r from-purple-50 to-purple-100') }}">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                                <i
                                    class="fas {{ $role === 'admin' ? 'fa-user-shield text-blue-600' : ($role === 'dean' ? 'fa-graduation-cap text-green-600' : 'fa-crown text-purple-600') }} mr-2"></i>
                                {{ ucfirst($role) }}s ({{ count($admins) }})
                            </h3>
                            <div class="flex items-center space-x-2">
                                <span
                                    class="text-sm {{ $role === 'admin' ? 'text-blue-600' : ($role === 'dean' ? 'text-green-600' : 'text-purple-600') }}">
                                    {{ count($admins) }} {{ count($admins) === 1 ? 'administrator' : 'administrators' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Administrator
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Email Address
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Role
                                    </th>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($admins as $admin)
                                    <tr class="hover:bg-gray-50 transition-all admin-row"
                                        data-name="{{ strtolower($admin['firstName'] . ' ' . $admin['lastName']) }}"
                                        data-email="{{ strtolower($admin['email']) }}" data-role="{{ $role }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div
                                                    class="w-10 h-10 {{ $role === 'admin' ? 'bg-blue-100' : ($role === 'dean' ? 'bg-green-100' : 'bg-purple-100') }} rounded-full flex items-center justify-center mr-3">
                                                    <span
                                                        class="font-semibold {{ $role === 'admin' ? 'text-blue-600' : ($role === 'dean' ? 'text-green-600' : 'text-purple-600') }}">
                                                        {{ strtoupper(substr($admin['firstName'], 0, 1) . substr($admin['lastName'], 0, 1)) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">
                                                        {{ $admin['firstName'] }} {{ $admin['lastName'] }}
                                                    </div>
                                                    <div class="text-sm text-gray-500">ID: {{ substr($admin['id'], 0, 8) }}...
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">{{ $admin['email'] }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span
                                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $role === 'admin' ? 'bg-blue-100 text-blue-800' : ($role === 'dean' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800') }}">
                                                {{ ucfirst($role) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2">
                                                <a href="{{ route('editAdmin', ['id' => $admin['id']]) }}"
                                                    class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-all">
                                                    <i class="fas fa-edit mr-1"></i>Edit
                                                </a>
                                                <button onclick="viewAdmin('{{ $admin['id'] }}')"
                                                    class="inline-flex items-center px-3 py-2 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-all">
                                                    <i class="fas fa-eye mr-1"></i>View
                                                </button>
                                                <button
                                                    onclick="deleteAdmin('{{ $admin['id'] }}', '{{ $admin['firstName'] }} {{ $admin['lastName'] }}')"
                                                    class="inline-flex items-center px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-all">
                                                    <i class="fas fa-trash mr-1"></i>Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center">
                                            <div class="flex flex-col items-center justify-center">
                                                <i class="fas fa-user-slash text-gray-400 text-4xl mb-4"></i>
                                                <p class="text-lg font-semibold text-gray-600">No {{ $role }}s found</p>
                                                <p class="text-gray-500">No administrators with this role are currently
                                                    registered</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endforeach

        <!-- No Results Message -->
        <div id="noResultsMessage" class="hidden text-center py-12">
            <div class="bg-white rounded-lg shadow-md p-8">
                <i class="fas fa-search text-gray-400 text-6xl mb-4"></i>
                <h3 class="text-xl font-semibold text-gray-600 mb-2">No Results Found</h3>
                <p class="text-gray-500">Try adjusting your search criteria or filters</p>
                <button onclick="clearFilters()"
                    class="mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-all">
                    <i class="fas fa-times mr-2"></i>Clear Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog"
        aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div
                            class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Delete Administrator
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500" id="deleteMessage">
                                    Are you sure you want to delete this administrator? This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" id="confirmDelete"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Delete
                    </button>
                    <button type="button" id="cancelDelete"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Search and filter functionality
        document.getElementById('searchInput').addEventListener('input', filterAdmins);
        document.getElementById('roleFilter').addEventListener('change', filterAdmins);

        function filterAdmins() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const roleFilter = document.getElementById('roleFilter').value;
            const rows = document.querySelectorAll('.admin-row');
            const sections = document.querySelectorAll('.role-section');
            let foundAny = false;

            sections.forEach(section => {
                const sectionRole = section.getAttribute('data-role');
                let sectionHasVisible = false;

                if (!roleFilter || sectionRole === roleFilter) {
                    section.style.display = 'block';

                    const sectionRows = section.querySelectorAll('.admin-row');
                    sectionRows.forEach(row => {
                        const name = row.getAttribute('data-name');
                        const email = row.getAttribute('data-email');

                        const matchesSearch = name.includes(searchTerm) || email.includes(searchTerm);

                        if (matchesSearch) {
                            row.style.display = '';
                            foundAny = true;
                            sectionHasVisible = true;
                        } else {
                            row.style.display = 'none';
                        }
                    });
                } else {
                    section.style.display = 'none';
                }
            });

            // Show/hide no results message
            const noResultsMessage = document.getElementById('noResultsMessage');
            if (noResultsMessage) {
                noResultsMessage.style.display = foundAny ? 'none' : 'block';
            }
        }

        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('roleFilter').value = '';
            filterAdmins();
        }

        // Admin actions
        function viewAdmin(adminId) {
            // Implement view functionality or redirect to view page
            alert('View admin functionality - Admin ID: ' + adminId);
        }

        let adminToDelete = null;

        function deleteAdmin(adminId, adminName) {
            adminToDelete = adminId;
            document.getElementById('deleteMessage').textContent =
                `Are you sure you want to delete ${adminName}? This action cannot be undone.`;
            document.getElementById('deleteModal').classList.remove('hidden');
        }

        document.getElementById('cancelDelete').addEventListener('click', function () {
            document.getElementById('deleteModal').classList.add('hidden');
            adminToDelete = null;
        });

        document.getElementById('confirmDelete').addEventListener('click', function () {
            if (adminToDelete) {
                // Create a form and submit it for deletion
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/${adminToDelete}`;

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = '{{ csrf_token() }}';

                const methodField = document.createElement('input');
                methodField.type = 'hidden';
                methodField.name = '_method';
                methodField.value = 'DELETE';

                form.appendChild(csrfToken);
                form.appendChild(methodField);
                document.body.appendChild(form);
                form.submit();
            }
        });
    </script>
</body>

</html>