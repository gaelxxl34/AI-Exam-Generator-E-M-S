<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Dean Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/js/all.min.js"></script>


</head>

<body>

    @include('partials.dean-navbar')

<div class="p-6 sm:ml-64 mt-20">

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        {{-- Pending Exams --}}
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-6 rounded shadow-md">
            <div class="text-4xl font-bold">{{ $pendingExams }}</div>
            <div class="mt-2 text-sm font-semibold">Pending Exams</div>
        </div>

        {{-- Approved Exams --}}
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-6 rounded shadow-md">
            <div class="text-4xl font-bold">{{ $approvedExams }}</div>
            <div class="mt-2 text-sm font-semibold">Approved Exams</div>
        </div>

        {{-- Declined Exams --}}
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-6 rounded shadow-md">
            <div class="text-4xl font-bold">{{ $declinedExams }}</div>
            <div class="mt-2 text-sm font-semibold">Declined Exams</div>
        </div>

        {{-- Faculty Courses --}}
        <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-6 rounded shadow-md">
            <div class="text-4xl font-bold">{{ count($facultyCourses) }}</div>

            @php
$faculties = session('user_faculty');
if (!is_array($faculties)) {
    $faculties = [$faculties];
}
            @endphp
            <div class="mt-2 text-sm font-semibold">
                Courses for Faculty ({{ implode(', ', $faculties) }})
            </div>

        </div>

    </div>

    <!-- Hidden iframe for download -->
    <iframe id="downloadFrame" name="downloadFrame" style="display:none;"></iframe>

    <!-- Download button form targets the iframe -->
    <form id="reportForm" action="{{ route('dashboard.export-report') }}" method="GET" target="downloadFrame">
        <button type="submit" id="downloadBtn"
            class="mt-10 block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg shadow transition duration-200 flex items-center justify-center gap-2">
            <span id="btnText">📄 Download Report</span>
            <span id="btnSpinner"
                class="hidden animate-spin border-2 border-white border-t-transparent rounded-full w-5 h-5"></span>
        </button>
    </form>



    {{-- Additional Insights Section --}}
    <div class="mt-10 space-y-6">
    
        {{-- Lecturer Participation --}}
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-bold text-gray-800 mb-4">👨‍🏫 Lecturer Participation</h2>
            <p class="text-gray-700">
                <span class="font-semibold">{{ count($lecturerSubmissions) }}</span> out of
                <span class="font-semibold">{{ count($allLecturers) }}</span> faculty lecturers have submitted at least one exam.

            </p>
        </div>
    
        {{-- Missing Courses --}}
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-bold text-gray-800 mb-4">📘 Missing Courses</h2>
            @if(count($missingCourses))
                <ul class="list-disc list-inside text-gray-700">
                    @foreach ($missingCourses as $course)
                        <li>{{ $course }}</li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-600">All courses have at least one exam submitted.</p>
            @endif
        </div>
    
        {{-- Submission Timeline --}}
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-bold text-gray-800 mb-4">📆 Submissions by Month</h2>
            @if(count($submissionsByMonth))
                <ul class="list-disc list-inside text-gray-700">
                    @foreach ($submissionsByMonth as $month => $count)
                        <li><strong>{{ $month }}</strong>: {{ $count }} exams</li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-600">No submission data available.</p>
            @endif
        </div>
    
        {{-- Average Questions Per Section --}}
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-bold text-gray-800 mb-4">📊 Average Questions per Section</h2>
            <ul class="list-disc list-inside text-gray-700">
                @foreach ($averageQuestions as $section => $avg)
                    <li>Section {{ $section }}: {{ $avg }} questions (avg)</li>
                @endforeach
            </ul>
        </div>
    
        {{-- Top Incomplete Exams --}}
        {{-- <div class="bg-white p-6 rounded shadow">
            <h2 class="text-xl font-bold text-gray-800 mb-4">⚠️ Incomplete Exams</h2>
            @if(count($incompleteExams))
                <table class="w-full table-auto text-sm text-left border">
                    <thead class="bg-gray-100 font-semibold">
                        <tr>
                            <th class="px-4 py-2">Course Unit</th>
                            <th class="px-4 py-2">Lecturer</th>
                            <th class="px-4 py-2">Email</th>
                            <th class="px-4 py-2">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($incompleteExams as $exam)
                            <tr class="border-t">
                                <td class="px-4 py-2">{{ $exam['courseUnit'] ?? 'N/A' }}</td>
                                <td class="px-4 py-2">{{ $exam['lecturerName'] ?? 'Unknown' }}</td>
                                <td class="px-4 py-2">{{ $exam['lecturerEmail'] ?? 'N/A' }}</td>
                                <td class="px-4 py-2">{{ $exam['status'] ?? 'Pending Review' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-gray-600">All exams meet the minimum required questions per section.</p>
            @endif
        </div> --}}
    
    </div>

</div>


<script>
    document.getElementById('reportForm').addEventListener('submit', function () {
        const btn = document.getElementById('downloadBtn');
        document.getElementById('btnText').textContent = 'Preparing...';
        document.getElementById('btnSpinner').classList.remove('hidden');
        btn.disabled = true;

        // Reset the button after 5 seconds (adjust as needed)
        setTimeout(() => {
            document.getElementById('btnText').textContent = '📄 Download Report';
            document.getElementById('btnSpinner').classList.add('hidden');
            btn.disabled = false;
        }, 5000);
    });
</script>


</body>

</html>