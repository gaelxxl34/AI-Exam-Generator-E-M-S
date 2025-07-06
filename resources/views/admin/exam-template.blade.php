<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Exam - {{ $courseUnit }}</title>
    <style>
        * {
            font-family: "Times New Roman", Times, serif !important;
        }

        body {
            font-size: 14px;
            margin: 15px;
        }

        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        p,
        span,
        div,
        td,
        th,
        strong,
        b,
        em,
        i,
        textarea,
        input,
        label {
            font-family: "Times New Roman", Times, serif !important;
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

        /* Ensure all images in content fit the page and are not cut off */
        img {
            max-width: 100% !important;
            height: auto !important;
            display: block;
            margin: 0 auto 8px auto;
        }

        /* Ensure tables fit the page and do not overflow */
        .question-content table,
        .question-content th,
        .question-content td {
            width: 100% !important;
            max-width: 100% !important;
            word-break: break-word;
            box-sizing: border-box;
        }

        .question-content {
            overflow-x: auto;
        }

        .faculty-name,
        {
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

        .info-left,
        .info-right {
            /* margin: 20px 50px; */
            text-align: left;
            font-weight: bold;
        }

        .info-right {
            text-align: right;
        }

        .instructions {
            font-weight: bold;

            margin-top: 18px;
            text-transform: uppercase;
            text-decoration: underline;
        }

        .instructions p:first-child {}

        .clear {
            clear: both;
        }

        .section {
            padding: 8px;
            /* Increased padding for questions */
        }

        .question {
            margin-top: 6px;
            /* Increased margin above each question for readability */
            margin-bottom: 10px;
            /* Added margin below each question */
        }

        .question p {
            font-weight: normal;
            /* Only the question number should be bold */
            margin: 0;
            /* Keep margins tight around text */
        }

        .question-content {
            padding: 12px;
            /* Small padding around content for visual separation */
        }

        h2 {
            font-weight: bold;
            /* Ensure section headers are bold */
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-bordered {
            border: 1px solid black;
        }

        td,
        th {
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
        <div class="faculty-name">{{ $facultyOf }}</div>
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
                    <span>{!! strip_tags($sectionAInstructions, '<b><strong><i><em><u><span><br><img>') !!}</span>
                @elseif ($sectionName == 'B' && isset($sectionBInstructions))
                    <span>{!! strip_tags($sectionBInstructions, '<b><strong><i><em><u><span><br><img>') !!}</span>
                @elseif ($sectionName == 'C' && isset($sectionCInstructions))
                    <span>{!! strip_tags($sectionCInstructions, '<b><strong><i><em><u><span><br><img>') !!}</span>
                @endif
            </h2>

            @foreach ($questions as $questionIndex => $question)
                <div class="question">
                    <p style="font-weight: bold; font-size: 16px">Question {{ $questionIndex + 1 }}:</p>
                    <div class="question-content p-4 rounded">
                        {!! strip_tags($question, '<b><strong><i><em><u><span><br><ul><ol><li><table><tr><td><th><sup><sub><img>') !!}
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach


    @if (isset($pdf))
        <script type="text/php">
                if (isset($pdf)) {
                    $font = $fontMetrics->getFont("Times New Roman", "normal");
                    $size = 10;
                    $pdf->page_script('
                        if ($PAGE_COUNT > 1) {
                            $font = $fontMetrics->getFont("Times New Roman", "normal");
                            $pdf->text(520, 820, "Page $PAGE_NUM of $PAGE_COUNT", $font, 10);
                        }
                    ');
                }
            </script>
    @endif


</body>

</html>