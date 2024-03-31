<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Generated Exam</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

</head>
<body>
    
    @include('partials.admin-navbar')

<div class="p-4 sm:ml-64 mt-20 ">
    <h1 class="text-2xl text-center font-bold mb-8">Exam for {{ $courseUnit }}</h1>

    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Section A Questions
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($sectionAQuestions as $index => $question)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">Question {{ $index + 1 }}: {{ $question }}</div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">No questions found for Section A.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="min-w-full divide-y divide-gray-200 mt-8">
        <thead class="bg-gray-50">
            <tr>
                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Section B Questions
                </th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @forelse($sectionBQuestions as $index => $question)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">Question {{ $index + 1 }}: {{ $question }}</div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">No questions found for Section B.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="flex justify-center mt-8">
        <form action="{{ route('download.exam') }}" method="POST">
            @csrf
            <input type="hidden" name="courseUnit" value="{{ $courseUnit }}">
            <input type="hidden" name="sectionAQuestions" value="{{ json_encode($sectionAQuestions) }}">
            <input type="hidden" name="sectionBQuestions" value="{{ json_encode($sectionBQuestions) }}">
            <div class="flex justify-center mt-8">
                <button type="submit" class="inline-block px-6 py-2 border border-transparent text-base font-medium rounded-md text-white bg-black hover:bg-red-700">
                    Download Exam
                </button>
            </div>
        </form>

    </div>

</div>



</body>
</html>