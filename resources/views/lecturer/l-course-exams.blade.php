<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Course Details</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

    <style>
        /* Tailwind CSS to mimic Bootstrap table styles */
        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table-bordered {
            border: 1px solid black;
        }

        .table-bordered td,
        .table-bordered th {
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
    
    @include('partials.lecturer-navbar')

<div class="p-4 sm:ml-64 mt-20">
    <div class="container mx-auto px-4">
        @forelse ($exams as $index => $exam)
            <div class="mt-8 bg-white rounded-lg shadow-md">
                <h1 class="text-xl font-bold p-4 border-b text-center">{{ "Exam " . ($index + 1) . " - " . $exam['courseUnit'] }}</h1>
                @foreach ($exam['sections'] as $sectionName => $questions)
                    <div class="mt-4 p-4 border-t">
                        <h2 class="text-lg font-semibold">{{ "Section " . $sectionName }}</h2>
                        @foreach ($questions as $questionIndex => $question)
                            <div class="mt-2">
                                <p>Question {{ $questionIndex + 1 }}:</p>
                                <div class="p-4 bg-gray-100 rounded">{!! $question !!}</div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @empty
            <div class="mt-8 flex flex-col items-center justify-center h-screen">
                <img src="https://www.google.com/url?sa=i&url=https%3A%2F%2Fwww.freepik.com%2Ffree-photos-vectors%2F404-found-png&psig=AOvVaw2PMniOzbz6x9UEscG6HgU0&ust=1712508354343000&source=images&cd=vfe&opi=89978449&ved=0CBIQjRxqFwoTCKiN4oCFroUDFQAAAAAdAAAAABAE" alt="No Data Available" class="w-1/2 max-w-sm mx-auto">
                <p class="mt-4 text-lg font-semibold text-gray-600">No course details available.</p>
            </div>

        @endforelse
    </div>
</div>




    
</body>
</html>