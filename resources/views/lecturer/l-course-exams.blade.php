<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Course Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">



    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <style>
        /* Tailwind CSS to mimic Bootstrap table styles */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-bordered {
            border: 1px solid black;
        }

        .table-bordered td,
        .table-bordered th {
            border: 1px solid black;
        }

        .table td,
        .table th {
            padding: .75rem;
            vertical-align: top;
        }
        /* Ensure the modal is always on top */
        #statusOverlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1050;
            visibility: hidden;
            opacity: 0;
            transition: opacity 0.3s ease-in-out, visibility 0.3s;
        }

        /* Keep modal above everything */
        #statusModal {
            background: white;
            padding: 20px;
            border-radius: 10px;
            max-width: 400px;
            width: 100%;
            position: relative;
            z-index: 1051;
        }

        /* Blurring everything including the text editor */
        .blur-effect {
            filter: blur(5px);
            pointer-events: none;
        }

        /* Show modal */
        #statusOverlay.active {
            visibility: visible;
            opacity: 1;
        }

        /* Prevent scrolling when modal is open */
        body.modal-open {
            overflow: hidden;
        }

    </style>
</head>
<body>
    
    @include('partials.lecturer-navbar')

<div class="p-4 sm:ml-64 mt-20">

<div  class="container mx-auto px-4">
    @forelse ($exams as $index => $exam)
                            <div class="mt-8 bg-white rounded-lg shadow-md">
                                <div class="flex items-center justify-between">
                                    <h1 class="text-xl font-bold p-4 border-b text-center">{{$exam['courseUnit'] }}</h1>

                                    <div class="flex items-center space-x-2">
                                        <!-- Status Button -->
                                            <button id="statusButton" class="inline-flex items-center px-4 py-2 text-white text-sm font-bold rounded-full">
                                                <i class="fas fa-info-circle mr-2"></i> Status
                                            </button>

                                        <!-- Preview Button -->
                                        <a href="{{ route('preview.pdf', ['courseUnit' => $exam['courseUnit']]) }}"
                                            class="inline-flex items-center px-4 py-2 bg-gray-500 text-white text-sm font-bold rounded-full hover:bg-black">
                                            <i class="fas fa-oil-can mr-2"></i> Preview
                                        </a>
                                    </div>
                                </div>

            <!-- Status Modal -->
            <div id="statusOverlay" class="hidden">
                <div id="statusModal">
                    <h2 class="text-lg font-bold">Exam Status</h2>
                    <p id="statusMessage"></p>
                    <ul id="commentList"></ul>
                    <button id="closeStatusModal" class="bg-red-500 text-white px-4 py-2 rounded mt-2">Close</button>
                </div>
            </div>

                                    @php
    $sections = $exam['sections'];
    ksort($sections); // Sort the sections by their keys
                                    @endphp
                                        @foreach ($sections as $sectionName => $questions)
                                                <div class="mt-4 p-4 border-t">
                                                                                                <h2 class="text-lg font-semibold">{{ "Section " . $sectionName }}</h2>

                                                                                                <!-- Display the questions -->
                                                                                                @foreach ($questions as $questionIndex => $question)
                                                                                                    <div  class="mt-2">
                                                                                                        <p>Question {{ $questionIndex + 1 }}:</p>
                                                                                                        <form  class="update-question-form" action="{{ route('update.question', ['courseUnit' => $exam['courseUnit'], 'sectionName' => $sectionName, 'questionIndex' => $questionIndex]) }}" method="POST" class="mb-1">
                                                                                                            @csrf
                                                                                                            @method('PUT') <!-- Method spoofing for PUT request -->
                                                                                                            <textarea id="questionEditor_{{ $sectionName }}_{{ $questionIndex }}" class="tinyMCEEditor" name="question">{!! $question !!}</textarea>

                                                                                                            <!-- Hidden input to track previous content -->
                                                                                                            <input type="hidden" id="previousContent_{{ $sectionName }}_{{ $questionIndex }}" name="previous_question"
                                                                                                                value="{{ $question }}">

                                                                                                            <div class="flex justify-end mt-2">
                                                                                                                <button type="submit" class="bg-gray-500 hover:bg-green-500 text-white font-bold py-1 px-2 text-xs rounded">Update</button>
                                                                                                            </div>

                                                                                                            <!-- Check for success message for adding/updating only -->
                                                                                                            @if (session('success') && session('updatedQuestion') == $sectionName . "_" . $questionIndex)
                                                                                                                <div class="text-sm text-green-600 mt-1 flex justify-end">
                                                                                                                    {{ session('success') }}
                                                                                                                </div>
                                                                                                            @endif

                                                                                                            <!-- Check for error message on the updated/added question only -->
                                                                                                            @if (session('error') && session('updatedQuestion') == $sectionName . "_" . $questionIndex)
                                                                                                                <div class="text-sm text-red-600 mt-1 flex justify-end">
                                                                                                                    {{ session('error') }}
                                                                                                                </div>
                                                                                                            @endif
                                                                                                        </form>

                                                                                                        <!-- Form to delete a question -->
                                                                                                        <form action="{{ route('delete.question', ['courseUnit' => $exam['courseUnit'], 'sectionName' => $sectionName, 'questionIndex' => $questionIndex]) }}" method="POST" class="flex justify-end">
                                                                                                            @csrf
                                                                                                            @method('DELETE')
                                                                                                            <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 text-xs rounded">Delete</button>
                                                                                                        </form>
                                                                                                    </div>
                                                                                                @endforeach

                                                                                                <!-- Error message for number of questions in the section (below questions) -->
                                                                                                @php
        $questionCount = count($questions);
        $errorMessage = '';

        // Check faculty and determine error message based on section and question count
        if (in_array($exam['faculty'], ['FST', 'FBM'])) {
            // FST and FBM requirements
            if ($sectionName == 'A' && $questionCount < 2) {
                $errorMessage = 'Minimum required 2 Case Studies for Section A';
            } elseif ($sectionName == 'B' && $questionCount < 12) {
                $errorMessage = 'Minimum required 12 questions for Section B';
            }
        } elseif ($exam['faculty'] == 'FOE') {
            // FOE requirements - 6 questions for both sections A and B
            if ($sectionName == 'A' && $questionCount < 6) {
                $errorMessage = 'Minimum required 6 questions for Section A';
            } elseif ($sectionName == 'B' && $questionCount < 6) {
                $errorMessage = 'Minimum required 6 questions for Section B';
            }
        } elseif ($exam['faculty'] == 'HEC') {
            // HEC requirements - Section A (20 questions), Section B (10 questions)
            if ($sectionName == 'A' && $questionCount < 20) {
                $errorMessage = 'Minimum required 20 questions for Section A';
            } elseif ($sectionName == 'B' && $questionCount < 10) {
                $errorMessage = 'Minimum required 10 questions for Section B';
            }
        } elseif ($exam['faculty'] == 'FOL') {
            // FOL requirements - Section A (2 questions), Section B (4 questions), Section C (5 essay questions)
            if ($sectionName == 'A' && $questionCount < 2) {
                $errorMessage = 'Minimum required 2 questions for Section A';
            } elseif ($sectionName == 'B' && $questionCount < 4) {
                $errorMessage = 'Minimum required 4 questions for Section B';
            } elseif ($sectionName == 'C' && $questionCount < 5) {
                $errorMessage = 'Minimum required 5 essay questions for Section C';
            }
        }

                                                                                                @endphp

                                                                                                <!-- Display error message if applicable -->
                                                                                                @if ($errorMessage)
                                                                                                    <p class="text-red-700 font-semibold text-md">{{ $errorMessage }}</p>
                                                                                                @endif
                                                                        </div>
                                                    @endforeach
                            </div>
    @empty
        <div class="mt-8 flex flex-col items-center justify-center">
            <img src="/assets/img/404.jpeg" alt="No Data Available" class="w-1/2 max-w-sm mx-auto">
            <p class="mt-4 text-lg font-semibold text-gray-600">No course details available.</p>
        </div>
    @endforelse
