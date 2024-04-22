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
            font-weight: bold;
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
            text-align: left;
            margin-top: 20px;
            text-transform: uppercase;
        }
        .instructions p:first-child {
            text-decoration: underline;
        }
        .clear {
            clear: both;
        }
       
        .question {
            margin-top: 10px;
        }
        .question-content {
            border-radius: 5px;
            margin-top: -10px;
            font-size: 16px;
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
        .uppercase {
            text-transform: uppercase;
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

    <div class="instructions">
        <p>INSTRUCTIONS TO CANDIDATES:</p>
        {!! nl2br(e($examInstructions)) !!}
    </div>
</div>


   
    @foreach ($sections as $sectionName => $questions)
        <div class="section">
            <h2>Section {{ $sectionName }}</h2>
            @foreach ($questions as $questionIndex => $question)
                <div class="question">
                    <p style="font-weight: bold;">Question {{ $questionIndex + 1 }}:</p>
                    <div class="question-content">{!! $question !!}</div>
                </div>
            @endforeach
        </div>
    @endforeach
</body>
</html>
