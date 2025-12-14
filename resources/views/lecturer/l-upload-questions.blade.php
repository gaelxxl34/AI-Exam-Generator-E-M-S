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

    <!-- CSRF Token for AJAX requests -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- jQuery for Summernote -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Summernote Lite (no Bootstrap dependency) -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <!-- AI Assistant -->
    <script src="{{ asset('assets/js/ai-assistant.js') }}"></script>

    <!-- Custom CSS for Paper-Width Editor & Professional Formatting -->
    <style>
        /* ============================================
           📄 PAPER-WIDTH EDITOR SYSTEM
           Matches A4 paper dimensions for WYSIWYG
           A4 = 210mm width, with 15mm margins = 180mm printable
           At 96 DPI: ~680px, we use 650px for safety
           ============================================ */

        /* Paper-like container for each question */
        .paper-editor-container {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            display: flex;
            justify-content: center;
        }

        .paper-editor-wrapper {
            background: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
            padding: 15mm;
            width: 210mm;
            /* A4 paper width */
            max-width: 100%;
        }

        /* Remove visual margin guides - pure white paper look */
        .paper-margin-guide {
            background: white;
            padding: 0;
            min-height: 150px;
        }

        /* Constrain Summernote editor to paper width */
        .note-editor {
            position: relative;
            z-index: 10;
            max-width: 100% !important;
            border: 1px solid #ccc !important;
            border-radius: 4px;
        }

        .note-editor .note-editing-area {
            background: white;
        }

        .note-editor .note-editing-area .note-editable {
            /* Match PDF font for true WYSIWYG */
            font-family: 'Times New Roman', Georgia, serif !important;
            font-size: 12pt !important;
            line-height: 1.15 !important;
            /* MS Word default */
            color: #000 !important;
            padding: 15px 20px !important;
            min-height: 120px;
            max-height: 400px;
            overflow-y: auto;
        }

        /* ============================================
           📋 TABLE STYLING - Fit to Paper Width
           ============================================ */
        .note-editable table {
            border-collapse: collapse !important;
            width: 100% !important;
            max-width: 100% !important;
            border: 1px solid #000000 !important;
            margin: 10px 0 !important;
            table-layout: fixed !important;
            /* Force columns to fit */
            font-size: 11px !important;
        }

        .note-editable table td,
        .note-editable table th {
            border: 1px solid #000000 !important;
            padding: 6px 8px !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            vertical-align: top !important;
        }

        .note-editable table thead th {
            background-color: #f2f2f2 !important;
            font-weight: bold !important;
            text-align: left !important;
        }

        /* Prevent table overflow */
        .note-editor .note-editing-area .note-editable table {
            border: 1px solid #000000 !important;
            table-layout: fixed !important;
        }

        /* ============================================
           📝 LIST FORMATTING - Professional Style
           ============================================ */
        .note-editable ul {
            list-style-type: disc !important;
            margin: 8px 0 8px 25px !important;
            padding-left: 0 !important;
        }

        .note-editable ol {
            list-style-type: decimal !important;
            margin: 8px 0 8px 25px !important;
            padding-left: 0 !important;
        }

        .note-editable ul ul {
            list-style-type: circle !important;
            margin: 4px 0 4px 20px !important;
        }

        .note-editable ol ol {
            list-style-type: lower-alpha !important;
            margin: 4px 0 4px 20px !important;
        }

        .note-editable li {
            margin-bottom: 0 !important;
            line-height: 1.15 !important;
        }

        /* Nested list items (a, b, c style for sub-questions) */
        .note-editable ol[type="a"],
        .note-editable ol.lower-alpha {
            list-style-type: lower-alpha !important;
        }

        .note-editable ol[type="i"],
        .note-editable ol.lower-roman {
            list-style-type: lower-roman !important;
        }

        /* ============================================
           🔤 TEXT FORMATTING - Match PDF Output
           ============================================ */
        .note-editable p {
            margin: 0 !important;
            line-height: 1.15 !important;
        }

        .note-editable strong,
        .note-editable b {
            font-weight: bold !important;
        }

        .note-editable em,
        .note-editable i {
            font-style: italic !important;
        }

        .note-editable u {
            text-decoration: underline !important;
        }

        /* Subscript and Superscript */
        .note-editable sub {
            vertical-align: sub !important;
            font-size: 0.8em !important;
        }

        .note-editable sup {
            vertical-align: super !important;
            font-size: 0.8em !important;
        }

        /* ============================================
           🖼️ IMAGE HANDLING - Fit to Paper
           ============================================ */
        .note-editable img {
            max-width: 100% !important;
            height: auto !important;
            display: block;
            margin: 10px auto;
        }

        /* ============================================
           🎛️ TOOLBAR STYLING
           ============================================ */
        .note-toolbar {
            z-index: 50 !important;
            position: relative !important;
            background: #f8f9fa !important;
            border-bottom: 1px solid #ddd !important;
            padding: 5px !important;
            flex-wrap: wrap;
        }

        .note-toolbar .note-btn-group {
            z-index: 51 !important;
            margin: 2px !important;
        }

        .note-toolbar .note-btn {
            padding: 4px 8px !important;
            font-size: 12px !important;
        }

        .note-popover {
            z-index: 60 !important;
        }

        .dropdown-menu,
        .note-dropdown-menu {
            z-index: 70 !important;
            pointer-events: auto !important;
        }

        .note-toolbar button {
            pointer-events: auto !important;
            cursor: pointer !important;
        }

        /* ============================================
           🤖 AI ASSISTANT BUTTON STYLING
           ============================================ */
        .note-btn-group .btn-ai-assistant,
        .note-toolbar .note-btn[data-name="aiAssistant"] {
            background: #2563eb !important;
            color: white !important;
            border: none !important;
            border-radius: 4px !important;
            font-weight: 500 !important;
            padding: 5px 8px !important;
            transition: all 0.2s ease !important;
        }

        .note-btn-group .btn-ai-assistant:hover,
        .note-toolbar .note-btn[data-name="aiAssistant"]:hover {
            background: #1d4ed8 !important;
        }

        .note-btn-group .btn-ai-assistant i,
        .note-toolbar .note-btn[data-name="aiAssistant"] i {
            margin-right: 3px !important;
        }

        /* ============================================
           📏 PAPER WIDTH INDICATOR
           ============================================ */
        .paper-width-indicator {
            background: #e3f2fd;
            border: 1px dashed #2196f3;
            border-radius: 4px;
            padding: 8px 12px;
            margin-bottom: 10px;
            font-size: 11px;
            color: #1565c0;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .paper-width-indicator i {
            font-size: 14px;
        }

        /* ============================================
           📱 RESPONSIVE - Mobile friendly
           ============================================ */
        @media (max-width: 850px) {
            .paper-editor-wrapper {
                width: 100%;
                padding: 10px;
            }

            .note-editable {
                font-size: 11px !important;
            }
        }

        /* ============================================
           ✨ QUESTION NUMBER STYLING
           ============================================ */
        .question-label {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 8px;
        }

        .question-label.section-a {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        }

        .question-label.section-b {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
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

        <!-- Progress Stepper -->
        <div class="mb-8 max-w-4xl mx-auto">
            <div class="bg-white rounded-lg shadow-md p-4">
                <div class="flex items-center justify-between">
                    <!-- Step 1: Course -->
                    <div class="flex flex-col items-center flex-1" id="step1Container">
                        <div id="step1"
                            class="w-10 h-10 rounded-full flex items-center justify-center text-white font-bold bg-blue-600 ring-4 ring-blue-200 transition-all">
                            <i class="fas fa-book text-sm"></i>
                        </div>
                        <span class="text-xs mt-2 text-blue-600 font-semibold">Course</span>
                    </div>

                    <!-- Connector 1 -->
                    <div class="flex-1 h-1 bg-gray-200 mx-2 rounded" id="connector1">
                        <div class="h-full bg-blue-600 rounded transition-all duration-300" style="width: 0%"></div>
                    </div>

                    <!-- Step 2: Section A -->
                    <div class="flex flex-col items-center flex-1" id="step2Container">
                        <div id="step2"
                            class="w-10 h-10 rounded-full flex items-center justify-center font-bold bg-gray-200 text-gray-500 transition-all">
                            <span class="text-sm">A</span>
                        </div>
                        <span class="text-xs mt-2 text-gray-500">Section A</span>
                    </div>

                    <!-- Connector 2 -->
                    <div class="flex-1 h-1 bg-gray-200 mx-2 rounded" id="connector2">
                        <div class="h-full bg-blue-600 rounded transition-all duration-300" style="width: 0%"></div>
                    </div>

                    <!-- Step 3: Section B -->
                    <div class="flex flex-col items-center flex-1" id="step3Container">
                        <div id="step3"
                            class="w-10 h-10 rounded-full flex items-center justify-center font-bold bg-gray-200 text-gray-500 transition-all">
                            <span class="text-sm">B</span>
                        </div>
                        <span class="text-xs mt-2 text-gray-500">Section B</span>
                    </div>

                    <!-- Connector 3 -->
                    <div class="flex-1 h-1 bg-gray-200 mx-2 rounded" id="connector3">
                        <div class="h-full bg-blue-600 rounded transition-all duration-300" style="width: 0%"></div>
                    </div>

                    <!-- Step 4: Instructions -->
                    <div class="flex flex-col items-center flex-1" id="step4Container">
                        <div id="step4"
                            class="w-10 h-10 rounded-full flex items-center justify-center font-bold bg-gray-200 text-gray-500 transition-all">
                            <i class="fas fa-clipboard-list text-sm"></i>
                        </div>
                        <span class="text-xs mt-2 text-gray-500">Instructions</span>
                    </div>

                    <!-- Connector 4 -->
                    <div class="flex-1 h-1 bg-gray-200 mx-2 rounded" id="connector4">
                        <div class="h-full bg-blue-600 rounded transition-all duration-300" style="width: 0%"></div>
                    </div>

                    <!-- Step 5: Submit -->
                    <div class="flex flex-col items-center flex-1" id="step5Container">
                        <div id="step5"
                            class="w-10 h-10 rounded-full flex items-center justify-center font-bold bg-gray-200 text-gray-500 transition-all">
                            <i class="fas fa-check text-sm"></i>
                        </div>
                        <span class="text-xs mt-2 text-gray-500">Submit</span>
                    </div>
                </div>

                <!-- Progress Text -->
                <div class="mt-4 text-center">
                    <p id="progressText" class="text-sm text-gray-600">
                        <i class="fas fa-arrow-down mr-1"></i> Start by selecting a course below
                    </p>
                </div>
            </div>
        </div>

        <!-- User Tip -->
        <div class="mb-6 max-w-4xl mx-auto">
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

                        @if(empty($courses))
                            <!-- No Courses Message -->
                            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 rounded-lg">
                                <div class="flex items-start">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-info-circle text-blue-400 text-xl"></i>
                                    </div>
                                    <div class="ml-3">
                                        <h3 class="text-blue-900 font-semibold mb-1">No Courses Assigned</h3>
                                        <p class="text-blue-800 text-sm">
                                            You currently have no courses assigned to your account. Please contact your
                                            faculty administrator to assign courses to your profile.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div>
                                <label for="courseUnit" class="block text-sm font-medium text-gray-700 mb-2">
                                    Course Unit <span class="text-red-500">*</span>
                                </label>
                                <select id="courseUnit" name="courseUnit"
                                    class="block w-full p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                                    required>
                                    <option value="">Select a course unit</option>
                                    @foreach ($courses as $course)
                                        <option value="{{ $course['name'] }}" data-faculty="{{ $course['faculty'] }}"
                                            data-code="{{ $course['code'] }}">
                                            {{ $course['code'] }} - {{ $course['name'] }}
                                        </option>
                                    @endforeach
                                </select>

                                <!-- Course Code Field (Hidden - for unique identification) -->
                                <input type="hidden" id="courseCodeField" name="courseCode" value="">

                                <!-- Faculty Field (Hidden) -->
                                <input type="hidden" id="facultyField" name="faculty" value="">

                                @if ($errors->has('faculty'))
                                    <div class="text-red-500 mt-2 text-sm flex items-center">
                                        <i class="fas fa-exclamation-triangle mr-1"></i>
                                        {{ $errors->first('faculty') }}
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>

                    @if(!empty($courses))
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

                    @endif
                <!-- Submit Section -->
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
                    @if(!empty($courses))
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
                    @endif
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

                // Add paper width info notice (only once at the top)
                if (numberOfFields > 0) {
                    const infoNotice = document.createElement('div');
                    infoNotice.className = 'paper-width-indicator';
                    infoNotice.innerHTML = `
                        <i class="fas fa-ruler-horizontal"></i>
                        <span><strong>Paper Preview Mode:</strong> Editor width matches A4 paper. What you see here will match the PDF output.</span>
                    `;
                    container.appendChild(infoNotice);
                }

                for (let i = 1; i <= numberOfFields; i++) {
                    const sectionId = containerId.charAt(containerId.length - 1);
                    const editorId = `${containerId}Editor${i}`;
                    const sectionClass = sectionId === 'A' ? 'section-a' : 'section-b';

                    // Main field container with paper styling
                    const fieldContainer = document.createElement('div');
                    fieldContainer.className = 'mb-6';

                    // Question label with section styling
                    const label = document.createElement('div');
                    label.className = `question-label ${sectionClass}`;
                    label.innerHTML = `<i class="fas fa-question-circle"></i> Question ${sectionId}${i}`;

                    // Paper-like editor container
                    const paperContainer = document.createElement('div');
                    paperContainer.className = 'paper-editor-container';

                    const paperWrapper = document.createElement('div');
                    paperWrapper.className = 'paper-editor-wrapper';

                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = `section${sectionId}[]`;

                    const editorDiv = document.createElement('textarea');
                    editorDiv.id = editorId;
                    editorDiv.className = 'summernote';

                    // Assemble the structure
                    paperWrapper.appendChild(editorDiv);
                    paperContainer.appendChild(paperWrapper);

                    fieldContainer.appendChild(label);
                    fieldContainer.appendChild(paperContainer);
                    fieldContainer.appendChild(hiddenInput);
                    container.appendChild(fieldContainer);

                    // Initialize Summernote on dynamically created textareas
                    initializeSummernote(`#${editorId}`, hiddenInput);
                }
            }

            // Function to initialize Summernote with Paper-Width Settings
            function initializeSummernote(selector, hiddenInput) {
                $(selector).summernote({
                    height: 150,
                    minHeight: 150,
                    maxHeight: 400,
                    placeholder: "Type your question here. Use the toolbar to add lists, tables, images, and formatting...",
                    // Use Times New Roman as default to match PDF output
                    fontNames: ['Times New Roman'],
                    fontNamesIgnoreCheck: ['Times New Roman'],
                    // Default font settings matching PDF
                    defaultFontName: 'Times New Roman',
                    // ✅ Enable UTF-8 character support
                    codeviewFilter: true,
                    codeviewIframeFilter: true,
                    disableDragAndDrop: false,
                    // Table settings - constrain to paper width
                    tableClassName: 'table table-bordered',
                    toolbar: [
                        ['font', ['bold', 'italic', 'underline', 'strikethrough']],
                        ['fontsize', ['fontsize']],
                        ['script', ['superscript', 'subscript']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture']],
                        ['view', ['codeview']],
                        ['ai', ['aiAssistant']]
                    ],
                    buttons: {
                        aiAssistant: function (context) {
                            var ui = $.summernote.ui;
                            var button = ui.button({
                                contents: '<i class="fas fa-pen-fancy"></i> <span class="hidden sm:inline">AI Help</span>',
                                tooltip: 'AI Writing Assistant - Clean up, check grammar, fix equations',
                                className: 'btn-ai-assistant',
                                click: function () {
                                    var $editor = context.$note;
                                    var editorId = $editor.attr('id');
                                    var selector = editorId ? '#' + editorId : '.summernote';
                                    showAIAssistantModal(selector);
                                }
                            });
                            return button.render();
                        }
                    },
                    // Font sizes optimized for print
                    fontSizes: ['8', '9', '10', '11', '12', '13', '14', '16', '18', '20', '24'],
                    callbacks: {
                        onKeyup: function () {
                            $(selector).summernote('saveRange');
                        },
                        onMouseUp: function () {
                            $(selector).summernote('saveRange');
                        },
                        onChange: function (contents) {
                            // Clean and sanitize content before saving
                            let cleanedContent = sanitizeForPdf(contents);
                            hiddenInput.value = cleanedContent;
                        },
                        onInit: function () {
                            // Set UTF-8 charset and ensure paper-width styling
                            $(selector).next('.note-editor').find('.note-editable')
                                .attr('accept-charset', 'UTF-8')
                                .css({
                                    'font-family': "'Times New Roman', Georgia, serif",
                                    'font-size': '12px',
                                    'line-height': '1.6'
                                });
                        },
                        onPaste: function (e) {
                            // Clean pasted content
                            setTimeout(function () {
                                let content = $(selector).summernote('code');
                                let cleaned = sanitizeForPdf(content);
                                $(selector).summernote('code', cleaned);
                            }, 100);
                        },
                        onImageUpload: function (files) {
                            // Resize images to fit paper width
                            for (let i = 0; i < files.length; i++) {
                                resizeAndInsertImage(files[i], selector);
                            }
                        }
                    }
                });
            }

            // Sanitize content for PDF compatibility
            function sanitizeForPdf(html) {
                // Create a temporary container
                let temp = document.createElement('div');
                temp.innerHTML = html;

                // Fix tables - ensure they have proper structure
                let tables = temp.querySelectorAll('table');
                tables.forEach(function (table) {
                    table.style.width = '100%';
                    table.style.maxWidth = '100%';
                    table.style.tableLayout = 'fixed';
                    table.style.borderCollapse = 'collapse';
                    table.setAttribute('border', '1');

                    // Ensure all cells have borders
                    let cells = table.querySelectorAll('td, th');
                    cells.forEach(function (cell) {
                        cell.style.border = '1px solid #000';
                        cell.style.padding = '6px';
                        cell.style.wordWrap = 'break-word';
                    });
                });

                // Fix lists - ensure proper nesting
                let lists = temp.querySelectorAll('ul, ol');
                lists.forEach(function (list) {
                    list.style.marginLeft = '25px';
                    list.style.paddingLeft = '0';
                });

                // Fix images - constrain width
                let images = temp.querySelectorAll('img');
                images.forEach(function (img) {
                    img.style.maxWidth = '100%';
                    img.style.height = 'auto';
                });

                return temp.innerHTML;
            }

            // Resize image before inserting
            function resizeAndInsertImage(file, selector) {
                let reader = new FileReader();
                reader.onload = function (e) {
                    let img = new Image();
                    img.onload = function () {
                        // Max width for A4 paper (in pixels at 96 DPI)
                        const maxWidth = 600;
                        let width = img.width;
                        let height = img.height;

                        if (width > maxWidth) {
                            height = (height * maxWidth) / width;
                            width = maxWidth;
                        }

                        // Create canvas and resize
                        let canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        let ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        // Insert resized image
                        let resizedDataUrl = canvas.toDataURL('image/jpeg', 0.85);
                        $(selector).summernote('insertImage', resizedDataUrl);
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }

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

            // Faculty and Course Code Selection Handling
            let courseDropdown = document.getElementById('courseUnit');
            let facultyInput = document.getElementById('facultyField');
            let courseCodeInput = document.getElementById('courseCodeField');

            courseDropdown.addEventListener('change', function () {
                let selectedOption = courseDropdown.options[courseDropdown.selectedIndex];
                let faculty = selectedOption.getAttribute('data-faculty');
                let courseCode = selectedOption.getAttribute('data-code');

                if (faculty) {
                    facultyInput.value = faculty;
                    console.log("✅ Faculty updated:", faculty);
                } else {
                    console.error("⚠ Faculty data missing from selected option.");
                }

                if (courseCode) {
                    courseCodeInput.value = courseCode;
                    console.log("✅ Course Code updated:", courseCode);
                } else {
                    console.error("⚠ Course Code data missing from selected option.");
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

    <!-- Progress Stepper JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const courseDropdown = document.getElementById('courseUnit');
            const dropdownA = document.getElementById('dropdownA');
            const dropdownB = document.getElementById('dropdownB');
            const progressText = document.getElementById('progressText');

            // Step elements
            const steps = {
                1: { circle: document.getElementById('step1'), container: document.getElementById('step1Container') },
                2: { circle: document.getElementById('step2'), container: document.getElementById('step2Container') },
                3: { circle: document.getElementById('step3'), container: document.getElementById('step3Container') },
                4: { circle: document.getElementById('step4'), container: document.getElementById('step4Container') },
                5: { circle: document.getElementById('step5'), container: document.getElementById('step5Container') }
            };

            const connectors = {
                1: document.getElementById('connector1')?.querySelector('div'),
                2: document.getElementById('connector2')?.querySelector('div'),
                3: document.getElementById('connector3')?.querySelector('div'),
                4: document.getElementById('connector4')?.querySelector('div')
            };

            function updateStep(stepNum, status) {
                const step = steps[stepNum];
                if (!step) return;

                const circle = step.circle;
                const label = step.container.querySelector('span:last-child');

                // Reset classes
                circle.className = 'w-10 h-10 rounded-full flex items-center justify-center font-bold transition-all';

                if (status === 'active') {
                    circle.classList.add('bg-blue-600', 'text-white', 'ring-4', 'ring-blue-200');
                    if (label) label.classList.replace('text-gray-500', 'text-blue-600');
                    if (label) label.classList.add('font-semibold');
                } else if (status === 'completed') {
                    circle.classList.add('bg-green-500', 'text-white');
                    if (label) label.classList.replace('text-gray-500', 'text-green-600');
                    if (label) label.classList.replace('text-blue-600', 'text-green-600');
                } else {
                    circle.classList.add('bg-gray-200', 'text-gray-500');
                    if (label) label.classList.remove('font-semibold');
                    if (label) label.className = 'text-xs mt-2 text-gray-500';
                }
            }

            function updateConnector(connectorNum, percentage) {
                const connector = connectors[connectorNum];
                if (connector) {
                    connector.style.width = percentage + '%';
                }
            }

            function updateProgress() {
                let currentStep = 1;
                let sectionAHasQuestions = false;
                let sectionBHasQuestions = false;
                let instructionsComplete = false;

                // Check course selection
                const courseSelected = courseDropdown && courseDropdown.value !== '';

                // Check Section A
                if (dropdownA) {
                    sectionAHasQuestions = dropdownA.value !== '' && parseInt(dropdownA.value) > 0;
                }

                // Check Section B
                if (dropdownB) {
                    sectionBHasQuestions = dropdownB.value !== '' && parseInt(dropdownB.value) > 0;
                }

                // Check instructions (check if any instruction fields exist and have values)
                const instructionInputs = document.querySelectorAll('[name^="instructions"]');
                if (instructionInputs.length > 0) {
                    instructionsComplete = Array.from(instructionInputs).every(input => input.value.trim() !== '');
                }

                // Determine current step and update UI
                if (!courseSelected) {
                    currentStep = 1;
                    updateStep(1, 'active');
                    updateStep(2, 'inactive');
                    updateStep(3, 'inactive');
                    updateStep(4, 'inactive');
                    updateStep(5, 'inactive');
                    updateConnector(1, 0);
                    updateConnector(2, 0);
                    updateConnector(3, 0);
                    updateConnector(4, 0);
                    progressText.innerHTML = '<i class="fas fa-arrow-down mr-1"></i> Start by selecting a course below';
                } else if (!sectionAHasQuestions) {
                    currentStep = 2;
                    updateStep(1, 'completed');
                    updateStep(2, 'active');
                    updateStep(3, 'inactive');
                    updateStep(4, 'inactive');
                    updateStep(5, 'inactive');
                    updateConnector(1, 100);
                    updateConnector(2, 0);
                    updateConnector(3, 0);
                    updateConnector(4, 0);
                    progressText.innerHTML = '<i class="fas fa-check text-green-500 mr-1"></i> Course selected! Now add Section A questions';
                } else if (!sectionBHasQuestions) {
                    currentStep = 3;
                    updateStep(1, 'completed');
                    updateStep(2, 'completed');
                    updateStep(3, 'active');
                    updateStep(4, 'inactive');
                    updateStep(5, 'inactive');
                    updateConnector(1, 100);
                    updateConnector(2, 100);
                    updateConnector(3, 0);
                    updateConnector(4, 0);
                    progressText.innerHTML = '<i class="fas fa-check text-green-500 mr-1"></i> Section A ready! Now add Section B questions';
                } else if (!instructionsComplete) {
                    currentStep = 4;
                    updateStep(1, 'completed');
                    updateStep(2, 'completed');
                    updateStep(3, 'completed');
                    updateStep(4, 'active');
                    updateStep(5, 'inactive');
                    updateConnector(1, 100);
                    updateConnector(2, 100);
                    updateConnector(3, 100);
                    updateConnector(4, 0);
                    progressText.innerHTML = '<i class="fas fa-check text-green-500 mr-1"></i> Questions ready! Fill in the exam instructions';
                } else {
                    currentStep = 5;
                    updateStep(1, 'completed');
                    updateStep(2, 'completed');
                    updateStep(3, 'completed');
                    updateStep(4, 'completed');
                    updateStep(5, 'active');
                    updateConnector(1, 100);
                    updateConnector(2, 100);
                    updateConnector(3, 100);
                    updateConnector(4, 100);
                    progressText.innerHTML = '<i class="fas fa-check-circle text-green-500 mr-1"></i> All sections complete! Ready to submit';
                }
            }

            // Add event listeners to track progress
            if (courseDropdown) {
                courseDropdown.addEventListener('change', updateProgress);
            }
            if (dropdownA) {
                dropdownA.addEventListener('change', updateProgress);
            }
            if (dropdownB) {
                dropdownB.addEventListener('change', updateProgress);
            }

            // Watch for instruction input changes (using event delegation since they're dynamically created)
            document.addEventListener('input', function (e) {
                if (e.target.name && e.target.name.startsWith('instructions')) {
                    updateProgress();
                }
            });

            // Initial update
            updateProgress();
        });
    </script>


</body>

</html>