</div>



    <!-- Add new question on a section or new section for FOL -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-4">
        <h2 class="text-lg font-semibold flex justify-center">Add New Question</h2>
        <form enctype="multipart/form-data" action="{{ route('add.question', ['courseUnit' => $exam['courseUnit']]) }}" method="POST">
            @csrf
            <!-- Dropdown for selecting the section -->
            <div class="mb-4">
                <label for="sectionSelect" class="block text-gray-700 text-sm font-bold mb-2">Select Section:</label>
                <select id="sectionSelect" name="section"
                    class="block appearance-none w-full bg-white border border-gray-200 text-gray-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:border-gray-500"
                    required>
                    @foreach ($exam['sections'] as $sectionName => $questions)
                        <option value="{{ $sectionName }}">Section {{ $sectionName }}</option>
                    @endforeach
                    <!-- Conditionally add Section C for FOL -->
                    @if ($exam['faculty'] == 'FOL' && !isset($exam['sections']['C']))
                        <option value="C">Section C</option>
                    @endif
                </select>
            </div>
            <!-- TinyMCE Editor for new question -->
            <div class="mb-4">
                <label for="newQuestion" class="block text-gray-700 text-sm font-bold mb-2">Question:</label>
                <textarea id="questionEditor_new" name="newQuestion" class="tinyMCEEditor"></textarea>
            </div>
            <!-- Submit Button -->
            <div class="flex justify-center">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Add Question
                </button>
            </div>
        </form>
    </div>

    <!-- New section for editing instructions -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-4">
        <h2 class="text-lg font-semibold flex justify-center">Edit Instructions</h2>
        <form action="{{ route('update.instructions', ['courseUnit' => $exam['courseUnit']]) }}" method="POST">
            @csrf
            @method('PUT') <!-- Method spoofing for PUT request -->

            <!-- Input field for Section A instructions -->
            <div class="mb-4">
                <label for="sectionA_instructions" class="block text-gray-700 text-sm font-bold mb-2">Section A
                    Instructions:</label>
                <input type="text" id="sectionA_instructions" name="sectionA_instructions"
                    value="{{ $exam['sectionA_instructions'] ?? '' }}"
                    class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded leading-tight focus:outline-none focus:border-gray-500">
            </div>

            <!-- Input field for Section B instructions -->
            <div class="mb-4">
                <label for="sectionB_instructions" class="block text-gray-700 text-sm font-bold mb-2">Section B
                    Instructions:</label>
                <input type="text" id="sectionB_instructions" name="sectionB_instructions"
                    value="{{ $exam['sectionB_instructions'] ?? '' }}"
                    class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded leading-tight focus:outline-none focus:border-gray-500">
            </div>

            <!-- Hidden Input Field for Section C Instructions (Shown only for FOL) -->
            <div id="sectionC_instruction_container" class="mb-4 hidden">
                <label for="sectionC_instructions" class="block text-gray-700 text-sm font-bold mb-2">Section C
                    Instructions:</label>
                <input type="text" id="sectionC_instructions" name="sectionC_instructions"
                    value="{{ $exam['sectionC_instructions'] ?? '' }}"
                    class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded leading-tight focus:outline-none focus:border-gray-500">
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Update Instructions
                </button>
            </div>
        </form>
    </div>


