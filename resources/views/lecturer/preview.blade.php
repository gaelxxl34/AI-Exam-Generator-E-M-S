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
            font-family: 'Times New Roman', 'DejaVu Sans', serif;
            font-size: 12px;
            margin: 0;
            padding: 15mm;
            background: white;
        }

        /* No container styling needed - pure white paper */

        h1 {
            font-size: 18px;
            text-align: center;
            margin-bottom: 20px;
        }

        h2 {
            font-size: 16px;
            margin-top: 20px;
            margin-bottom: 10px;
            font-weight: bold;
            text-transform: uppercase;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }

        .section {
            padding: 0;
            margin-bottom: 20px;
        }

        .section>p {
            font-size: 12px;
            margin-bottom: 10px;
            font-style: italic;
        }

        .question {
            margin-top: 15px;
            margin-bottom: 10px;
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
            line-height: 1.6;
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
            margin-bottom: 4px;
        }

        .question-content strong,
        .question-content b {
            font-weight: bold !important;
        }

        .question-content em,
        .question-content i {
            font-style: italic !important;
        }

        .question-content img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 10px 0;
        }

        /* ✅ Table styling matching PDF output */
        table {
            border-collapse: collapse !important;
            width: 100% !important;
            max-width: 100% !important;
            margin: 10px 0 !important;
            table-layout: fixed !important;
        }

        table th,
        table td {
            border: 1px solid #000000 !important;
            padding: 6px 8px !important;
            text-align: left;
            vertical-align: top;
            word-wrap: break-word;
            font-size: 11px;
        }

        table thead th {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .question-content table,
        .question-content table th,
        .question-content table td {
            border: 1px solid #000000 !important;
        }

        /* ✅ List styling matching PDF output */
        ul,
        ol {
            margin: 8px 0 8px 0 !important;
            padding-left: 25px !important;
        }

        ul {
            list-style-type: disc !important;
        }

        ol {
            list-style-type: decimal !important;
        }

        li {
            margin-bottom: 4px !important;
            line-height: 1.5 !important;
        }

        ul ul,
        ol ul {
            list-style-type: circle !important;
            margin: 4px 0 4px 0 !important;
        }

        ol ol,
        ul ol {
            list-style-type: lower-alpha !important;
            margin: 4px 0 4px 0 !important;
        }

        /* Preview notice */
        .preview-notice {
            background: #e3f2fd;
            border: 1px dashed #2196f3;
            border-radius: 4px;
            padding: 10px 15px;
            margin-bottom: 20px;
            font-size: 12px;
            color: #1565c0;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="preview-container">
        <div class="preview-notice">
            📄 <strong>PDF Preview</strong> - This preview shows how your exam will appear in the final PDF document.
        </div>

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
    </div>
</body>

</html>