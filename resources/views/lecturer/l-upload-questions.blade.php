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

        <script src="https://cdn.ckeditor.com/ckeditor5/36.0.1/classic/ckeditor.js"></script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<style>
    
</style>

</head>
<body>
    @include('partials.lecturer-navbar')

<div class="p-4 sm:ml-64 mt-20 flex justify-center">
    <form id="uploadForm" enctype="multipart/form-data" action="{{ route('upload.exam') }}" method="post" class="w-full sm:w-3/5 p-2 border border-gray-300 rounded-md">
        @csrf <!-- Laravel CSRF token -->

        <!-- Display error message for duplicate exam -->
        @if (session('error'))
            <div class="text-red-500 mb-4 text-sm font-bold">
                {{ session('error') }}
            </div>
        @endif

        <!-- Course Unit Dropdown -->
        <div>
            <label for="courseUnit" class="block text-sm font-medium text-gray-700">Course Unit</label>
            <select id="courseUnit" name="courseUnit" class="block w-full p-2 border border-gray-300 rounded-md mb-4" required>
                <option value="">Select a course unit</option>
                @foreach ($courses as $course)
                    <option value="{{ $course['name'] }}" data-faculty="{{ $course['faculty'] }}">
                        {{ $course['name'] }}
                    </option>
                @endforeach
            </select>

            <!-- Faculty Field (Hidden) -->
            <input type="hidden" id="facultyField" name="faculty" value="">

            <!-- Display error if faculty is missing -->
            @if ($errors->has('faculty'))
                <div class="text-red-500 mt-2 text-sm">
                    {{ $errors->first('faculty') }}
                </div>
            @endif
        </div>

        <!-- Hidden Format Input -->
        <input type="hidden" name="format" value="AB">

        <!-- Section A -->
        <div id="sectionA" class="mt-5">
            <label for="dropdownA" class="block text-sm font-medium text-gray-700">Section A</label>
            <select id="dropdownA" name="sectionA_count" class="block w-full p-2 border border-gray-300 rounded-md mb-4" required>
                <option value="">Select number of fields for section A</option>
                <!-- Populated by JavaScript -->
            </select>
            <div id="inputFieldsA"></div>

            @if ($errors->has('sectionA'))
                <div class="text-red-500 mt-2 text-sm">
                    {{ $errors->first('sectionA') }}
                </div>
            @endif
        </div>

        <!-- Section B -->
        <div id="sectionB" class="mt-5">
            <label for="dropdownB" class="block text-sm font-medium text-gray-700">Section B</label>
            <select id="dropdownB" name="sectionB_count" class="block w-full p-2 border border-gray-300 rounded-md mb-4" required>
                <option value="">Select number of fields for section B</option>
                <!-- Populated by JavaScript -->
            </select>
            <div id="inputFieldsB"></div>

            @if ($errors->has('sectionB'))
                <div class="text-red-500 mt-2 text-sm">
                    {{ $errors->first('sectionB') }}
                </div>
            @endif
        </div>

        <!-- Instructions -->
        <div id="instructionsContainer" class="mt-5">
            <!-- Instructions fields populated by JavaScript -->
        </div>

        @if ($errors->has('instructions'))
            <div class="text-red-500 mt-2 text-sm">
                {{ $errors->first('instructions') }}
            </div>
        @endif

        <!-- Submit Button -->
        <div class="flex justify-center">
            <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-red-700 text-white rounded-md">
                Submit
            </button>
        </div>
    </form>
</div>





