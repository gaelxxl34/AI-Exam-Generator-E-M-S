<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Lecturer List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet">


</style>
</head>
<body>
     @include('partials.admin-navbar')
<div class="p-4 sm:ml-64 mt-20">
    @if (count($lecturersByFaculty) > 0)
        <div class="container mx-auto mt-3">
            <div class="table-responsive">
                @foreach ($lecturersByFaculty as $faculty => $lecturers)
                    <div class="mt-8">
                        <h2 class="text-center text-xl font-bold mb-4">{{ $faculty }}</h2>
                        <table class="min-w-full table-auto leading-normal">
                            <thead class="bg-gray-800 text-white">
                                <tr>
                                    <th scope="col" class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold uppercase tracking-wider">
                                        Picture
                                    </th>
                                    <th scope="col" class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold uppercase tracking-wider">
                                        First Name
                                    </th>
                                    <th scope="col" class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold uppercase tracking-wider">
                                        Last Name
                                    </th>
                                    <th scope="col" class="px-5 py-3 border-b-2 border-gray-200 text-left text-xs font-semibold uppercase tracking-wider">
                                        Email Address
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($lecturers as $lecturer)
                                <tr>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        @if ($lecturer['profile_picture'])
                                            <img src="{{ $lecturer['profile_picture'] }}" alt="User" class="rounded-full" style="width: 55px; height: 55px; object-fit: cover;">
                                        @else
                                            <span>No Picture</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        {{ $lecturer['firstName'] }}
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        {{ $lecturer['lastName'] }}
                                    </td>
                                    <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                        <div class="flex items-center justify-between">
                                            {{ $lecturer['email'] }}
                                            <!-- Edit button -->
                                            <a href="{{ route('editLecturer', ['id' => $lecturer['id']]) }}" class="text-red-500 hover:text-red-700 ml-4">
                                                <i class="fas fa-pen"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-5 text-center border-b border-gray-200 bg-white text-sm">
                                        <div class="flex flex-col items-center justify-center">
                                            <img src="../assets/img/404.jpeg" alt="No Data Available" class="w-1/2 max-w-md mx-auto">
                                            <p class="mt-4 text-lg font-semibold text-gray-600">No lecturer data found.</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                @endforeach

            </div>
        </div>
    @else
        <div class="mt-8 flex flex-col items-center justify-center">
            <img src="../assets/img/404.jpeg" alt="No Data Available" class="w-1/2 max-w-sm mx-auto">
            <p class="mt-4 text-lg font-semibold text-gray-600">No lecturer available.</p>
        </div>
    @endif

</div>


    
</body>
</html>