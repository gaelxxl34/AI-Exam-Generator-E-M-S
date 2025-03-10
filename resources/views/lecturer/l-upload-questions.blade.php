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
        // Load the special character modal from an external file 
        console.log("Attempting to load: {{ asset('./assets/js/special-character-picker.js') }}");

        $.getScript("{{ asset('./assets/js/special-character-picker.js') }}")
            .done(function () {
                console.log("✅ special-character-picker.js loaded successfully!");
                loadSpecialCharModal(); // Call function inside script
            })
            .fail(function (jqxhr, settings, exception) {
                console.error("❌ Failed to load special-character-picker.js:", exception);
            });



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
                label.textContent = `Question ${sectionId}${i}`;

                const hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = `section${sectionId}[]`;

                const editorDiv = document.createElement('textarea');
                editorDiv.id = editorId;
                editorDiv.className = 'summernote';

                fieldContainer.appendChild(label);
                fieldContainer.appendChild(editorDiv);
                fieldContainer.appendChild(hiddenInput);
                container.appendChild(fieldContainer);

                // Initialize Summernote on dynamically created textareas
                initializeSummernote(`#${editorId}`, hiddenInput);
            }
        }

        // Function to initialize Summernote
        function initializeSummernote(selector, hiddenInput) {
            $(selector).summernote({
                height: 100,
                minHeight: 150,
                maxHeight: 300,
                focus: true,
                dialogsInBody: true,
                placeholder: "Type your question here...",
                fontNames: ['Arial', 'Courier New', 'Times New Roman', 'Verdana', 'Georgia', 'Comic Sans MS'],
                fontNamesIgnoreCheck: ['Arial', 'Courier New', 'Times New Roman', 'Verdana', 'Georgia', 'Comic Sans MS'],
                toolbar: [
                    ['style', ['style']],
                    ['fontname', ['fontname']],
                    ['fontsize', ['fontsize']],
                    ['font', ['bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture', 'video', 'table']],
                    ['view', ['codeview', 'help']],
                    ['custom', ['specialCharButton']] // ✅ Custom Special Characters Button
                ],
                buttons: {
                    specialCharButton: SpecialCharButton
                },
                callbacks: {
                    onKeyup: function () {
                        $(selector).summernote('saveRange'); // Save cursor position when typing
                    },
                    onMouseUp: function () {
                        $(selector).summernote('saveRange'); // Save cursor position on mouse click
                    },
                    onChange: function (contents) {
                        hiddenInput.value = contents; // Update hidden input with Summernote content
                    },
                    
                }
            });
        }

        function SpecialCharButton(context) {
            var ui = $.summernote.ui;
            var button = ui.button({
                contents: '<i class="fas fa-font"></i> Special Characters',
                tooltip: 'Insert Special Character',
                click: function () {
                    // Save Summernote range
                    $('.summernote').summernote('saveRange');
                    // Show the Tailwind-based modal
                    showSpecialCharModal();
                }
            });
            return button.render();
        }


        $(document).on('click', '.special-char', function () {
            // Restore Summernote cursor position
            $('.summernote').summernote('restoreRange');
            // Insert the selected character
            $('.summernote').summernote('editor.insertText', $(this).text());
            // Hide the Tailwind modal
            hideSpecialCharModal();
        });


        // Automatically create instructions for Section A and B
        updateInstructions();

        function updateInstructions() {
            const instructionsContainer = document.getElementById('instructionsContainer');
            instructionsContainer.innerHTML = ''; // Clear previous fields

            const fieldsInfo = ['Section A Instructions', 'Section B Instructions'];

            fieldsInfo.forEach((info, index) => {
                const inputGroup = document.createElement('div');
                inputGroup.className = 'mb-4';

                const label = document.createElement('label');
                label.textContent = info;
                label.setAttribute('for', `instructions${index}`);
                label.className = 'block text-sm font-bold text-gray-900';

                const input = document.createElement('input');
                input.id = `instructions${index}`;
                input.name = `instructions[${index + 1}]`;
                input.type = 'text';
                input.required = true;
                input.className = 'block w-full p-2 border border-gray-300 rounded-md';

                inputGroup.appendChild(label);
                inputGroup.appendChild(input);
                instructionsContainer.appendChild(inputGroup);
            });
        }

        // Faculty Selection Handling
        let courseDropdown = document.getElementById('courseUnit');
        let facultyInput = document.getElementById('facultyField');

        courseDropdown.addEventListener('change', function () {
            let selectedOption = courseDropdown.options[courseDropdown.selectedIndex];
            let faculty = selectedOption.getAttribute('data-faculty');

            if (faculty) {
                facultyInput.value = faculty; // Set the faculty value dynamically
                console.log("✅ Faculty updated:", faculty);
            } else {
                console.error("⚠ Faculty data missing from selected option.");
            }
        });

        // Form Submission with Validation
        $('#uploadForm').on('submit', function (e) {
            let isValid = true;
            let missingFields = [];

            // Check Course Unit
            if ($('#courseUnit').val() === '') {
                isValid = false;
                missingFields.push('Course Unit');
            }

            // Check Section A & B selection
            if ($('#dropdownA').val() === '') {
                isValid = false;
                missingFields.push('Section A selection');
            }
            if ($('#dropdownB').val() === '') {
                isValid = false;
                missingFields.push('Section B selection');
            }

            // Check Instructions
            $('[name^="instructions"]').each(function () {
                if ($(this).val().trim() === '') {
                    isValid = false;
                    missingFields.push('Instruction fields');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert(`⚠ Please fill in the missing fields: \n\n- ${missingFields.join('\n- ')}`);
            }
        });
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


</body>
</html>