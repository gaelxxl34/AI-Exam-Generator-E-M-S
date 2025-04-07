<!DOCTYPE html>
<html>

<head>
    <title>Faculty Dashboard Report</title>
    <style>
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 13px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 6px;
            text-align: left;
        }

        th {
            background-color: #f0f0f0;
        }

        .section {
            margin-top: 30px;
        }

        .logo {
            width: 150px;
            margin: 0 auto;
            display: block;
        }

        .faculty-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
        }

        p {
            text-align: center;
            font-size: 12px;
            color: #555;
        }
    </style>
</head>

<body>

    <img src="https://iuea.ac.ug/sitepad-data/uploads//2020/11/Website-Logo.png" class="logo" alt="University Logo">
    @php
$facultyCode = session('user_faculty');
if (is_array($facultyCode)) {
    $facultyCode = $facultyCode[0] ?? '';
}

$facultyNames = [
    'FST' => 'Faculty of Science and Technology',
    'FBM' => 'Faculty of Business Management',
    'FOE' => 'Faculty of Engineering',
    'FOL' => 'Faculty of Law',
    'HEC' => 'Higher Education Certificate',
];

$facultyOf = $facultyNames[$facultyCode] ?? $facultyCode;
    @endphp
    
    <div class="faculty-title">{{ $facultyOf }}</div>


    <h2>Faculty Dashboard Report</h2>
    <p>Generated on: {{ now()->setTimezone('Africa/Kampala')->format('d M Y, H:i A') }}</p>

    <div class="section">
        <h3>Summary</h3>
        <ul>
            <li>Pending Exams: {{ $pendingExams }}</li>
            <li>Approved Exams: {{ $approvedExams }}</li>
            <li>Declined Exams: {{ $declinedExams }}</li>
            <li>Faculty Courses: {{ count($facultyCourses) }}</li>
            <li>Lecturers Submitted: {{ count($lecturerSubmissions) }} / {{ count($allLecturers) }}</li>
            <li>Missing Courses: {{ count($missingCourses) }}</li>
        </ul>
    </div>

    <div class="section">
        <h3>Average Questions per Section</h3>
        <ul>
            @foreach ($averageQuestions as $section => $avg)
                <li>Section {{ $section }}: {{ $avg }} questions (avg)</li>
            @endforeach
        </ul>
    </div>

    <div class="section">
        <h3>Incomplete Exams</h3>
        <table>
            <thead>
                <tr>
                    <th>Course Unit</th>
                    <th>Lecturer</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($incompleteExams as $exam)
                    <tr>
                        <td>{{ $exam['courseUnit'] ?? 'N/A' }}</td>
                        <td>{{ $exam['lecturerName'] ?? 'Unknown' }}</td>
                        <td>{{ $exam['lecturerEmail'] ?? 'N/A' }}</td>
                        <td>{{ $exam['status'] ?? 'Pending Review' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">No incomplete exams found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</body>

</html>