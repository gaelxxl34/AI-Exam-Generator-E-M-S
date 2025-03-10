<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Exam - {{ $courseUnit }}</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif;
            font-size: 14px;
            margin: 15px;
        }
   .exam-cover {
            text-align: center;
            page-break-after: always;
            
        }
        .exam-cover img {
            margin: 0 auto;
            display: block;
            width: 220px;
            height: 70px;
        }
        .faculty-name,{
            margin-bottom: 10px;
            margin-top: 40px;
            text-transform: uppercase;
            font-weight: bold;
        } 
        .uppercase {
            text-transform: uppercase;
        }
        .exam-date {
            margin-bottom: 30px;
            margin-top: 10px;
            font-weight: bold;
        }
        .info-left, .info-right {
            /* margin: 20px 50px; */
            text-align: left;
            font-weight: bold;
        }
        .info-right {
            text-align: right;
        }
        .instructions {
            font-weight: bold;
            
            margin-top: 20px;
            text-transform: uppercase;
            text-decoration: underline;
        }
        .instructions p:first-child {
            
        }
        .clear {
            clear: both;
        }
        .section {
            padding: 8px; /* Increased padding for questions */
        }
        .question {
            margin-top: 6px; /* Increased margin above each question for readability */
            margin-bottom: 10px; /* Added margin below each question */
        }
        .question p {
            font-weight: normal; /* Only the question number should be bold */
            margin: 0; /* Keep margins tight around text */
        }
        .question-content {
            padding: 12px; /* Small padding around content for visual separation */
        }
        h2 {
            font-weight: bold; /* Ensure section headers are bold */
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-bordered {
            border: 1px solid black;
        }

        td,th {
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
<div class="exam-cover">
    <img src="https://iuea.ac.ug/sitepad-data/uploads//2020/11/Website-Logo.png" alt="University Logo">
    <div class="faculty-name">FACULTY OF {{ $facultyOf }}</div>
    <div class="exam-date uppercase">END OF SEMESTER EXAMINATIONS - {{ $examPeriod }}</div>
    <div class="info-left">
        <p>PROGRAMME: {{ $program }}</p>
        <p class="uppercase">YEAR/SEM: {{ $yearSem }}</p>
        <p>COURSE CODE: {{ $code }}</p>
        <p class="uppercase"> NAME: {{ $courseUnit }}</p>
    </div>
    <div class="info-right">
        <p>DATE: {{ $date }}</p>
        <p class="uppercase">TIME: {{ $time }}</p>
    </div>
    <div class="clear"></div>
    <div style="text-align: left;">
        <p class="instructions">INSTRUCTIONS TO CANDIDATES:</p>
        {!! $generalInstructions !!}
    </div>
</div>

{{-- Ensure sections are sorted by name --}}
@php
ksort($sections);
@endphp

@foreach ($sections as $sectionName => $questions)
        <div class="section">
            <h2>
                Section {{ $sectionName }}
                {{-- Inline instructions for each section --}}
                @if ($sectionName == 'A' && isset($sectionAInstructions))
                    <span>{!! $sectionAInstructions !!}</span>
                @elseif ($sectionName == 'B' && isset($sectionBInstructions))
                    <span>{!! $sectionBInstructions !!}</span>
                @elseif ($sectionName == 'C' && isset($sectionCInstructions))
                    <span>{!! $sectionCInstructions !!}</span>
                @endif
            </h2>

            @foreach ($questions as $questionIndex => $question)
                <div class="question">
                    <p style="font-weight: bold; font-size: 16px">Question {{ $questionIndex + 1 }}:</p>
                    <div class="question-content p-4 rounded">{!! $question !!}</div>
                </div>
            @endforeach
        </div>
@endforeach

</body>
</html>
