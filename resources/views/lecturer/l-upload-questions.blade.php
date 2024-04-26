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

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<style>
    
</style>

</head>
<body>
    @include('partials.lecturer-navbar')

<div class="p-4 sm:ml-64 mt-20 flex justify-center">
    <!-- Set w-full for small screens and w-3/5 for medium screens and up -->
    <form id="uploadForm" enctype="multipart/form-data" action="{{ route('upload.exam') }}" method="post" class="w-full sm:w-3/5 p-2 border border-gray-300 rounded-md">
        @csrf <!-- Laravel CSRF token -->

        <div>
            <label for="courseUnit" class="block text-sm font-medium text-gray-700">Course Unit</label>
            <select id="courseUnit" name="courseUnit" class="block w-full p-2 border border-gray-300 rounded-md mb-4">
                <option value="">Select a course unit</option>
                @foreach ($courses as $course)
                    <option value="{{ $course }}">{{ $course }}</option>
                @endforeach
            </select>
        </div>


        <div>
            <label for="formSelect" class="block text-sm font-medium text-gray-700">Format</label>
            <select id="formSelect" name="format" class="block w-full p-2 border border-gray-300 rounded-md mb-4">
                <option value="">Select an option</option>
                <option value="AB">A-B</option>
                <option value="Practical">Practical Exam / Unique Section</option>
            </select>
        </div>




        <div id="practicalOptions" class="hidden mt-5">
            <label for="practicalSectionCount" class="block text-sm font-medium text-gray-700">Number of Sections for Practical Exam</label>
            <select id="practicalSectionCount" class="block w-full p-2 border border-gray-300 rounded-md mb-4">
                <option value="A">Only Section A</option>
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

   <div id="instructionsContainer" class="hidden mt-5">
            <!-- Instructions fields populated by JavaScript -->
        </div>


        <div class="mb-4">
            <label for="fileUpload" class="block text-sm font-medium text-gray-900 dark:text-gray-500">Upload marking guide</label>
            <input type="file" id="fileUpload" name="fileUpload" accept=".pdf" class="block w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 cursor-pointer dark:text-gray-400 focus:outline-none dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400" required>
            
            @if ($errors->has('fileUpload'))
                <div class="text-red-500 mt-2 text-sm">
                    {{ $errors->first('fileUpload') }}
                </div>
            @endif
        </div>


        <div class="flex justify-center">
            <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-red-700 text-white rounded-md">
                Submit
            </button>
        </div>
    </form>
</div>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formSelect = document.getElementById('formSelect');
        const practicalSectionCount = document.getElementById('practicalSectionCount');

        formSelect.addEventListener('change', function() {
            const value = this.value;
            
            // Toggle visibility based on selected format
            document.getElementById('sectionA').classList.toggle('hidden', value !== 'AB' && value !== 'Practical');
            document.getElementById('sectionB').classList.toggle('hidden', value !== 'AB');
            document.getElementById('practicalOptions').classList.toggle('hidden', value !== 'Practical');

            populateDropdown('dropdownA', 20); // Max 20 for Section A

            if (value === 'AB' || value === 'Practical') {
                populateDropdown('dropdownB', 5); // Max 5 for Section B
            }
        });

        practicalSectionCount.addEventListener('change', function() {
            const sectionCount = this.value;

            document.getElementById('sectionA').classList.remove('hidden');
            document.getElementById('sectionB').classList.toggle('hidden', sectionCount !== 'AB');

            populateDropdown('dropdownA', 20); // Adjust if needed
            if (sectionCount === 'AB') {
                populateDropdown('dropdownB', 5);
            }
        });

        function populateDropdown(dropdownId, maxFields) {
            const dropdown = document.getElementById(dropdownId);
            dropdown.innerHTML = '<option value="">Select number of questions</option>';

            for (let i = 1; i <= maxFields; i++) {
                const option = new Option(i, i);
                dropdown.add(option);
            }

            dropdown.removeEventListener('change', handleDropdownChange); // Remove any existing event listener
            dropdown.addEventListener('change', handleDropdownChange);
        }

        function handleDropdownChange() {
            const section = this.id.charAt(this.id.length - 1);
            createInputFields(`inputFields${section}`, this.value);
        }

        function createInputFields(containerId, numberOfFields) {
            const container = document.getElementById(containerId);
            container.innerHTML = ''; // Clear previous fields

            for (let i = 1; i <= numberOfFields; i++) {
                const sectionId = containerId.charAt(containerId.length - 1);
                const editorId = `${containerId}Editor${i}`;

                const editorContainer = document.createElement('div');
                editorContainer.id = editorId;
                editorContainer.className = 'summernote-editor';

                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = `section${sectionId}[]`;

                container.appendChild(editorContainer);
                container.appendChild(hiddenInput);

                $(`#${editorId}`).summernote({
                    placeholder: `Field ${sectionId}${i}`,
                    tabsize: 2,
                    height: 120,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', [ 'codeview', 'help']]
                    ],
                    callbacks: {
                        onChange: function(contents, $editable) {
                            hiddenInput.value = contents;
                        }
                    }
                });
            }
        }




    });


</script>



<script>
    document.addEventListener('DOMContentLoaded', function() {
        const formSelect = document.getElementById('formSelect');
        const instructionsContainer = document.getElementById('instructionsContainer');

        formSelect.addEventListener('change', function() {
            const format = this.value;
            updateInstructions(format);
        });

        function updateInstructions(format) {
            instructionsContainer.innerHTML = ''; // Clear previous fields
            instructionsContainer.classList.remove('hidden');

            let fieldsInfo;
            if (format === 'AB') {
                fieldsInfo = ['General Instructions', 'Section A Instructions', 'Section B Instructions'];
            } else if (format === 'Practical') {
                fieldsInfo = ['General Instructions', 'Section A Instructions'];
            } else {
                instructionsContainer.classList.add('hidden');
                return;
            }

            fieldsInfo.forEach((info, index) => {
                const inputGroup = document.createElement('div');
                const label = document.createElement('label');
                const input = document.createElement('textarea'); // Changed to textarea for Summernote

                label.textContent = info;
                label.setAttribute('for', `instructions${index}`);
                label.className = 'block text-sm font-medium text-gray-700';

                input.id = `instructions${index}`;
                input.name = `instructions[${index}]`;
                input.className = 'block w-full p-2 border border-gray-300 rounded-md mb-4';

                inputGroup.appendChild(label);
                inputGroup.appendChild(input);
                instructionsContainer.appendChild(inputGroup);

                // Initialize Summernote only for General Instructions
                if (info === 'General Instructions') {
                    $(`#instructions${index}`).summernote({
                        placeholder: info,
                        tabsize: 2,
                        height: 120,
                        toolbar: [
                            ['style', ['style']],
                            ['font', ['bold', 'underline', 'clear']],
                            ['color', ['color']],
                            ['para', ['ul', 'ol', 'paragraph']],
                            ['table', ['table']],
                            ['insert', ['link', 'picture', 'video']],
                            ['view', ['codeview', 'help']]
                        ]
                    });
                }
            });
        }
    });
</script>

</body>
</html>