<!-- JavaScript for populating fields -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Populate the dropdowns for Section A and B
    populateDropdown('dropdownA', 20);
    populateDropdown('dropdownB', 10);

    function populateDropdown(dropdownId, maxFields) {
        const dropdown = document.getElementById(dropdownId);
        dropdown.innerHTML = '<option value="">Select number of questions</option>';

        for (let i = 1; i <= maxFields; i++) {
            const option = new Option(i, i);
            dropdown.add(option);
        }

        dropdown.addEventListener('change', handleDropdownChange);
    }

    function handleDropdownChange() {
        const section = this.id.charAt(this.id.length - 1); // Get section A or B
        createInputFields(`inputFields${section}`, this.value);
    }

    function createInputFields(containerId, numberOfFields) {
        const container = document.getElementById(containerId);
        container.innerHTML = ''; // Clear previous fields

        for (let i = 1; i <= numberOfFields; i++) {
            const sectionId = containerId.charAt(containerId.length - 1);
            const editorId = `${containerId}Editor${i}`;

            const fieldContainer = document.createElement('div');
            fieldContainer.className = 'mb-4';

            const label = document.createElement('label');
            label.setAttribute('for', editorId);
            label.className = 'block text-sm font-medium text-gray-700';
            label.textContent = `Field ${sectionId}${i}`;

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = `section${sectionId}[]`;

            const editorDiv = document.createElement('div');
            editorDiv.id = editorId;

            fieldContainer.appendChild(label);
            fieldContainer.appendChild(editorDiv);
            fieldContainer.appendChild(hiddenInput);
            container.appendChild(fieldContainer);

            // Initialize TinyMCE with the custom image picker functionality
            tinymce.init({
                selector: `#${editorId}`,
                plugins: 'image table link code charmap preview fullscreen anchor lists',
                toolbar: 'undo redo | formatselect | bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist outdent indent | link image charmap | fullscreen preview code',
                menubar: 'file edit view insert format tools table help',
                height: 200,

                // Enable title field in the Image dialog
                image_title: true,

                // Enable automatic uploads for blob or data URIs
                automatic_uploads: true,

                // Specify file types for the file picker
                file_picker_types: 'image',

                // Custom file picker for images
                file_picker_callback: (cb, value, meta) => {
                    const input = document.createElement('input');
                    input.setAttribute('type', 'file');
                    input.setAttribute('accept', 'image/*');

                    input.addEventListener('change', (e) => {
                        const file = e.target.files[0];

                        const reader = new FileReader();
                        reader.addEventListener('load', () => {
                            const id = 'blobid' + new Date().getTime();
                            const blobCache = tinymce.activeEditor.editorUpload.blobCache;
                            const base64 = reader.result.split(',')[1];
                            const blobInfo = blobCache.create(id, file, base64);
                            blobCache.add(blobInfo);

                            // Call the callback with the blob URI and populate the title field with the file name
                            cb(blobInfo.blobUri(), { title: file.name });
                        });
                        reader.readAsDataURL(file);
                    });

                    input.click();
                },

                // Update hidden input on change
                setup: function (editor) {
                    editor.on('change', function () {
                        hiddenInput.value = editor.getContent(); // Set TinyMCE content to hidden input field
                    });
                },

                content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
            });
        }
    }

    // Automatically create instructions for Section A and B
    updateInstructions();

    function updateInstructions() {
        const instructionsContainer = document.getElementById('instructionsContainer');
        instructionsContainer.innerHTML = ''; // Clear previous fields

        const fieldsInfo = ['Section A Instructions', 'Section B Instructions'];

        fieldsInfo.forEach((info, index) => {
            const inputGroup = document.createElement('div');
            const label = document.createElement('label');
            const input = document.createElement('input'); // Simple input field for instructions

            label.textContent = info;
            label.setAttribute('for', `instructions${index}`);
            label.className = 'block text-sm font-bold text-gray-900';

            input.id = `instructions${index}`;
            input.name = `instructions[${index + 1}]`;
            input.type = 'text';
            input.required = true;
            input.className = 'block w-full p-2 border border-gray-300 rounded-md mb-4';

            inputGroup.appendChild(label);
            inputGroup.appendChild(input);
            instructionsContainer.appendChild(inputGroup);
        });
    }
});

</script>


{{-- js for submit faculty --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
            let courseDropdown = document.getElementById('courseUnit');
            let facultyInput = document.getElementById('facultyField');

            courseDropdown.addEventListener('change', function () {
                let selectedOption = courseDropdown.options[courseDropdown.selectedIndex];
                let faculty = selectedOption.getAttribute('data-faculty');

                facultyInput.value = faculty; // Set the faculty value dynamically
                console.log("🔄 Faculty updated:", faculty);
            });
        });

</script>

<!-- Include TinyMCE -->
<script src="https://cdn.tiny.cloud/1/hs23eqphwt8todsqyrkfui7bvhc29664dxr64fj9h09r460f/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<!-- Include MathJax -->
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

</body>
</html>