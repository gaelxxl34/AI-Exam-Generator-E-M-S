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
                <h1 class="text-xl font-bold p-4 border-b text-center">{{ "Exam " . ($index + 1) . " - " . $exam['courseUnit'] }}</h1>
                @php
                    $sections = $exam['sections'];
                    ksort($sections); // Sorts the sections by their keys
                @endphp
                @foreach ($sections as $sectionName => $questions)
                    <div class="mt-4 p-4 border-t">
                        <h2 class="text-lg font-semibold">{{ "Section " . $sectionName }}</h2>
                            @foreach ($questions as $questionIndex => $question)
                                <div class="mt-2">
                                    <p>Question {{ $questionIndex + 1 }}:</p>
                                    <form action="{{ route('update.question', ['courseUnit' => $exam['courseUnit'], 'sectionName' => $sectionName, 'questionIndex' => $questionIndex]) }}" method="POST" class="mb-1">
                                        @csrf
                                        @method('PUT') <!-- Method spoofing for PUT request -->
                                        <textarea id="questionEditor" class="summernote" name="question">{!! $question !!}</textarea>
                                        <div class="flex justify-end mt-2">
                                            <button type="submit" class="bg-gray-500 hover:bg-green-500 text-white font-bold py-1 px-2 text-xs rounded">Update</button>
                                        </div>
                                    </form>
                                    <form action="{{ route('delete.question', ['courseUnit' => $exam['courseUnit'], 'sectionName' => $sectionName, 'questionIndex' => $questionIndex]) }}" method="POST" class="flex justify-end">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 text-xs rounded">Delete</button>
                                    </form>
                                    <!-- Check for success message -->
                                    @if (session('success'))
                                        <div class="text-sm text-green-600 mt-1 flex justify-end">
                                            {{ session('success') }}
                                        </div>
                                    @endif
                                    <!-- Check for error message -->
                                    @if (session('error'))
                                        <div class="text-sm text-red-600 mt-1 flex justify-end">
                                            {{ session('error') }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                    </div>
                @endforeach
            </div>
        @empty
            <div class="mt-8 flex flex-col items-center justify-center ">
                <img src="/assets/img/404.jpeg" alt="No Data Available" class="w-1/2 max-w-sm mx-auto">
                <p class="mt-4 text-lg font-semibold text-gray-600">No course details available.</p>
            </div>
        @endforelse
    </div>

    <div class="mt-8 bg-white rounded-lg shadow-md p-4">
        <h2 class="text-lg font-semibold flex justify-center">Add New Question</h2>
        <form action="{{ route('add.question', ['courseUnit' => $exam['courseUnit']]) }}" method="POST">
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
            <!-- Summernote Editor for new question -->
            <div class="mb-4">
                <label for="newQuestion" class="block text-gray-700 text-sm font-bold mb-2">Question:</label>
                <textarea id="questionEditor" name="newQuestion" class="summernote"></textarea>
            </div>
            <!-- Submit Button -->
            <div class="flex justify-center">
                <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                    Add Question
                </button>
            </div>
                                                <!-- Check for success message -->
                                    @if (session('succes'))
                                        <div class="text-sm text-green-600 mt-1 flex justify-end">
                                            {{ session('success') }}
                                        </div>
                                    @endif
                                    <!-- Check for error message -->
                                    @if (session('erreur'))
                                        <div class="text-sm text-red-600 mt-1 flex justify-end">
                                            {{ session('error') }}
                                        </div>
                                    @endif
        </form>
    </div>


</div>

<script>
    // Initialize the Summernote editors
    $(document).ready(function() {
        $('textarea[id^="questionEditor"]').each(function() {
            $(this).summernote({
                placeholder: 'Edit question content here...',
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
                        // Update logic to store the edited content
                    }
                }
            });
        });
    });


</script>





    
</body>
</html>