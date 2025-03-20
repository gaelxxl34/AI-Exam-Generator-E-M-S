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
            margin-bottom: 15px;
        }

        .question {
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .question p {
            margin: 0;
            font-size: 16px;
        }

        .question-content {
            padding-left: 20px;
            margin-top: 5px;
            margin-bottom: 10px;
            font-size: 15px;
            line-height: 1.6;
        }

        .question-content img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 10px 0;
        }
    </style>
</head>

<body>

    <h1 style="text-align: center; font-size: 24px;">{{ $courseUnit }}</h1>

    @php
        ksort($sections);
    @endphp

    @foreach ($sections as $sectionName => $questions)
        <div class="section">
            <h2>Section {{ $sectionName }}</h2>
            @if ($sectionName == 'A' && isset($sectionAInstructions))
                <p>{!! $sectionAInstructions !!}</p>
            @elseif ($sectionName == 'B' && isset($sectionBInstructions))
                <p>{!! $sectionBInstructions !!}</p>
            @endif

            @foreach ($questions as $questionIndex => $question)
                <div class="question">
                    <p>Question {{ $questionIndex + 1 }}:</p>
                    <div class="question-content">{!! $question !!}</div>
                </div>
            @endforeach
        </div>
    @endforeach

</body>

</html>