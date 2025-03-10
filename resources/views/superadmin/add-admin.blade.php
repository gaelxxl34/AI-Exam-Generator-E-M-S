<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Add Admins</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

</head>
<body>
    
    @include('partials.super-admin-navbar')
    <div class="p-4 sm:ml-64 mt-20 flex justify-center">
        <form id="adminForm" action="{{ route('upload.admin') }}" method="post" enctype="multipart/form-data" class="bg-white p-6 rounded-lg shadow-lg w-3/5">

            @csrf

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

            <div class="mb-5"> <!-- increased bottom margin -->
                <label for="facultySelect" class="block text-sm font-medium text-gray-700">Select Faculty:</label>
                <div class="relative">
                    <select id="facultySelect" name="faculty" class="block w-full p-2 border border-gray-300 rounded-md shadow-sm text-gray-700 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" required>
                        <option value="">Please choose</option>
                        <option value="FST">Faculty of Science and Technology (FST)</option>
                        <option value="FBM">Faculty of Business Management (FBM)</option>
                        <option value="FOE">Faculty of Engineering (FOE)</option>
                        <option value="FOL">Faculty of Law (FOL)</option>
                        <option value="HEC">Higher Education Certificate (HEC)</option>
                        <option value="FST,FBM,FOE,FOL,HEC">ALL</option>
                    </select>
                </div>
            </div>

            <div class="mb-5"> <!-- Role dropdown added below faculty dropdown -->
                <label for="roleSelect" class="block text-sm font-medium text-gray-700">Select Role:</label>
                <div class="relative">
                    <select id="roleSelect" name="role" class="block w-full p-2 border border-gray-300 rounded-md shadow-sm text-gray-700 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500" required>
                        <option value="">Please choose</option>
                        <option value="admin">Faculty Admin</option>
                        <option value="genadmin">Exam Generator</option>
                        <option value="dean">Dean</option>
                    </select>
                </div>
            </div>

            <input type="hidden" id="password" name="password" value="000000">

         

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