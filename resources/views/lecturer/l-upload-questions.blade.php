<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Upload Exam Questions</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>


</head>
<body>
    @include('partials.lecturer-navbar')

<div class="p-4 sm:ml-64 mt-20">
    <form action="{{ route('upload.exam') }}" method="post" class="block w-full p-2 border border-gray-300 rounded-md">
        @csrf <!-- Laravel CSRF token -->

        <input type="hidden" name="faculty" value="fst"> <!-- Set faculty as 'fst' -->

        <div>
            <label for="courseUnit" class="block text-sm font-medium text-gray-700">Course Unit</label>
            <select id="courseUnit" name="courseUnit" class="block w-full p-2 border border-gray-300 rounded-md mb-4">
                <option value="">Select a course unit</option>
                <option value="Data structures and algorithm">Data structures and algorithm</option>
                <option value="Problem solving">Problem solving</option>
                <option value="System analysis and design">System analysis and design</option>

            </select>
        </div>

        <div>
            <label for="formSelect" class="block text-sm font-medium text-gray-700">Format</label>
            <select id="formSelect" name="format" class="block w-full p-2 border border-gray-300 rounded-md mb-4">
                <option value="">Select an option</option>
                <option value="AB">A-B</option>
                <option value="ABC">A-B-C</option>
            </select>
        </div>

        <div id="sectionA" class="hidden mt-5">
            <label for="dropdownA" class="block text-sm font-medium text-gray-700">Section A</label>
            <select id="dropdownA" class="block w-full p-2 border border-gray-300 rounded-md mb-4">
                <option value="">Select number of fields for section A</option>
                <!-- Populate with JavaScript -->
            </select>
            <div id="inputFieldsA"></div>
        </div>

        <div id="sectionB" class="hidden mt-5">
            <label for="dropdownB" class="block text-sm font-medium text-gray-700">Section B</label>
            <select id="dropdownB" class="block w-full p-2 border border-gray-300 rounded-md mb-4">
                <option value="">Select number of fields for section B</option>
                <!-- Populate with JavaScript -->
            </select>
            <div id="inputFieldsB"></div>
        </div>

        <div id="sectionC" class="hidden mt-5">
            <label for="dropdownC" class="block text-sm font-medium text-gray-700">Section C</label>
            <select id="dropdownC" class="block w-full p-2 border border-gray-300 rounded-md mb-4">
                <option value="">Select number of fields for section C</option>
                <!-- Populate with JavaScript -->
            </select>
            <div id="inputFieldsC"></div>
        </div>

        <button type="submit" class="mt-4 px-4 py-2 bg-gray-800 hover:bg-red-700 text-white rounded-md w-full">
            Submit
        </button>
    </form>
</div>

<script>
    document.getElementById('formSelect').addEventListener('change', function() {
        var value = this.value;
        
        document.getElementById('sectionA').classList.toggle('hidden', value !== 'AB' && value !== 'ABC');
        document.getElementById('sectionB').classList.toggle('hidden', value !== 'AB' && value !== 'ABC');
        document.getElementById('sectionC').classList.toggle('hidden', value !== 'ABC');

        populateDropdown('dropdownA', 20); // Max 20 for Section A
        populateDropdown('dropdownB', 5);  // Max 5 for Section B
        if (value === 'ABC') {
            populateDropdown('dropdownC', 5); // Max 5 for Section C
        }
    });

    function populateDropdown(dropdownId, maxFields) {
        const dropdown = document.getElementById(dropdownId);
        dropdown.innerHTML = '<option value="">Select number of questions</option>'; // Reset

        for (let i = 1; i <= maxFields; i++) {
            const option = new Option(i, i);
            dropdown.add(option);
        }

        dropdown.addEventListener('change', function() {
            createInputFields(dropdownId.replace('dropdown', 'inputFields'), this.value);
        });
    }

    function createInputFields(containerId, numberOfFields) {
        const container = document.getElementById(containerId);
        const sectionId = containerId.charAt(containerId.length - 1); // Get 'A', 'B', or 'C' from the ID
        container.innerHTML = ''; // Clear previous fields

        for (let i = 1; i <= numberOfFields; i++) {
            const input = document.createElement('input');
            input.type = 'text';
            input.name = `section${sectionId}[]`; // Name corrected to match backend expectation
            input.placeholder = `Field ${sectionId}${i}`;
            input.className = 'block w-full p-2 border border-gray-300 rounded-md mb-4';
            container.appendChild(input);
        }
    }

</script>


</body>
</html>