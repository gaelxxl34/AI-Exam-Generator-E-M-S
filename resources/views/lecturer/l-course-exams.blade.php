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
        <div class="container mx-auto px-4">
            @forelse ($exams as $index => $exam)
                @php
                    // Define minimum questions per section by faculty
                    $minQuestions = [
                        'FST' => ['A' => 2, 'B' => 12],
                        'FBM' => ['A' => 2, 'B' => 12],
                        'FOE' => ['A' => 4, 'B' => 4],
                        'HEC' => ['A' => 20, 'B' => 10],
                        'FOL' => ['A' => 2, 'B' => 4, 'C' => 5],
                    ];
                    $faculty = $exam['faculty'];
                    $sections = $exam['sections'] ?? [];
                    $requirementsMet = true;
                    if (isset($minQuestions[$faculty])) {
                        foreach ($minQuestions[$faculty] as $section => $required) {
                            $count = isset($sections[$section]) ? count($sections[$section]) : 0;
                            if ($count < $required) {
                                $requirementsMet = false;
                                break;
                            }
                        }
                    }
                @endphp
                <!-- Preview Requirement Info (always show) -->
                <div class="mb-6">
                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg flex items-start">
                        <div class="flex-shrink-0 mt-1">
                            <i class="fas fa-info-circle text-yellow-400 text-xl"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-yellow-900 font-semibold mb-1">Preview Requirement</p>
                            <p class="text-yellow-800 text-sm mb-1">
                                For your exam to be eligible for submission, you must preview it at least <span
                                    class="font-bold">3 times</span>.<br>
                                This helps ensure you carefully review your exam before sending it for review.
                            </p>
                            <p class="text-yellow-900 text-sm font-medium mt-2">
                                Previewed: <span id="previewCountDisplay_{{ $index }}" class="font-bold">0</span> / 3 times
                            </p>
                            @if (!$requirementsMet)
                                <p class="text-red-600 text-xs mt-1">You must complete all required questions before previewing will count.</p>
                            @endif
                        </div>
                    </div>
                </div>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        var requirementsMet = @json($requirementsMet);
                        var courseKey = 'preview_count_' + @json($exam['courseUnit']);
                        var display = document.getElementById('previewCountDisplay_{{ $index }}');
                        var previewForm = document.getElementById('previewForm_{{ $index }}');
                        if (requirementsMet) {
                            var count = parseInt(localStorage.getItem(courseKey) || '0');
                            if (display) display.textContent = count;
                            if (previewForm) {
                                previewForm.addEventListener('submit', function (e) {
                                    count = parseInt(localStorage.getItem(courseKey) || '0');
                                    count++;
                                    localStorage.setItem(courseKey, count);
                                    if (display) display.textContent = count;
                                }, true);
                            }
                        } else {
                            if (display) display.textContent = 0;
                            if (previewForm) {
                                previewForm.addEventListener('submit', function (e) {
                                    // Prevent incrementing if requirements not met
                                    localStorage.setItem(courseKey, 0);
                                    if (display) display.textContent = 0;
                                }, true);
                            }
                        }
                    });
                </script>
                <!-- Header with course info and status -->
                <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">{{ $exam['courseUnit'] }}</h1>
                        <span class="text-sm text-gray-500">Faculty: {{ $exam['faculty'] }}</span>
                    </div>
                    <div class="flex items-center space-x-2 mt-2 md:mt-0">
                        <button id="statusButton"
                            class="inline-flex items-center px-4 py-2 text-white text-sm font-bold rounded-full">
                            <i class="fas fa-info-circle mr-2"></i> Status
                        </button>
                        <form id="previewForm_{{ $index }}" action="{{ route('preview.pdf', ['courseUnit' => $exam['courseUnit']]) }}" method="GET"
                            x-data="{ loading: false }" x-on:submit="loading = true" class="inline">
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-gray-500 text-white text-sm font-bold rounded-full hover:bg-black focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-700"
                                :disabled="loading">
                                <span x-show="!loading"><i class="fas fa-oil-can mr-2"></i> Preview</span>
                                <span x-show="loading" class="flex items-center">
                                    <svg class="animate-spin h-4 w-4 mr-2 text-white" xmlns="http://www.w3.org/2000/svg"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                                    </svg>
                                    Previewing...
                                </span>
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Tabs -->
                <div x-data="{ tab: 'questions' }" class="mb-8">
                    <nav class="flex space-x-4 border-b mb-4">
                        <button
                            :class="tab === 'questions' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500'"
                            class="px-4 py-2 font-semibold focus:outline-none" @click="tab = 'questions'">Questions</button>
                        <button
                            :class="tab === 'instructions' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500'"
                            class="px-4 py-2 font-semibold focus:outline-none"
                            @click="tab = 'instructions'">Instructions</button>
                        <button
                            :class="tab === 'marking' ? 'border-b-2 border-indigo-600 text-indigo-600' : 'text-gray-500'"
                            class="px-4 py-2 font-semibold focus:outline-none" @click="tab = 'marking'">Marking
                            Guide</button>
                    </nav>

                    <!-- Questions Tab -->
                    <div x-show="tab === 'questions'">
                        <!-- Floating Add Question Button -->
                        <button @click="$refs.addQuestionModal.showModal()"
                            class="fixed bottom-8 right-8 z-50 bg-green-600 hover:bg-green-700 text-white rounded-full shadow-lg p-4 flex items-center space-x-2 transition-all duration-200">
                            <i class="fas fa-plus"></i>
                            <span class="hidden md:inline">Add Question</span>
                        </button>
                        <!-- Add Question Modal -->
                        <dialog x-ref="addQuestionModal" class="rounded-lg shadow-lg w-full max-w-lg p-0">
                            <form enctype="multipart/form-data"
                                action="{{ route('add.question', ['courseUnit' => $exam['courseUnit']]) }}" method="POST"
                                x-data="{ loading: false }" x-on:submit="loading = true">
                                @csrf
                                <div class="p-6">
                                    <h2 class="text-lg font-semibold mb-4">Add New Question</h2>
                                    <div class="mb-4">
                                        <label for="sectionSelect" class="block text-gray-700 text-sm font-bold mb-2">Select
                                            Section:</label>
                                        <select id="sectionSelect" name="section"
                                            class="block w-full bg-white border border-gray-200 text-gray-700 py-2 px-3 rounded leading-tight focus:outline-none focus:border-gray-500"
                                            required>
                                            @foreach ($exam['sections'] as $sectionName => $questions)
                                                <option value="{{ $sectionName }}">Section {{ $sectionName }}</option>
                                            @endforeach
                                            @if ($exam['faculty'] == 'FOL' && !isset($exam['sections']['C']))
                                                <option value="C">Section C</option>
                                            @endif
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <label for="newQuestion"
                                            class="block text-gray-700 text-sm font-bold mb-2">Question:</label>
                                        <textarea id="questionEditor_new" name="newQuestion"
                                            class="tinyMCEEditor"></textarea>
                                    </div>
                                    <div class="flex justify-end space-x-2">
                                        <button type="button" @click="$refs.addQuestionModal.close()"
                                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">Cancel</button>
                                        <button type="submit"
                                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center transition-all duration-200"
                                            :disabled="loading">
                                            <template x-if="!loading">
                                                <span><i class="fas fa-plus mr-2"></i>Add Question</span>
                                            </template>
                                            <template x-if="loading">
                                                <span class="flex items-center"><svg
                                                        class="animate-spin h-5 w-5 mr-2 text-white"
                                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                            stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor"
                                                            d="M4 12a8 8 0 018-8v8z"></path>
                                                    </svg>Adding Question...</span>
                                            </template>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </dialog>

                        <!-- Section Accordions -->
                        <div class="space-y-4">
                            @php ksort($exam['sections']); @endphp
                            @foreach ($exam['sections'] as $sectionName => $questions)
                                <div x-data="{ open: true }" class="border rounded-lg shadow-sm bg-white">
                                    <button @click="open = !open"
                                        class="w-full flex justify-between items-center px-6 py-4 text-lg font-semibold focus:outline-none">
                                        <span>Section {{ $sectionName }}</span>
                                        <svg :class="open ? 'rotate-180' : ''" class="h-5 w-5 transition-transform" fill="none"
                                            stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </button>
                                    <div x-show="open" class="px-6 pb-4 space-y-4">
                                        @foreach ($questions as $questionIndex => $question)
                                            <div class="bg-gray-50 rounded-lg p-4 shadow flex flex-col">
                                                <div class="font-semibold text-gray-700 mb-1">Question
                                                    {{ $questionIndex + 1 }}
                                                </div>
                                                <!-- Save Form: wraps editor and Save button only -->
                                                <form class="update-question-form w-full mb-2"
                                                    action="{{ route('update.question', ['courseUnit' => $exam['courseUnit'], 'sectionName' => $sectionName, 'questionIndex' => $questionIndex]) }}"
                                                    method="POST" x-data="{ loading: false }" x-on:submit="loading = true">
                                                    @csrf
                                                    @method('PUT')
                                                    <textarea id="questionEditor_{{ $sectionName }}_{{ $questionIndex }}"
                                                        class="tinyMCEEditor" name="question">{!! $question !!}</textarea>
                                                    <div class="flex justify-end mt-4">
                                                        <button type="submit"
                                                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-1 px-3 rounded flex items-center transition-all duration-200"
                                                            :disabled="loading">
                                                            <template x-if="!loading">
                                                                <span><i class="fas fa-save mr-1"></i>Save</span>
                                                            </template>
                                                            <template x-if="loading">
                                                                <span class="flex items-center"><svg
                                                                        class="animate-spin h-4 w-4 mr-1 text-white"
                                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                        viewBox="0 0 24 24">
                                                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                            stroke="currentColor" stroke-width="4"></circle>
                                                                        <path class="opacity-75" fill="currentColor"
                                                                            d="M4 12a8 8 0 018-8v8z"></path>
                                                                    </svg>Saving...</span>
                                                            </template>
                                                        </button>
                                                    </div>
                                                </form>
                                                <!-- Delete Form: only the button, not nested, sibling to Save form -->
                                                <form
                                                    action="{{ route('delete.question', ['courseUnit' => $exam['courseUnit'], 'sectionName' => $sectionName, 'questionIndex' => $questionIndex]) }}"
                                                    method="POST" x-data="{ loading: false }" x-on:submit="loading = true"
                                                    class="w-full">
                                                    @csrf
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <div class="flex justify-start">
                                                        <button type="submit"
                                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded flex items-center transition-all duration-200"
                                                            :disabled="loading"
                                                            onclick="return confirm('Are you sure you want to delete this question?')">
                                                            <template x-if="!loading">
                                                                <span><i class="fas fa-trash-alt mr-1"></i>Delete</span>
                                                            </template>
                                                            <template x-if="loading">
                                                                <span class="flex items-center"><svg
                                                                        class="animate-spin h-4 w-4 mr-1 text-white"
                                                                        xmlns="http://www.w3.org/2000/svg" fill="none"
                                                                        viewBox="0 0 24 24">
                                                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                                            stroke="currentColor" stroke-width="4"></circle>
                                                                        <path class="opacity-75" fill="currentColor"
                                                                            d="M4 12a8 8 0 018-8v8z"></path>
                                                                    </svg>Deleting...</span>
                                                            </template>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        @endforeach
                                        <!-- Error message for number of questions in the section (below questions) -->
                                        @php
                    $questionCount = count($questions);
                    $errorMessage = '';
                    if (in_array($exam['faculty'], ['FST', 'FBM'])) {
                        if ($sectionName == 'A' && $questionCount < 2) {
                            $errorMessage = 'Minimum required 2 Case Studies for Section A';
                        } elseif ($sectionName == 'B' && $questionCount < 12) {
                            $errorMessage = 'Minimum required 12 questions for Section B';
                        }
                    } elseif ($exam['faculty'] == 'FOE') {
                        if ($sectionName == 'A' && $questionCount < 4) {
                            $errorMessage = 'Minimum required 4 questions for Section A';
                        } elseif ($sectionName == 'B' && $questionCount < 4) {
                            $errorMessage = 'Minimum required 4 questions for Section B';
                        }
                    } elseif ($exam['faculty'] == 'HEC') {
                        if ($sectionName == 'A' && $questionCount < 20) {
                            $errorMessage = 'Minimum required 20 questions for Section A';
                        } elseif ($sectionName == 'B' && $questionCount < 10) {
                            $errorMessage = 'Minimum required 10 questions for Section B';
                        }
                    } elseif ($exam['faculty'] == 'FOL') {
                        if ($sectionName == 'A' && $questionCount < 2) {
                            $errorMessage = 'Minimum required 2 questions for Section A';
                        } elseif ($sectionName == 'B' && $questionCount < 4) {
                            $errorMessage = 'Minimum required 4 questions for Section B';
                        } elseif ($sectionName == 'C' && $questionCount < 5) {
                            $errorMessage = 'Minimum required 5 essay questions for Section C';
                        }
                    }
                                        @endphp
                                        @if ($errorMessage)
                                            <p class="text-red-700 font-semibold text-md mt-2">{{ $errorMessage }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Instructions Tab -->
                    <div x-show="tab === 'instructions'">
                        <div class="bg-white rounded-lg shadow-md p-6 max-w-xl mx-auto">
                            <h2 class="text-lg font-semibold flex justify-center mb-4">Edit Instructions</h2>
                            <form action="{{ route('update.instructions', ['courseUnit' => $exam['courseUnit']]) }}"
                                method="POST" x-data="{ loading: false }" x-on:submit="loading = true">
                                @csrf
                                @method('PUT')
                                <div class="mb-4">
                                    <label for="sectionA_instructions"
                                        class="block text-gray-700 text-sm font-bold mb-2">Section A Instructions:</label>
                                    <input type="text" id="sectionA_instructions" name="sectionA_instructions"
                                        value="{{ $exam['sectionA_instructions'] ?? '' }}"
                                        class="block w-full bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded leading-tight focus:outline-none focus:border-gray-500">
                                </div>
                                <div class="mb-4">
                                    <label for="sectionB_instructions"
                                        class="block text-gray-700 text-sm font-bold mb-2">Section B Instructions:</label>
                                    <input type="text" id="sectionB_instructions" name="sectionB_instructions"
                                        value="{{ $exam['sectionB_instructions'] ?? '' }}"
                                        class="block w-full bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded leading-tight focus:outline-none focus:border-gray-500">
                                </div>
                                <div id="sectionC_instruction_container" class="mb-4 hidden">
                                    <label for="sectionC_instructions"
                                        class="block text-gray-700 text-sm font-bold mb-2">Section C Instructions:</label>
                                    <input type="text" id="sectionC_instructions" name="sectionC_instructions"
                                        value="{{ $exam['sectionC_instructions'] ?? '' }}"
                                        class="block w-full bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded leading-tight focus:outline-none focus:border-gray-500">
                                </div>
                                <div class="flex justify-center">
                                    <button type="submit"
                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center transition-all duration-200"
                                        :disabled="loading">
                                        <template x-if="!loading">
                                            <span><i class="fas fa-edit mr-2"></i>Update Instructions</span>
                                        </template>
                                        <template x-if="loading">
                                            <span class="flex items-center"><svg
                                                    class="animate-spin h-5 w-5 mr-2 text-white"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                        stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z">
                                                    </path>
                                                </svg>Updating Instructions...</span>
                                        </template>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Marking Guide Tab -->
                    <div x-show="tab === 'marking'" x-cloak>
                        <div class="bg-white rounded-lg shadow-md p-6 max-w-xl mx-auto">
                            <h2 class="text-lg font-semibold flex justify-center mb-4">Add Marking Guide</h2>
                            <form action="{{ route('upload.file', ['courseUnit' => $courseUnit]) }}" method="POST"
                                enctype="multipart/form-data" class="mb-6" x-data="{ loading: false }"
                                x-on:submit="loading = true">
                                @csrf
                                <div class="mb-4">
                                    <label for="attached_file" class="block text-sm font-bold text-gray-700">Attach a
                                        Document (Word, PDF, Excel)</label>
                                    <input type="file" name="attached_file" id="attached_file"
                                        accept=".pdf,.doc,.docx,.xls,.xlsx"
                                        class="block w-full p-2 border border-gray-300 rounded-md" required>
                                    <small class="text-gray-500">Max file size: 3MB</small>
                                </div>
                                <div class="flex justify-center space-x-4">
                                    <button type="submit"
                                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded flex items-center transition-all duration-200"
                                        :disabled="loading">
                                        <span class="flex items-center">
                                            <i class="fas fa-upload mr-2"></i>
                                            <span x-show="!loading">Upload Marking Guide</span>
                                            <span x-show="loading" class="flex items-center ml-2">
                                                <svg class="animate-spin h-5 w-5 mr-2 text-white"
                                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                        stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z">
                                                    </path>
                                                </svg>
                                                Uploading...
                                            </span>
                                        </span>
                                    </button>
                                    <a href="{{ route('download.markingGuide', ['courseUnit' => $courseUnit]) }}"
                                        class="bg-gray-500 hover:bg-black text-white font-bold py-2 px-4 rounded">Download
                                        Marking Guide</a>
                                </div>
                                @if (session('success_file'))
                                    <div class="text-sm text-green-600 mt-1 flex justify-center">{{ session('success_file') }}
                                    </div>
                                @endif
                                @if (session('error_file'))
                                    <div class="text-sm text-red-600 mt-1 flex justify-center">{{ session('error_file') }}</div>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
                <!-- Status Modal (unchanged) -->
                <div id="statusOverlay" class="hidden">
                    <div id="statusModal">
                        <h2 class="text-lg font-bold">Exam Status</h2>
                        <p id="statusMessage"></p>
                        <ul id="commentList"></ul>
                        <button id="closeStatusModal" class="bg-red-500 text-white px-4 py-2 rounded mt-2">Close</button>
                    </div>
                </div>
            @empty
                <div class="mt-8 flex flex-col items-center justify-center">
                    <img src="/assets/img/404.jpeg" alt="No Data Available" class="w-1/2 max-w-sm mx-auto">
                    <p class="mt-4 text-lg font-semibold text-gray-600">No course details available.</p>
                </div>
            @endforelse
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
                    // Remove event.preventDefault() so form submits normally
                    var btn = form.querySelector('.updateBtn');
                    var spinner = form.querySelector('.updateSpinner');
                    btn.disabled = true;
                    spinner.classList.remove('hidden');
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
                    ['view', ['codeview', 'help']],
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


    {{-- status js --}}
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
                "FOE": { "A": 4, "B": 4 },
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

    <!-- Alpine.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>