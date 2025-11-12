<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Preview - {{ $courseUnit }}</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ storage_path('fonts/DejaVuSans.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: 'Times New Roman', 'DejaVu Sans', serif;
            font-size: 13px;
            margin: 15px;
        }

        h1 {
            font-size: 20px;
            text-align: center;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 18px;
            margin-top: 15px;
            margin-bottom: 10px;
        }

        .section {
            padding: 8px;
            margin-bottom: 15px;
        }

        .section>p {
            font-size: 13px;
            margin-bottom: 10px;
        }

        .question {
            margin-top: 20px;
            margin-bottom: 10px;
        }

        .question p {
            margin: 0;
            font-size: 15px;
            font-weight: bold;
        }

        .question-content {
            padding-left: 20px;
            margin-top: 5px;
            margin-bottom: 10px;
            font-size: 12px;
            line-height: 1.5;
            font-weight: normal;
        }

        .question-content p,
        .question-content div,
        .question-content span,
        .question-content li {
            font-weight: normal !important;
        }

        .question-content strong,
        .question-content b {
            font-weight: bold !important;
        }

        .question-content img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 10px 0;
        }

        /* ✅ Table styling for proper border rendering in PDF */
        table {
            border-collapse: collapse;
            width: 100%;
            margin: 10px 0;
            border: 1px solid #000000 !important;
        }

        table th,
        table td {
            border: 1px solid #000000 !important;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }

        table thead th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        /* Ensure nested tables also have borders */
        .question-content table,
        .question-content table th,
        .question-content table td {
            border: 1px solid #000000 !important;
        }
    </style>
</head>

<body>

    <h1>{{ $courseUnit }}</h1>

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