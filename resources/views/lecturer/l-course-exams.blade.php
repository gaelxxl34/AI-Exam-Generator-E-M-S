<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
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

    </style>
</head>
<body>
    
    @include('partials.lecturer-navbar')

<div class="p-4 sm:ml-64 mt-20">

<div class="container mx-auto px-4">
    @forelse ($exams as $index => $exam)
        <div class="mt-8 bg-white rounded-lg shadow-md">
            <div class="flex items-center justify-between">
                <h1 class="text-xl font-bold p-4 border-b text-center">{{$exam['courseUnit'] }}</h1>
                
                <!-- Preview Button with Oil (Gear) Icon -->
                <a href="{{ route('preview.pdf', ['courseUnit' => $exam['courseUnit']]) }}" class="inline-flex items-center px-4 py-2 bg-gray-500 text-white text-sm font-bold rounded-full hover:bg-black">
                    <i class="fas fa-oil-can mr-2"></i> Preview
                </a>
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
                        <div class="mt-2">
                            <p>Question {{ $questionIndex + 1 }}:</p>
                            <form action="{{ route('update.question', ['courseUnit' => $exam['courseUnit'], 'sectionName' => $sectionName, 'questionIndex' => $questionIndex]) }}" method="POST" class="mb-1">
                                @csrf
                                @method('PUT') <!-- Method spoofing for PUT request -->
                                <textarea id="questionEditor_{{ $sectionName }}_{{ $questionIndex }}" class="tinyMCEEditor" name="question">{!! $question !!}</textarea>
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
                                $errorMessage = 'Minimum required 2 questions for Section A';
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




    <div class="mt-8 bg-white rounded-lg shadow-md p-4">
        <h2 class="text-lg font-semibold flex justify-center">Add New Question</h2>
        <form enctype="multipart/form-data" action="{{ route('add.question', ['courseUnit' => $exam['courseUnit']]) }}" method="POST">
            @csrf
            <!-- Dropdown for selecting the section -->
            <div class="mb-4">
                <label for="sectionSelect" class="block text-gray-700 text-sm font-bold mb-2">Select Section:</label>
                <select id="sectionSelect" name="section" class="block appearance-none w-full bg-white border border-gray-200 text-gray-700 py-2 px-3 pr-8 rounded leading-tight focus:outline-none focus:border-gray-500" required>
                    @foreach ($exam['sections'] as $sectionName => $questions)
                        <option value="{{ $sectionName }}">Section {{ $sectionName }}</option>
                    @endforeach
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
                <label for="sectionA_instructions" class="block text-gray-700 text-sm font-bold mb-2">Section A Instructions:</label>
                <input type="text" id="sectionA_instructions" name="sectionA_instructions" value="{{ $exam['sectionA_instructions'] }}" class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded leading-tight focus:outline-none focus:border-gray-500">
            </div>

            <!-- Input field for Section B instructions -->
            <div class="mb-4">
                <label for="sectionB_instructions" class="block text-gray-700 text-sm font-bold mb-2">Section B Instructions:</label>
                <input type="text" id="sectionB_instructions" name="sectionB_instructions" value="{{ $exam['sectionB_instructions'] }}" class="block appearance-none w-full bg-white border border-gray-300 text-gray-700 py-2 px-3 rounded leading-tight focus:outline-none focus:border-gray-500">
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
document.addEventListener('DOMContentLoaded', function() {
    // Initialize TinyMCE on all textareas with the class 'tinyMCEEditor'
    tinymce.init({
        selector: 'textarea.tinyMCEEditor',
        plugins: 'table link image code charmap preview fullscreen anchor MathType lists', // Add MathType, lists, and image plugins
        toolbar: 'undo redo | formatselect | bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist outdent indent | link image charmap MathType | fullscreen preview code',
        menubar: 'file edit view insert format tools table help',
        height: 200,
        setup: function(editor) {
            editor.on('change', function() {
                let content = editor.getContent();
                console.log('Editor content changed:', content);
            });
        }
    });
});
</script>


    
<!-- Include TinyMCE -->
<script src="https://cdn.tiny.cloud/1/2mfwgn8z2yosxyoi5n0rqda97mb7eeb61rys0b2tj0ixzssy/tinymce/5/tinymce.min.js" referrerpolicy="origin"></script>
<!-- Include MathJax -->
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
</body>
</html>