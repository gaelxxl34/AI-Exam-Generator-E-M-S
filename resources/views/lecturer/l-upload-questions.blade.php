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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
</head>

<body class="bg-gray-50">
    @include('partials.lecturer-navbar')

    <div class="p-4 sm:ml-64 mt-20">
        <!-- Page Header -->
        <div class="mb-8">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-lg shadow-lg p-6 text-white">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-white bg-opacity-20 rounded-lg flex items-center justify-center mr-4">
                        <i class="fas fa-file-upload text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold">Upload Exam Questions</h1>
                        <p class="text-blue-100 mt-1">Create and submit your examination questions for review</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Tip -->
        <div class="mb-6">
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg flex items-start">
                <div class="flex-shrink-0 mt-1">
                    <i class="fas fa-lightbulb text-yellow-400 text-xl"></i>
                </div>
                <div class="ml-3">
                    <p class="text-yellow-900 font-semibold mb-1">Tip for Creating Exam Template</p>
                    <ul class="list-disc list-inside text-yellow-800 text-sm space-y-1">
                        <li>Select <span class="font-medium">one question</span> in <span class="font-medium">Section
                                A</span> and <span class="font-medium">one question</span> in <span
                                class="font-medium">Section B</span> to start.</li>
                        <li>Fill in some sample data and add the required instructions to create your exam template.
                        </li>
                        <li>After saving, you can <span class="font-medium">edit and add more questions later</span> by
                            visiting the <span class="font-medium">Review Exams</span> page.</li>
                    </ul>
                </div>
            </div>
        </div>
        <!-- End User Tip -->

        <!-- Main Form Container -->
        <div class="max-w-4xl mx-auto">
            <form id="uploadForm" enctype="multipart/form-data" action="{{ route('upload.exam') }}" method="post"
                class="bg-white rounded-lg shadow-lg overflow-hidden">
                @csrf

                <!-- Display error message for duplicate exam -->
                @if (session('error'))
                    <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <i class="fas fa-exclamation-circle text-red-400"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Form Content -->
                <div class="p-6 space-y-6">
                    <!-- Course Selection Section -->
                    <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-book text-blue-600 mr-2"></i>
                            <h2 class="text-lg font-semibold text-gray-900">Course Information</h2>
                        </div>

                        <div>
                            <label for="courseUnit" class="block text-sm font-medium text-gray-700 mb-2">
                                Course Unit <span class="text-red-500">*</span>
                            </label>
                            <select id="courseUnit" name="courseUnit"
                                class="block w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                required>
                                <option value="">Select a course unit</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course['name'] }}" data-faculty="{{ $course['faculty'] }}">
                                        {{ $course['name'] }} ({{ $course['code'] }})
                                    </option>
                                @endforeach
                            </select>

                            <!-- Faculty Field (Hidden) -->
                            <input type="hidden" id="facultyField" name="faculty" value="">

                            @if ($errors->has('faculty'))
                                <div class="text-red-500 mt-2 text-sm flex items-center">
                                    <i class="fas fa-exclamation-triangle mr-1"></i>
                                    {{ $errors->first('faculty') }}
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Hidden Format Input -->
                    <input type="hidden" name="format" value="AB">

                    <!-- Section A -->
                    <div class="bg-green-50 rounded-lg p-6 border border-green-200">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-list-ol text-green-600 mr-2"></i>
                            <h2 class="text-lg font-semibold text-gray-900">Section A</h2>
                            <span class="ml-2 text-sm text-gray-500">(Multiple Choice / Short Answer)</span>
                        </div>

                        <div class="mb-4">
                            <label for="dropdownA" class="block text-sm font-medium text-gray-700 mb-2">
                                Number of Questions <span class="text-red-500">*</span>
                            </label>
                            <select id="dropdownA" name="sectionA_count"
                                class="block w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors"
                                required>
                                <option value="">Select number of questions for Section A</option>
                            </select>
                        </div>

                        <div id="inputFieldsA" class="space-y-4"></div>

                        @if ($errors->has('sectionA'))
                            <div class="text-red-500 mt-2 text-sm flex items-center">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ $errors->first('sectionA') }}
                            </div>
                        @endif
                    </div>

                    <!-- Section B -->
                    <div class="bg-purple-50 rounded-lg p-6 border border-purple-200">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-list-alt text-purple-600 mr-2"></i>
                            <h2 class="text-lg font-semibold text-gray-900">Section B</h2>
                            <span class="ml-2 text-sm text-gray-500">(Essay / Long Answer)</span>
                        </div>

                        <div class="mb-4">
                            <label for="dropdownB" class="block text-sm font-medium text-gray-700 mb-2">
                                Number of Questions <span class="text-red-500">*</span>
                            </label>
                            <select id="dropdownB" name="sectionB_count"
                                class="block w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors"
                                required>
                                <option value="">Select number of questions for Section B</option>
                            </select>
                        </div>

                        <div id="inputFieldsB" class="space-y-4"></div>

                        @if ($errors->has('sectionB'))
                            <div class="text-red-500 mt-2 text-sm flex items-center">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ $errors->first('sectionB') }}
                            </div>
                        @endif
                    </div>

                    <!-- Instructions Section -->
                    <div class="bg-orange-50 rounded-lg p-6 border border-orange-200">
                        <div class="flex items-center mb-4">
                            <i class="fas fa-clipboard-list text-orange-600 mr-2"></i>
                            <h2 class="text-lg font-semibold text-gray-900">Exam Instructions</h2>
                        </div>

                        <div id="instructionsContainer" class="space-y-4">
                            <!-- Instructions fields populated by JavaScript -->
                        </div>

                        @if ($errors->has('instructions'))
                            <div class="text-red-500 mt-2 text-sm flex items-center">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ $errors->first('instructions') }}
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    <div class="flex justify-between items-center">
                        <div class="text-sm text-gray-500">
                            <i class="fas fa-info-circle mr-1"></i>
                            Please review all sections before submitting
                        </div>
                        <button type="submit"
                            class="px-6 py-3 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white font-medium rounded-lg shadow-lg transform transition-all duration-200 hover:scale-105 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Submit Questions
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
        <div class="bg-white rounded-lg p-6 shadow-xl">
            <div class="flex items-center space-x-4">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <span class="text-gray-700 font-medium">Submitting questions...</span>
            </div>
        </div>
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
                    fieldContainer.className = 'bg-white rounded-lg border border-gray-200 p-4 shadow-sm';

                    const label = document.createElement('label');
                    label.setAttribute('for', editorId);
                    label.className = 'block text-sm font-medium text-gray-700 mb-2';
                    label.innerHTML = `<i class="fas fa-question-circle mr-1 text-gray-400"></i>Question ${sectionId}${i}`;

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

                const fieldsInfo = [
                    { label: 'Section A Instructions', icon: 'fas fa-list-ol', color: 'green' },
                    { label: 'Section B Instructions', icon: 'fas fa-list-alt', color: 'purple' }
                ];

                fieldsInfo.forEach((info, index) => {
                    const inputGroup = document.createElement('div');
                    inputGroup.className = 'bg-white rounded-lg border border-gray-200 p-4 shadow-sm';

                    const label = document.createElement('label');
                    label.innerHTML = `<i class="${info.icon} mr-1 text-${info.color}-600"></i>${info.label} <span class="text-red-500">*</span>`;
                    label.setAttribute('for', `instructions${index}`);
                    label.className = 'block text-sm font-medium text-gray-700 mb-2';

                    const input = document.createElement('input');
                    input.id = `instructions${index}`;
                    input.name = `instructions[${index + 1}]`;
                    input.type = 'text';
                    input.required = true;
                    input.placeholder = `Enter instructions for ${info.label.split(' ')[1]}...`;
                    input.className = 'block w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors';

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
                    // Enhanced error modal instead of alert
                    showErrorModal(missingFields);
                } else {
                    // Show loading overlay
                    document.getElementById('loadingOverlay').classList.remove('hidden');
                }
            });

            function showErrorModal(missingFields) {
                const modal = document.createElement('div');
                modal.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4';
                modal.innerHTML = `
                    <div class="bg-white rounded-lg shadow-xl max-w-md w-full p-6">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                <i class="fas fa-exclamation-triangle text-red-600"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900">Missing Required Fields</h3>
                        </div>
                        <p class="text-sm text-gray-600 mb-4">Please complete the following fields before submitting:</p>
                        <ul class="list-disc list-inside text-sm text-gray-700 mb-6 space-y-1">
                            ${missingFields.map(field => `<li>${field}</li>`).join('')}
                        </ul>
                        <button onclick="this.closest('.fixed').remove()" 
                            class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-medium rounded-lg transition-colors">
                            Close
                        </button>
                    </div>
                `;
                document.body.appendChild(modal);
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


</body>

</html>