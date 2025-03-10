<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Control</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
</head>

<body>
    @include('partials.super-admin-navbar')

    <div class="p-4 sm:ml-64 mt-20">
        <h1 class="text-3xl font-bold text-gray-800">👨‍🏫 Lecturer Management</h1>
        <p class="text-gray-600">Enable or disable lecturer accounts.</p>

        <div class="overflow-x-auto bg-white p-6 rounded-lg shadow-lg mt-6">
            @if(count($lecturerList) > 0)
                <table class="min-w-full border border-gray-300 rounded-lg shadow-md">
                    <thead class="bg-gray-800 text-white">
                        <tr>
                            <th class="py-3 px-4 text-left">Name</th>
                            <th class="py-3 px-4 text-left">Email</th>
                            <th class="py-3 px-4 text-center">Status</th>
                            <th class="py-3 px-4 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($lecturerList as $lecturer)
                            <tr class="border-b hover:bg-gray-100 transition">
                                <td class="py-3 px-4 font-semibold text-gray-700">{{ $lecturer['name'] }}</td>
                                <td class="py-3 px-4 text-gray-600">{{ $lecturer['email'] }}</td>
                                <td
                                    class="py-3 px-4 text-center font-bold {{ $lecturer['status'] ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $lecturer['status'] ? 'Disabled' : 'Active' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <button onclick="toggleLecturer('{{ $lecturer['id'] }}', this)"
                                        class="toggle-btn bg-{{ $lecturer['status'] ? 'green' : 'red' }}-500 hover:bg-{{ $lecturer['status'] ? 'green' : 'red' }}-700 text-white px-4 py-2 rounded-md">
                                        {{ $lecturer['status'] ? 'Enable' : 'Disable' }}
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-600 text-center mt-6">No lecturers found.</p>
            @endif
        </div>
    </div>

    <!-- ✅ JavaScript for Toggle Action -->
    <script>
    async function toggleLecturer(id, btn) {
        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute("content");

            const response = await fetch(`/superadmin/toggle-lecturer/${id}`, {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": csrfToken
                },
                credentials: "same-origin"
            });

            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`Server Error: ${errorText}`);
            }

            const data = await response.json();

            if (data.error) {
                alert("Error: " + data.error);
                return;
            }

            // **Correct Toggle Logic**
            const isDisabled = data.status; // Firestore now correctly returns true/false
            btn.textContent = isDisabled ? "Enable" : "Disable";

            btn.classList.toggle("bg-red-500", !isDisabled);
            btn.classList.toggle("bg-green-500", isDisabled);
            btn.classList.toggle("hover:bg-red-700", !isDisabled);
            btn.classList.toggle("hover:bg-green-700", isDisabled);

            let statusCell = btn.parentNode.previousElementSibling;
            statusCell.textContent = isDisabled ? "Disabled " : "Active ";
            statusCell.classList.toggle("text-red-600", isDisabled);
            statusCell.classList.toggle("text-green-600", !isDisabled);

            console.log(`✅ User ${id} was ${isDisabled ? "DISABLED ❌" : "ENABLED ✅"}`);
        } catch (error) {
            console.error("❌ Error toggling lecturer status:", error);
            alert("Something went wrong! Please check the console.");
        }
    }

    </script>

</body>

</html>