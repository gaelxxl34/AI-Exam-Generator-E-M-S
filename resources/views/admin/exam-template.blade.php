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

        /* ============================================
           🔤 FORCE TIMES NEW ROMAN ON ALL ELEMENTS
           Override any inline font styles from HTML editor
           ============================================ */
        *,
        *::before,
        *::after {
            font-family: 'Times New Roman', 'DejaVu Sans', Times, serif !important;
            box-sizing: border-box;
        }

        /* Override any inline styles that might use other fonts */
        [style*="font-family"] {
            font-family: 'Times New Roman', 'DejaVu Sans', Times, serif !important;
        }

        /* Target common font-family patterns from rich text editors */
        [style*="arial"],
        [style*="Arial"],
        [style*="helvetica"],
        [style*="Helvetica"],
        [style*="sans-serif"],
        [style*="verdana"],
        [style*="Verdana"],
        [style*="georgia"],
        [style*="Georgia"],
        [style*="courier"],
        [style*="Courier"],
        [style*="Comic"],
        [style*="Impact"],
        [style*="Trebuchet"],
        [style*="Tahoma"],
        [style*="Calibri"],
        [style*="Segoe"] {
            font-family: 'Times New Roman', 'DejaVu Sans', Times, serif !important;
        }

        span,
        div,
        p,
        td,
        th,
        li,
        a,
        strong,
        em,
        b,
        i,
        u,
        h1,
        h2,
        h3,
        h4,
        h5,
        h6,
        font {
            font-family: 'Times New Roman', 'DejaVu Sans', Times, serif !important;
        }

        body {
            font-size: 12px;
            margin: 15mm;
            padding: 0;
            line-height: 1.15;
            color: #000;
            background: white;
        }

        /* ============================================
           📄 PAGE & TYPOGRAPHY
           ============================================ */
        h1 {
            font-size: 18px;
            margin: 0 0 10px 0;
        }

        h2 {
            font-size: 16px;
            margin-top: 15px;
            margin-bottom: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        h3,
        h4,
        h5,
        h6 {
            font-size: 14px;
            margin: 10px 0 5px 0;
        }

        p {
            margin: 0;
            line-height: 1.15;
        }

        /* ============================================
           📋 TABLE STYLING - Professional Print
           ============================================ */
        table {
            border-collapse: collapse !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 10px 0 !important;
            table-layout: fixed !important;
            page-break-inside: avoid;
        }

        table th,
        table td {
            border: 1px solid #000000 !important;
            padding: 6px 8px !important;
            text-align: left !important;
            vertical-align: top !important;
            word-wrap: break-word !important;
            overflow-wrap: break-word !important;
            font-size: 11px !important;
        }

        table thead th {
            background-color: #f0f0f0 !important;
            font-weight: bold !important;
            text-align: center !important;
        }

        /* Nested tables */
        table table {
            margin: 5px 0 !important;
        }

        /* ============================================
           📝 LIST STYLING - Clean & Professional
           ============================================ */
        ul,
        ol {
            margin: 0 !important;
            padding-left: 25px !important;
        }

        ul {
            list-style-type: disc !important;
        }

        ol {
            list-style-type: decimal !important;
        }

        li {
            margin-bottom: 0 !important;
            line-height: 1.15 !important;
            padding-left: 5px !important;
        }

        /* Nested lists */
        ul ul,
        ol ul {
            list-style-type: circle !important;
            margin: 4px 0 4px 0 !important;
        }

        ul ul ul,
        ol ul ul {
            list-style-type: square !important;
        }

        ol ol,
        ul ol {
            list-style-type: lower-alpha !important;
            margin: 4px 0 4px 0 !important;
        }

        ol ol ol,
        ul ol ol {
            list-style-type: lower-roman !important;
        }

        /* Alphabetical lists (a, b, c) */
        ol[type="a"],
        ol.lower-alpha {
            list-style-type: lower-alpha !important;
        }

        ol[type="A"],
        ol.upper-alpha {
            list-style-type: upper-alpha !important;
        }

        ol[type="i"],
        ol.lower-roman {
            list-style-type: lower-roman !important;
        }

        ol[type="I"],
        ol.upper-roman {
            list-style-type: upper-roman !important;
        }

        /* ============================================
           🖼️ IMAGE HANDLING
           ============================================ */
        img {
            max-width: 100% !important;
            height: auto !important;
            display: block;
            margin: 8px auto;
            page-break-inside: avoid;
        }

        /* ============================================
           📑 EXAM COVER PAGE
           ============================================ */
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

        .clear {
            clear: both;
        }

        /* ============================================
           📖 SECTION STYLING
           ============================================ */
        .section {
            padding: 0;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .section>p {
            font-size: 12px;
            margin-bottom: 10px;
            font-style: italic;
        }

        /* ============================================
           ❓ QUESTION STYLING
           ============================================ */
        .question {
            margin-top: 15px;
            margin-bottom: 10px;
            page-break-inside: avoid;
        }

        .question>p {
            margin: 0 0 5px 0;
            font-size: 12px;
            font-weight: bold;
        }

        .question-content {
            padding-left: 20px;
            margin-top: 5px;
            margin-bottom: 10px;
            font-size: 12px;
            line-height: 1.15;
            font-weight: normal;
        }

        .question-content p,
        .question-content div,
        .question-content span {
            font-weight: normal !important;
            margin-bottom: 6px;
        }

        .question-content li {
            font-weight: normal !important;
        }

        .question-content strong,
        .question-content b {
            font-weight: bold !important;
        }

        .question-content em,
        .question-content i {
            font-style: italic !important;
        }

        .question-content u {
            text-decoration: underline !important;
        }

        /* Subscript and Superscript */
        .question-content sub {
            vertical-align: sub;
            font-size: 0.8em;
        }

        .question-content sup {
            vertical-align: super;
            font-size: 0.8em;
        }

        .question-content img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 10px 0;
        }

        /* Question content table specific styling */
        .question-content table {
            width: 100% !important;
            max-width: 100% !important;
            table-layout: fixed !important;
        }

        .question-content table th,
        .question-content table td {
            border: 1px solid #000000 !important;
            word-break: break-word !important;
        }

        /* ============================================
           🔧 UTILITY CLASSES
           ============================================ */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-bordered {
            border: 1px solid black;
        }

        .table td,
        .table th {
            padding: 6px 8px;
            vertical-align: top;
            border: 1px solid #000;
        }

        /* Page break controls */
        .page-break {
            page-break-after: always;
        }

        .no-break {
            page-break-inside: avoid;
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