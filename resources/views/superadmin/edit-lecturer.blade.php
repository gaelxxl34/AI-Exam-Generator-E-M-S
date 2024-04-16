<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Edit Lecturer</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

</head>
<body>
    
    @include('partials.super-admin-navbar')
    <div class="p-4 sm:ml-64 mt-20">
        <div class="container mx-auto mt-3 mb-3 text-center">
            <div class="flex justify-center items-center">
                <div class="bg-white rounded-lg shadow-lg p-6 w-full max-w-md">
                    <form action="{{ route('admin.update-lecturer-data', ['lecturerId' => $lecturer['id']]) }}" enctype="multipart/form-data" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')

                        <!-- Profile Picture URL -->
                        <div>
                            <label for="profilePicture" class="block text-sm font-medium text-gray-700">Profile Picture</label>
                            <input type="file" id="profilePicture" name="profilePicture" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:border file:border-gray-300 file:rounded-md file:text-sm file:bg-gray-50 file:text-gray-700 hover:file:bg-gray-100">
                            @if ($lecturer['profile_picture'])
                                <img src="{{ $lecturer['profile_picture'] }}" alt="Current Image" class="mt-2 max-h-52 w-auto">
                            @endif
                        </div>
                        
                        <!-- First Name -->
                        <div>
                            <label for="firstName" class="block text-sm font-medium text-gray-700">First Name</label>
                            <input type="text" id="firstName" name="firstName" value="{{ $lecturer['firstName'] }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        <!-- Last Name -->
                        <div>
                            <label for="lastName" class="block text-sm font-medium text-gray-700">Last Name</label>
                            <input type="text" id="lastName" name="lastName" value="{{ $lecturer['lastName'] }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" id="email" name="email" value="{{ $lecturer['email'] }}" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        </div>

                        <!-- Update Button -->
                        <div class="flex justify-center">
                            <button type="submit" class="inline-block w-full px-6 py-2 border border-transparent text-base font-medium rounded-md text-white bg-gray-800 hover:bg-gray-900" onclick="return confirm('Are you sure you want to update the lecturer data?')">Update</button>
                        </div>


                    </form>

                    <!-- Success Message -->
                    @if (session('success'))
                        <div class="mt-2 p-4 bg-green-100 border border-green-400 text-green-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Error Message -->
                    @if (session('error'))
                        <div class="mt-2 p-4 bg-red-100 border border-red-400 text-red-700">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('lecturer.delete', ['lecturerId' => $lecturer['id']]) }}" method="POST" class="mt-2">
                        @csrf
                        @method('DELETE')
                        <div class="flex justify-center">
                            <button type="submit" class="inline-block w-full px-3 py-2 border border-transparent text-base font-medium rounded-md text-white bg-red-600 hover:bg-red-700" onclick="return confirm('Are you sure you want to delete this lecturer?')">Delete</button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

<script>
    function previewImage() {
        var preview = document.getElementById('imagePreview');
        var fileInput = document.getElementById('imageInput');
        var file = fileInput.files[0];
        var reader = new FileReader();

        reader.onloadend = function () {
            preview.src = reader.result;
            preview.style.display = 'block';
        }

        if (file) {
            reader.readAsDataURL(file);
            truncateFileName(fileInput);
        } else {
            preview.src = "";
            preview.style.display = 'none';
        }
    }

    function truncateFileName(input) {
        var fileName = input.files[0].name;
        var maxFileNameLength = Math.floor(input.offsetWidth / 10); // Assuming average character width
        if (fileName.length > maxFileNameLength) {
            var truncatedFileName = fileName.substring(0, maxFileNameLength - 3) + '...';
            input.nextElementSibling.textContent = truncatedFileName;
        } else {
            input.nextElementSibling.textContent = fileName;
        }
    }


</script>
</body>
</html>