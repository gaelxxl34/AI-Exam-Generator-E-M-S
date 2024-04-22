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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

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
    <table border="1" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>Program</th>
                <th>Course Unit</th>
                <th>Year</th>
                <th>Download File</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($examsData as $program => $courses)
                @foreach ($courses as $courseUnit => $details)
                    @php $totalDetails = count($details); @endphp
                    @foreach ($details as $index => $data)
                        <tr>
                            <!-- Program and Course Unit -->
                            @if ($index == 0)
                                <td rowspan="{{ $totalDetails }}">{{ $program }}</td>
                                <td rowspan="{{ $totalDetails }}">{{ $courseUnit }}</td>
                            @endif
                            <!-- Year and download link -->
                            <td>{{ $data['year'] }}</td>
                            <td><a href="data:application/pdf;base64,{{ $data['file'] }}" download="Exam_{{ $courseUnit }}_{{ $data['year'] }}.pdf">Download</a></td>
                            <!-- Delete action -->
                            <td>
                                <a href="{{ route('delete-past-exam', ['id' => $data['id']]) }}" onclick="return confirm('Are you sure you want to delete this exam?')" title="Delete">
                                   <i class="fa fa-trash" aria-hidden="true" style="color: rgb(173, 13, 13);"></i>

                                </a>
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        </tbody>
    </table>
@else
            <div class="mt-8 flex flex-col items-center justify-center">
                <img src="../assets/img/404.jpeg" alt="No Data Available" class="w-1/2 max-w-sm mx-auto">
                <p class="mt-4 text-lg font-semibold text-gray-600">No past exams available.</p>
            </div>
        @endif

    </div>
</body>
</html>