<!-- Separate form for file picker -->
    <div class="mt-8 bg-white rounded-lg shadow-md p-4">
        <h2 class="text-lg font-semibold flex justify-center">Add Marking Guide</h2>

        <form action="{{ route('upload.file', ['courseUnit' => $courseUnit]) }}" method="POST" enctype="multipart/form-data" class="mb-6">
            @csrf

            <!-- File Picker for Upload -->
            <div class="mb-4">
                <label for="attached_file" class="block text-sm font-bold text-gray-700">Attach a Document (Word, PDF, Excel)</label>
                <input type="file" name="attached_file" id="attached_file" accept=".pdf,.doc,.docx,.xls,.xlsx" class="block w-full p-2 border border-gray-300 rounded-md" required>
                <small class="text-gray-500">Max file size: 3MB</small>
            </div>

            <!-- Submit Button for file upload -->
            <div class="flex justify-center space-x-4">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">Upload File</button>

                <!-- Download Marking Guide Button -->
                <a href="{{ route('download.markingGuide', ['courseUnit' => $courseUnit]) }}" class="bg-gray-500 hover:bg-black text-white font-bold py-2 px-4 rounded">
                    Download Marking Guide
                </a>
            </div>

            {{-- Display success message --}}
            @if (session('success_file'))
                <div class="text-sm text-green-600 mt-1 flex justify-center">
                    {{ session('success_file') }}
                </div>
            @endif

            {{-- Display error message --}}
            @if (session('error_file'))
                <div class="text-sm text-red-600 mt-1 flex justify-center">
                    {{ session('error_file') }}
                </div>
            @endif
        </form>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        console.log("Attempting to load: {{ asset('assets/js/special-character-picker.js') }}");

        $.getScript("{{ asset('assets/js/special-character-picker.js') }}")
            .done(function () {
                console.log("✅ special-character-picker.js loaded successfully!");
                loadSpecialCharModal();

                // Initialize Summernote on all textareas with class 'tinyMCEEditor' (previous TinyMCE selector)
                document.querySelectorAll('textarea.tinyMCEEditor').forEach(function (textarea) {
                    if (!textarea.id) {
                        textarea.id = 'summernote_' + Math.random().toString(36).substr(2, 9);
                    }

                    let hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.name = textarea.name + "_content";
                    textarea.parentNode.insertBefore(hiddenInput, textarea.nextSibling);

                    initializeSummernote('#' + textarea.id, hiddenInput);
                });
            })
            .fail(function (jqxhr, settings, exception) {
                console.error("❌ Failed to load special-character-picker.js:", exception);
            });

        // Form Submission Handling (Ensures Content is Stored in Hidden Inputs)
        document.querySelectorAll('.update-question-form').forEach(form => {
            form.addEventListener('submit', function (event) {
                event.preventDefault();

                // Update all hidden inputs before submission
                document.querySelectorAll('textarea.tinyMCEEditor').forEach(textarea => {
                    let editorContent = $(textarea).summernote('code');
                    let hiddenInput = textarea.nextElementSibling;
                    if (hiddenInput && hiddenInput.type === "hidden") {
                        hiddenInput.value = editorContent;
                    }
                });

                console.log("✅ Submitting form...");
                this.submit();
            });
        });
    });

    // **Initialize Summernote**
    function initializeSummernote(selector, hiddenInput) {
        $(selector).summernote({
            height: 100,
            minHeight: 150,
            maxHeight: 500,
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
                ['view', [ 'codeview', 'help']],
                ['custom', ['specialCharButton']] // ✅ Custom Special Characters Button
            ],
            buttons: {
                specialCharButton: SpecialCharButton
            },
            callbacks: {
                onKeyup: function () {
                    $(selector).summernote('saveRange'); // Save cursor position
                },
                onMouseUp: function () {
                    $(selector).summernote('saveRange'); // Save cursor position
                },
                onChange: function (contents) {
                    hiddenInput.value = contents; // Update hidden input with editor content
                }
            }
        });
    }

    // **Custom Special Character Button**
    function SpecialCharButton(context) {
        var ui = $.summernote.ui;
        var button = ui.button({
            contents: '<i class="fas fa-font"></i> Special Characters',
            tooltip: 'Insert Special Character',
            click: function () {
                $(context.layoutInfo.editor).summernote('saveRange');
                showSpecialCharModal();
            }
        });
        return button.render();
    }
