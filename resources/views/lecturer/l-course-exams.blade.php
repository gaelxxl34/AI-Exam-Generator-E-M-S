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
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    
    <!-- AI Assistant -->
    <script src="{{ asset('assets/js/ai-assistant.js') }}"></script>
    <style>
        /* ============================================
           📄 PAPER-WIDTH EDITOR SYSTEM
           Matches A4 paper dimensions for WYSIWYG
           ============================================ */
        
        /* Paper-like container for each question */
        .paper-editor-container {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            display: flex;
            justify-content: center;
        }

        .paper-editor-wrapper {
            background: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            border: 1px solid #e0e0e0;
            padding: 15mm;
            width: 210mm;
            /* A4 paper width */
            max-width: 100%;
        }

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

        /* ============================================
           🎛️ SUMMERNOTE EDITOR STYLING
           ============================================ */
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
            font-family: 'Times New Roman', Georgia, serif !important;
            font-size: 12pt !important;
            line-height: 1.15 !important; /* MS Word default */
            color: #000 !important;
            padding: 15px 20px !important;
            min-height: 120px;
            max-height: 400px;
            overflow-y: auto;
        }

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
           📋 TABLE STYLING IN EDITOR
           ============================================ */
        .note-editable table {
            border-collapse: collapse !important;
            width: 100% !important;
            max-width: 100% !important;
            border: 1px solid #000000 !important;
            margin: 10px 0 !important;
            table-layout: fixed !important;
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

        /* ============================================
           📝 LIST FORMATTING
           ============================================ */
        .note-editable ul {
            list-style-type: disc !important;
            margin: 0 0 0 25px !important;
            padding-left: 0 !important;
        }

        .note-editable ol {
            list-style-type: decimal !important;
            margin: 0 0 0 25px !important;
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

        /* ============================================
           🔤 TEXT FORMATTING
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

        .note-editable sub {
            vertical-align: sub !important;
            font-size: 0.8em !important;
        }

        .note-editable sup {
            vertical-align: super !important;
            font-size: 0.8em !important;
        }

        /* ============================================
           🖼️ IMAGE HANDLING
           ============================================ */
        .note-editable img {
            max-width: 100% !important;
            height: auto !important;
            display: block;
            margin: 10px auto;
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

        /* ============================================
           ✨ QUESTION LABEL STYLING
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

        .question-label.section-c {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        /* ============================================
           📱 RESPONSIVE
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
                            target="_blank" x-data="{ loading: false }" x-on:submit="loading = true" class="inline">
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
                        <!-- Save All Changes Button -->
                        <button id="saveAllBtn" onclick="saveAllChanges()"
                            class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-bold rounded-full hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all"
                            title="Save all changes (Ctrl+S)">
                            <i class="fas fa-save mr-2"></i> Save All
                            <span class="hidden md:inline ml-1 text-xs opacity-75">(Ctrl+S)</span>
                        </button>
                    </div>
                </div>
                
                <!-- Keyboard Shortcut Notification Toast -->
                <div id="saveToast" class="fixed bottom-4 right-4 bg-green-600 text-white px-4 py-3 rounded-lg shadow-lg transform translate-y-20 opacity-0 transition-all duration-300 z-50 flex items-center">
                    <i class="fas fa-check-circle mr-2"></i>
                    <span id="saveToastMessage">All changes saved!</span>
                </div>

                <!-- Dean Review Notification Banner -->
                @if(isset($exam['dean_edits']) && count($exam['dean_edits']) > 0)
                    @php
                        $editCount = count(array_filter($exam['dean_edits'], fn($e) => ($e['type'] ?? 'edit') === 'edit'));
                        $reviewCount = count(array_filter($exam['dean_edits'], fn($e) => ($e['type'] ?? 'edit') === 'review'));
                    @endphp
                    <div class="mb-4 bg-blue-50 border-l-4 border-blue-500 p-4 rounded-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas fa-user-shield text-blue-500 text-xl"></i>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-semibold text-blue-800">Dean Review Activity</h3>
                                <p class="text-sm text-blue-700 mt-1">
                                    Your exam has been reviewed by the Dean.
                                    @if($editCount > 0)
                                        <span class="font-semibold">{{ $editCount }} question(s) were edited.</span>
                                    @endif
                                    @if($reviewCount > 0)
                                        <span>{{ $reviewCount }} question(s) were marked as reviewed.</span>
                                    @endif
                                </p>
                                <p class="text-xs text-blue-600 mt-1">
                                    Check the "Dean Reviews" tab to see all changes and feedback.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

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
                        @if(isset($exam['dean_edits']) && count($exam['dean_edits']) > 0)
                            <button
                                :class="tab === 'dean_reviews' ? 'border-b-2 border-blue-600 text-blue-600' : 'text-gray-500'"
                                class="px-4 py-2 font-semibold focus:outline-none relative" @click="tab = 'dean_reviews'">
                                <i class="fas fa-user-shield mr-1"></i> Dean Reviews
                                <span class="absolute -top-1 -right-1 bg-blue-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                                    {{ count($exam['dean_edits']) }}
                                </span>
                            </button>
                        @endif
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
                                    <div x-show="open" class="px-6 pb-4 space-y-6">
                                        <!-- Paper Width Info Notice -->
                                        <div class="paper-width-indicator">
                                            <i class="fas fa-ruler-horizontal"></i>
                                            <span><strong>Paper Preview Mode:</strong> Editor width matches A4 paper. What you see here will match the PDF output.</span>
                                        </div>
                                        
                                        @foreach ($questions as $questionIndex => $question)
                                            <div class="mb-6">
                                                <!-- Question Label -->
                                                <div class="question-label section-{{ strtolower($sectionName) }}">
                                                    <i class="fas fa-question-circle"></i>
                                                    Question {{ $sectionName }}{{ $questionIndex + 1 }}
                                                </div>
                                                
                                                <!-- Paper Editor Container -->
                                                <div class="paper-editor-container">
                                                    <div class="paper-editor-wrapper">
                                                        <!-- Save Form: wraps editor and Save button only -->
                                                        <form class="update-question-form w-full"
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
                                                    </div>
                                                </div>
                                                
                                                <!-- Delete Form: outside paper container -->
                                                <form
                                                    action="{{ route('delete.question', ['courseUnit' => $exam['courseUnit'], 'sectionName' => $sectionName, 'questionIndex' => $questionIndex]) }}"
                                                    method="POST" x-data="{ loading: false }" x-on:submit="loading = true"
                                                    class="mt-2">
                                                    @csrf
                                                    <input type="hidden" name="_method" value="DELETE">
                                                    <div class="flex justify-start">
                                                        <button type="submit"
                                                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-3 rounded flex items-center transition-all duration-200 text-sm"
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

                    <!-- Dean Reviews Tab -->
                    @if(isset($exam['dean_edits']) && count($exam['dean_edits']) > 0)
                        <div x-show="tab === 'dean_reviews'" x-cloak>
                            <div class="bg-white rounded-lg shadow-md p-6">
                                <div class="flex items-center justify-between mb-6">
                                    <h2 class="text-lg font-semibold">
                                        <i class="fas fa-user-shield text-blue-500 mr-2"></i>
                                        Dean Review History
                                    </h2>
                                    <span class="text-sm text-gray-500">
                                        {{ count($exam['dean_edits']) }} total actions
                                    </span>
                                </div>
                                
                                <!-- Summary Cards -->
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                                    @php
                                        $edits = array_filter($exam['dean_edits'], fn($e) => ($e['type'] ?? 'edit') === 'edit');
                                        $reviews = array_filter($exam['dean_edits'], fn($e) => ($e['type'] ?? 'edit') === 'review');
                                    @endphp
                                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                        <div class="flex items-center">
                                            <div class="bg-yellow-100 p-3 rounded-full mr-3">
                                                <i class="fas fa-edit text-yellow-600"></i>
                                            </div>
                                            <div>
                                                <div class="text-2xl font-bold text-yellow-700">{{ count($edits) }}</div>
                                                <div class="text-sm text-yellow-600">Questions Edited</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <div class="flex items-center">
                                            <div class="bg-green-100 p-3 rounded-full mr-3">
                                                <i class="fas fa-check-circle text-green-600"></i>
                                            </div>
                                            <div>
                                                <div class="text-2xl font-bold text-green-700">{{ count($reviews) }}</div>
                                                <div class="text-sm text-green-600">Questions Reviewed</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Timeline of Actions -->
                                <div class="border-l-2 border-gray-200 ml-4">
                                    @foreach(array_reverse($exam['dean_edits']) as $action)
                                        @php
                                            $isEdit = ($action['type'] ?? 'edit') === 'edit';
                                            $bgColor = $isEdit ? 'bg-yellow-50 border-yellow-200' : 'bg-green-50 border-green-200';
                                            $iconBg = $isEdit ? 'bg-yellow-500' : 'bg-green-500';
                                            $icon = $isEdit ? 'fa-edit' : 'fa-check';
                                        @endphp
                                        <div class="relative pl-8 pb-6">
                                            <!-- Timeline dot -->
                                            <div class="absolute left-0 transform -translate-x-1/2 w-4 h-4 {{ $iconBg }} rounded-full border-2 border-white"></div>
                                            
                                            <div class="{{ $bgColor }} border rounded-lg p-4">
                                                <div class="flex items-start justify-between">
                                                    <div class="flex items-center">
                                                        <div class="p-2 {{ $iconBg }} text-white rounded-full mr-3">
                                                            <i class="fas {{ $icon }} text-sm"></i>
                                                        </div>
                                                        <div>
                                                            <span class="font-semibold text-gray-800">
                                                                {{ $action['dean_name'] ?? 'Dean' }}
                                                            </span>
                                                            <span class="text-gray-500 text-sm ml-1">
                                                                {{ $isEdit ? 'edited' : 'reviewed' }}
                                                            </span>
                                                            <span class="font-medium text-gray-700">
                                                                Question {{ $action['section'] ?? '?' }}{{ ($action['questionIndex'] ?? 0) + 1 }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <span class="text-xs text-gray-400">
                                                        @if(isset($action['edited_at']))
                                                            {{ \Carbon\Carbon::parse($action['edited_at'])->diffForHumans() }}
                                                        @elseif(isset($action['reviewed_at']))
                                                            {{ \Carbon\Carbon::parse($action['reviewed_at'])->diffForHumans() }}
                                                        @endif
                                                    </span>
                                                </div>
                                                
                                                @if($isEdit && !empty($action['reason']))
                                                    <div class="mt-3 pl-11">
                                                        <div class="text-sm text-gray-600">
                                                            <span class="font-medium">Reason:</span>
                                                            <span class="italic">"{{ $action['reason'] }}"</span>
                                                        </div>
                                                    </div>
                                                @endif
                                                
                                                @if($isEdit && !empty($action['original_content_preview']))
                                                    <div class="mt-2 pl-11">
                                                        <details class="text-sm">
                                                            <summary class="cursor-pointer text-blue-600 hover:text-blue-800">
                                                                View original content preview
                                                            </summary>
                                                            <div class="mt-2 p-2 bg-gray-100 rounded text-gray-600 text-xs">
                                                                {{ $action['original_content_preview'] }}
                                                            </div>
                                                        </details>
                                                    </div>
                                                @endif
                                                
                                                <div class="mt-2 pl-11 text-xs text-gray-500">
                                                    <i class="fas fa-envelope mr-1"></i>
                                                    {{ $action['dean_email'] ?? 'N/A' }}
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                
                                <!-- Help Text -->
                                <div class="mt-6 bg-gray-50 rounded-lg p-4 text-sm text-gray-600">
                                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                    <strong>Note:</strong> All dean edits and reviews are logged automatically. 
                                    If a question was edited, please review the changes to ensure they align with your intent.
                                    You can continue to edit your questions as needed.
                                </div>
                            </div>
                        </div>
                    @endif
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
            // Initialize Summernote on all textareas with class 'tinyMCEEditor'
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

        // **Initialize Summernote with Paper-Width Settings**
        function initializeSummernote(selector, hiddenInput) {
            $(selector).summernote({
                height: 150,
                minHeight: 150,
                maxHeight: 400,
                placeholder: "Type your question here. Use the toolbar for formatting, lists, tables, and images...",
                fontNames: ['Times New Roman'],
                fontNamesIgnoreCheck: ['Times New Roman'],
                defaultFontName: 'Times New Roman',
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
                    aiAssistant: function(context) {
                        var ui = $.summernote.ui;
                        var button = ui.button({
                            contents: '<i class="fas fa-pen-fancy"></i> <span class="hidden sm:inline">AI Help</span>',
                            tooltip: 'AI Writing Assistant - Clean up, check grammar, fix equations',
                            className: 'btn-ai-assistant',
                            click: function() {
                                var $editor = context.$note;
                                var editorId = $editor.attr('id');
                                var selector = editorId ? '#' + editorId : '.summernote';
                                showAIAssistantModal(selector);
                            }
                        });
                        return button.render();
                    }
                },
                fontSizes: ['8', '9', '10', '11', '12', '13', '14', '16', '18', '20', '24'],
                callbacks: {
                    onKeyup: function () {
                        $(selector).summernote('saveRange');
                    },
                    onMouseUp: function () {
                        $(selector).summernote('saveRange');
                    },
                    onChange: function (contents) {
                        let cleanedContent = sanitizeForPdf(contents);
                        hiddenInput.value = cleanedContent;
                    },
                    onInit: function () {
                        $(selector).next('.note-editor').find('.note-editable')
                            .attr('accept-charset', 'UTF-8')
                            .css({
                                'font-family': "'Times New Roman', Georgia, serif",
                                'font-size': '12px',
                                'line-height': '1.6'
                            });
                    },
                    onPaste: function(e) {
                        setTimeout(function() {
                            let content = $(selector).summernote('code');
                            let cleaned = sanitizeForPdf(content);
                            $(selector).summernote('code', cleaned);
                        }, 100);
                    },
                    onImageUpload: function(files) {
                        for (let i = 0; i < files.length; i++) {
                            resizeAndInsertImage(files[i], selector);
                        }
                    }
                }
            });
        }

        // Sanitize content for PDF compatibility
        function sanitizeForPdf(html) {
            let temp = document.createElement('div');
            temp.innerHTML = html;

            // Fix tables
            let tables = temp.querySelectorAll('table');
            tables.forEach(function(table) {
                table.style.width = '100%';
                table.style.maxWidth = '100%';
                table.style.tableLayout = 'fixed';
                table.style.borderCollapse = 'collapse';
                table.setAttribute('border', '1');
                
                let cells = table.querySelectorAll('td, th');
                cells.forEach(function(cell) {
                    cell.style.border = '1px solid #000';
                    cell.style.padding = '6px';
                    cell.style.wordWrap = 'break-word';
                });
            });

            // Fix lists
            let lists = temp.querySelectorAll('ul, ol');
            lists.forEach(function(list) {
                list.style.marginLeft = '25px';
                list.style.paddingLeft = '0';
            });

            // Fix images
            let images = temp.querySelectorAll('img');
            images.forEach(function(img) {
                img.style.maxWidth = '100%';
                img.style.height = 'auto';
            });

            return temp.innerHTML;
        }

        // Resize image before inserting
        function resizeAndInsertImage(file, selector) {
            let reader = new FileReader();
            reader.onload = function(e) {
                let img = new Image();
                img.onload = function() {
                    const maxWidth = 600;
                    let width = img.width;
                    let height = img.height;

                    if (width > maxWidth) {
                        height = (height * maxWidth) / width;
                        width = maxWidth;
                    }

                    let canvas = document.createElement('canvas');
                    canvas.width = width;
                    canvas.height = height;
                    let ctx = canvas.getContext('2d');
                    ctx.drawImage(img, 0, 0, width, height);

                    let resizedDataUrl = canvas.toDataURL('image/jpeg', 0.85);
                    $(selector).summernote('insertImage', resizedDataUrl);
                };
                img.src = e.target.result;
            };
            reader.readAsDataURL(file);
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
    
    <!-- Save All Changes & Keyboard Shortcuts -->
    <script>
        let saveInProgress = false;
        let unsavedChanges = false;
        
        // Track changes in editors
        document.addEventListener('DOMContentLoaded', function() {
            // Mark as having unsaved changes when editor content changes
            const editors = document.querySelectorAll('.tinyMCEEditor, .note-editable');
            editors.forEach(editor => {
                editor.addEventListener('input', function() {
                    unsavedChanges = true;
                    updateSaveAllButton();
                });
            });
            
            // Also track for Summernote editors via MutationObserver
            const noteEditables = document.querySelectorAll('.note-editable');
            noteEditables.forEach(editable => {
                const observer = new MutationObserver(function() {
                    unsavedChanges = true;
                    updateSaveAllButton();
                });
                observer.observe(editable, { childList: true, subtree: true, characterData: true });
            });
        });
        
        function updateSaveAllButton() {
            const saveBtn = document.getElementById('saveAllBtn');
            if (saveBtn && unsavedChanges) {
                saveBtn.classList.add('ring-2', 'ring-yellow-400', 'ring-offset-2');
            }
        }
        
        // Keyboard shortcut: Ctrl+S to save all
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                saveAllChanges();
            }
        });
        
        // Save all forms sequentially
        async function saveAllChanges() {
            if (saveInProgress) return;
            
            const forms = document.querySelectorAll('.update-question-form');
            if (forms.length === 0) {
                showToast('No questions to save', 'info');
                return;
            }
            
            saveInProgress = true;
            const saveBtn = document.getElementById('saveAllBtn');
            const originalContent = saveBtn.innerHTML;
            
            // Update button to show saving state
            saveBtn.innerHTML = '<svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path></svg> Saving...';
            saveBtn.disabled = true;
            
            let successCount = 0;
            let errorCount = 0;
            
            // Process forms sequentially
            for (const form of forms) {
                try {
                    // Sync Summernote content to textarea before submit
                    const textarea = form.querySelector('textarea');
                    if (textarea && $(textarea).summernote) {
                        try {
                            const content = $(textarea).summernote('code');
                            textarea.value = content;
                        } catch (e) {
                            // Summernote might not be initialized
                        }
                    }
                    
                    const formData = new FormData(form);
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (response.ok) {
                        successCount++;
                    } else {
                        errorCount++;
                    }
                } catch (error) {
                    console.error('Error saving form:', error);
                    errorCount++;
                }
            }
            
            // Restore button
            saveBtn.innerHTML = originalContent;
            saveBtn.disabled = false;
            saveBtn.classList.remove('ring-2', 'ring-yellow-400', 'ring-offset-2');
            
            // Show result toast
            if (errorCount === 0) {
                unsavedChanges = false;
                showToast(`All ${successCount} question(s) saved successfully!`, 'success');
            } else if (successCount > 0) {
                showToast(`Saved ${successCount} question(s), ${errorCount} failed`, 'warning');
            } else {
                showToast('Failed to save changes. Please try again.', 'error');
            }
            
            saveInProgress = false;
        }
        
        // Toast notification function
        function showToast(message, type = 'success') {
            const toast = document.getElementById('saveToast');
            const toastMessage = document.getElementById('saveToastMessage');
            
            // Update colors based on type
            toast.className = 'fixed bottom-4 right-4 px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 z-50 flex items-center';
            
            switch(type) {
                case 'success':
                    toast.classList.add('bg-green-600', 'text-white');
                    break;
                case 'warning':
                    toast.classList.add('bg-yellow-500', 'text-white');
                    break;
                case 'error':
                    toast.classList.add('bg-red-600', 'text-white');
                    break;
                default:
                    toast.classList.add('bg-blue-600', 'text-white');
            }
            
            toastMessage.textContent = message;
            
            // Show toast
            toast.classList.remove('translate-y-20', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');
            
            // Hide after 3 seconds
            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 3000);
        }
        
        // Warn user before leaving with unsaved changes
        window.addEventListener('beforeunload', function(e) {
            if (unsavedChanges) {
                e.preventDefault();
                e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                return e.returnValue;
            }
        });
    </script>

    <!-- Alpine.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>