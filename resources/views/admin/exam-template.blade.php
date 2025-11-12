<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Exam - {{ $courseUnit }}</title>
    <style>
        @font-face {
            font-family: 'DejaVu Sans';
            src: url('{{ storage_path('fonts/DejaVuSans.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        * {
            font-family: 'Times New Roman', 'DejaVu Sans', serif !important;
        }

        body {
            font-size: 13px;
            margin: 15px;
        }

        h1 {
            font-size: 20px;
        }

        h2 {
            font-size: 18px;
            margin-top: 15px;
            margin-bottom: 10px;
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
            font-family: 'Times New Roman', 'DejaVu Sans', serif !important;
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

        /* ✅ Enhanced table styling for proper border rendering in PDF */
        table {
            border-collapse: collapse !important;
            width: 100% !important;
            margin: 10px 0 !important;
            border: 1px solid #000000 !important;
        }

        table th,
        table td {
            border: 1px solid #000000 !important;
            padding: 8px !important;
            text-align: left !important;
            vertical-align: top !important;
        }

        table thead th {
            background-color: #f2f2f2 !important;
            font-weight: bold !important;
        }

        /* Ensure tables in question content have proper borders */
        .question-content table,
        .question-content table th,
        .question-content table td {
            border: 1px solid #000000 !important;
            width: auto !important;
            max-width: 100% !important;
            word-break: break-word !important;
            box-sizing: border-box !important;
        }

        .question-content {
            overflow-x: auto;
        }

        .faculty-name {
            margin-bottom: 10px;
            margin-top: 40px;
            text-transform: uppercase;
            font-weight: bold;
            font-size: 16px;
        }

        .uppercase {
            text-transform: uppercase;
        }

        .exam-date {
            margin-bottom: 30px;
            margin-top: 10px;
            font-weight: bold;
            font-size: 14px;
        }

        .info-left,
        .info-right {
            /* margin: 20px 50px; */
            text-align: left;
            font-weight: bold;
            font-size: 13px;
        }

        .info-right {
            text-align: right;
            font-size: 12px;
        }

        .instructions {
            font-weight: bold;
            font-size: 14px;
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

        /* Remove duplicate table styling */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-bordered {
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
            <h2>Section {{ $sectionName }}</h2>
            {{-- Display section instructions --}}
            @if ($sectionName == 'A' && isset($sectionAInstructions))
                <p>{!! $sectionAInstructions !!}</p>
            @elseif ($sectionName == 'B' && isset($sectionBInstructions))
                <p>{!! $sectionBInstructions !!}</p>
            @elseif ($sectionName == 'C' && isset($sectionCInstructions))
                <p>{!! $sectionCInstructions !!}</p>
            @endif

            @foreach ($questions as $questionIndex => $question)
                <div class="question">
                    <p>Question {{ $questionIndex + 1 }}:</p>
                    <div class="question-content">{!! $question !!}</div>
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