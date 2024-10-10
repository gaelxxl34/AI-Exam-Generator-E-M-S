<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Preview - {{ $courseUnit }}</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 14px;
            margin: 15px;
        }
        .section {
            padding: 8px;
            margin-bottom: 15px; /* Add margin below each section */
        }
        .question {
            margin-top: 20px;
            margin-bottom: 10px; /* Reduce space between questions */
        }
        .question p {
            margin: 0;
            font-size: 16px;
            font-weight: bold;
        }
        .question-content {
            padding-left: 20px; /* Indent the question content slightly for better visual structure */
            margin-top: 5px; /* Reduce margin above question content */
            margin-bottom: 10px; /* Reduce space below question content */
            font-size: 15px;
            font-weight: normal; /* Ensure normal weight for question content */
            line-height: 1.6; /* Slightly increase line-height for readability */
        }
        .question-content * {
            font-weight: normal !important; /* Force normal weight on all elements inside question-content */
        }
        .instructions-inline {
            display: inline; /* Ensure section title and instructions are on the same line */
            font-size: 16px;
        }
        h2 {
            display: inline;
            font-weight: bold;
            font-size: 18px;
        }
        .section-header {
            font-weight: bold;
            font-size: 18px;
            margin-right: 10px;
        }
    </style>
</head>
<body>

<!-- Course Unit Title -->
<h1 style="text-align: center; font-size: 24px; font-weight: bold;">{{ $courseUnit }}</h1>

<!-- Instructions and Sections -->
@php
    ksort($sections);
@endphp

@foreach ($sections as $sectionName => $questions)
    <div class="section">
        <!-- Section Title and Instructions on Same Line -->
        <h2 class="section-header">Section {{ $sectionName }}</h2>
        @if ($sectionName == 'A' && isset($sectionAInstructions))
            <span class="instructions-inline">{!! $sectionAInstructions !!}</span>
        @elseif ($sectionName == 'B' && isset($sectionBInstructions))
            <span class="instructions-inline">{!! $sectionBInstructions !!}</span>
        @endif

        <!-- Display questions for the section -->
        @foreach ($questions as $questionIndex => $question)
            <div class="question">
                <!-- Question Title -->
                <p>Question {{ $questionIndex + 1 }}:</p>
                <!-- Question Content -->
                <div class="question-content">{!! $question !!}</div>
            </div>
        @endforeach
    </div>
@endforeach

</body>
</html>
