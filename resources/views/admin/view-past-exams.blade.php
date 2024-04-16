<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Past Exams List</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>

    @include('partials.admin-navbar')

    <div class="p-4 sm:ml-64 mt-20">
        <h1 class="text-3xl font-bold text-center mt-6 mb-4">Past Exams List</h1>

        @if (count($examsData) > 0)
            <table>
                <thead>
                    <tr>
                        <th>Course Unit</th>
                        <th>Year</th>
                        <th>Download File</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($examsData as $courseUnit => $details)
                    <tr>
                        <td rowspan="{{ count($details) }}">{{ $courseUnit }}</td>
                        @foreach ($details as $index => $data)
                            @if ($index > 0)
                            <tr>
                            @endif
                                <td>{{ $data['year'] }}</td>
                                <td><a href="data:application/pdf;base64,{{ $data['file'] }}" download="Exam_{{ $courseUnit }}_{{ $data['year'] }}.pdf">Download</a></td>
                            @if ($index > 0)
                            </tr>
                            @endif
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="mt-8 flex flex-col items-center justify-center">
                <img src="../assets/img/404.jpeg" alt="No Data Available" class="w-1/2 max-w-sm mx-auto">
                <p class="mt-4 text-lg font-semibold text-gray-600">No course details available.</p>
            </div>
        @endif

    </div>
</body>
</html>
