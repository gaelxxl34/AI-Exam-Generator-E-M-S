<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add Lecturer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

</head>
<body>
    
    @include('partials.admin-navbar')
    <div class="p-4 sm:ml-64 mt-20">

        <form action="{{ route('upload.lecturer') }}" method="post" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-lg">

            @csrf

            <div class="mb-4">
                <label for="image" class="block text-sm font-medium text-gray-700">Profile Picture</label>
                <input required type="file" id="image" name="image" accept="image/*" class="block w-full text-sm text-gray-900 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
            </div>

            <div class="mb-4">
                <label for="firstName" class="block text-sm font-medium text-gray-700">First Name</label>
                <input required type="text" id="firstName" name="firstName" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div class="mb-4">
                <label for="lastName" class="block text-sm font-medium text-gray-700">Last Name</label>
                <input required type="text" id="lastName" name="lastName" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div class="mb-6">
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input required type="email" id="email" name="email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700">Password:</label>
                <input type="password" id="password" name="password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>

            <div class="flex justify-center">
                <button type="submit" class="px-4 py-2 bg-black text-white rounded-md bg-gray-800 hover:bg-red-700">Submit</button>
            </div>

            @if ($errors->has('upload_error'))
                <p class="mt-3 text-danger">{{ $errors->first('upload_error') }}</p>
            @endif

        </form>
        
    </div>

</body>
</html>