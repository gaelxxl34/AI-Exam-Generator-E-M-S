<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Generated Exam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>

    <style>
        /* Tailwind resets list styling; restore bullets inside editors */
        .note-editable ul {
            list-style: disc !important;
            margin-left: 1.5rem !important;
        }

        .note-editable ol {
            list-style: decimal !important;
            margin-left: 1.5rem !important;
        }
    </style>
</head>

<body>

    @include('partials.gen-navbar')

    <div class="p-4 sm:ml-64 mt-20 ">

        <div class="container mx-auto px-4">
            @if (!empty($sections))
                <div class="mt-8 bg-white rounded-lg shadow-md">
                    <h1 class="text-xl font-bold p-4 border-b text-center">{{ "Exam - " . $courseUnit }}</h1>
                    @php
                        $sortedSections = ['A' => null, 'B' => null];
                        foreach ($sections as $sectionName => $questions) {
                            if (array_key_exists($sectionName, $sortedSections)) {
                                $sortedSections[$sectionName] = $questions;
                            }
                        }
                    @endphp
                    @foreach ($sortedSections as $sectionName => $questions)
                        @if (!is_null($questions))
                            <div class="mt-4 p-4 border-t">
                                <h2 class="text-lg font-semibold">{{ "Section " . $sectionName }}</h2>
                                @foreach ($questions as $questionIndex => $question)
                                    <div class="mt-2">
                                        <p>Question {{ $questionIndex + 1 }}:</p>
                                        <div class="p-4 rounded" style="font-size: 16px;">{!! $question !!}</div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @endforeach
                </div>
            @else
                <div class="mt-8 flex flex-col items-center justify-center">
                    <!-- Provide a valid image URL for 'No Data Available' -->
                    <img src="../assets/img/404.jpeg" alt="No Data Available" class="w-1/2 max-w-sm mx-auto">
                    <p class="mt-4 text-lg font-semibold text-gray-600">No course details available.</p>
                </div>
            @endif
        </div>



        <div class="flex justify-center mt-8">
            <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4 max-w-2xl w-full">
                <form action="{{ route('download.exam') }}" method="POST" target="_blank" class="space-y-4">
                    @csrf
                    <input type="hidden" name="courseUnit" value="{{ $courseUnit }}">

                    <div class="mb-5"> <!-- increased bottom margin -->
                        <label for="facultyOf" class="block text-sm font-medium text-gray-700">Select Faculty:</label>
                        <div class="relative">
                            <select id="facultyOf" name="facultyOf"
                                class="block w-full p-2 border border-gray-300 rounded-md shadow-sm text-gray-700 focus:outline-none focus:border-red-500 focus:ring-1 focus:ring-red-500"
                                required>
                                <option value="">Please choose</option>
                                <option value="Faculty of Science and Technology">Faculty of Science and Technology
                                    (FST)</option>
                                <option value="Faculty of Business Management">Faculty of Business Management (FBM)
                                </option>
                                <option value="Faculty of Engineering">Faculty of Engineering (FOE)</option>
                                <option value="Faculty of Law">Faculty of Law (FOL)</option>
                                <option value="Higher Education Certificate ">Higher Education Certificate (HEC)
                                </option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <input type="text" name="examPeriod" placeholder="EXAM PERIOD" required
                            class="w-full px-3 py-2 border rounded shadow-sm">
                    </div>
                    <div>
                        <input type="date" name="date" placeholder="DATE" required
                            class="w-full px-3 py-2 border rounded shadow-sm">
                    </div>
                    <div>
                        <input type="text" name="time" placeholder="TIME" required
                            class="w-full px-3 py-2 border rounded shadow-sm">
                    </div>

                    <div class="mb-5">
                        <label for="generalInstructions" class="block text-sm font-medium text-gray-700">General
                            Instructions:</label>
                        <textarea id="generalInstructions" name="generalInstructions"></textarea>
                    </div>

                    <div class="flex justify-center mt-8">
                        <button type="submit"
                            class="inline-block px-6 py-2 border border-transparent text-base font-medium rounded-md text-white bg-black hover:bg-red-700">
                            Download Exam
                        </button>
                    </div>
                </form>

            </div>
        </div>




    </div>


    <script>
        $(document).ready(function () {
            var initialContent = `<p>1. Read the instructions very carefully</p><p>2. The time allowed for this examination is STRICTLY three hours&nbsp;&nbsp;</p><p>3. Read each question carefully before you attempt and allocate your time equally between all the Sections</p><p>4. Write clearly and legibly. Illegible handwriting cannot be marked</p><p>5. Number the questions you have attempted</p><p>6. Use of appropriate workplace examples to illustrate your answers will earn you bonus marks</p><p>7. Any examination malpractice detected will lead to automatic disqualification.&nbsp;</p><p><br></p><p style="text-align: center; "><u><b>DO NOT WRITE ANYTHING ON THE QUESTION PAPER</b></u></p>`;

            $('#generalInstructions').summernote({
                placeholder: 'General Instructions',
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
            }); $('#generalInstructions').summernote('code', initialContent);
        });
    </script>

</body>

</html>