</script>

<!-- JavaScript to Show/Hide Section C Instructions for FOL Faculty -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        let faculty = "{{ $exam['faculty'] }}"; // Get faculty from backend
        let sectionCContainer = document.getElementById('sectionC_instruction_container');

        // Show Section C input only if faculty is FOL
        if (faculty === 'FOL') {
            sectionCContainer.classList.remove('hidden');
        }
    });
</script>
    

{{-- status js  --}}
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const statusButton = document.getElementById('statusButton');
        const statusOverlay = document.getElementById('statusOverlay');
        const closeStatusModal = document.getElementById('closeStatusModal');
        const statusMessage = document.getElementById('statusMessage');
        const commentList = document.getElementById('commentList');
        const questionForms = document.querySelectorAll('.update-question-form');

        let exam = @json($exam);
        let faculty = exam.faculty;
        let status = exam.status?.trim().toLowerCase() || "pending";
        let comment = exam.comment ?? null;

        // Status color mapping
        let statusColors = {
            "p": "bg-yellow-500",
            "a": "bg-green-500",
            "d": "bg-red-500" // Updated from "r" to "d" for Declined
        };
        let firstLetter = status.charAt(0);
        statusButton.classList.add(statusColors[firstLetter] || "bg-gray-500");

        function disableScroll() {
            document.body.classList.add('modal-open');
            questionForms.forEach(form => form.classList.add('blur-effect'));
        }

        function enableScroll() {
            document.body.classList.remove('modal-open');
            questionForms.forEach(form => form.classList.remove('blur-effect'));
        }

        let minQuestions = {
            "FST": { "A": 2, "B": 12 },
            "FBM": { "A": 2, "B": 12 },
            "FOE": { "A": 6, "B": 6 },
            "HEC": { "A": 20, "B": 10 },
            "FOL": { "A": 2, "B": 4, "C": 5 }
        };

        let missingSections = [];
        if (faculty in minQuestions) {
            Object.entries(minQuestions[faculty]).forEach(([section, required]) => {
                let count = exam.sections?.[section]?.length || 0;
                if (count < required) {
                    missingSections.push(`Section ${section}: ${count}/${required} questions`);
                }
            });
        }

        statusButton.addEventListener('click', function () {
            let message = `<strong>Status:</strong> <span class="font-semibold">${status.charAt(0).toUpperCase() + status.slice(1)}</span><br>`;

            if (missingSections.length > 0) {
                message += `<p class="text-red-500 mt-2">You need to complete the following before submission:</p><ul>`;
                missingSections.forEach(section => {
                    message += `<li class="text-red-500">${section}</li>`;
                });
                message += `</ul>`;
            } else {
                message += `<p class="text-green-500">✅ All required questions are completed.</p>`;
            }

            commentList.innerHTML = "";
            if (comment) {
                commentList.innerHTML = `<p class="text-gray-800 mt-2">Reviewer Comment:</p><li class="text-gray-700">${comment}</li>`;
            }

            statusMessage.innerHTML = message;
            statusOverlay.classList.add('active');
            disableScroll();
        });

        closeStatusModal.addEventListener('click', function () {
            statusOverlay.classList.remove('active');
            enableScroll();
        });

        statusOverlay.addEventListener('click', function (event) {
            if (event.target === statusOverlay) {
                statusOverlay.classList.remove('active');
                enableScroll();
            }
        });
    });
</script>

</body>
</html>