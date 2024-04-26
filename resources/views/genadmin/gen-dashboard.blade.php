<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


</head>
<body>
    
    @include('partials.gen-navbar')

<div class="p-4 sm:ml-64 mt-20">
    <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 mt-2 mb-4">
        <div class="flex-1 bg-gray-200 rounded-lg p-4 shadow">
            <div class="text-gray-900 text-lg">Courses</div>
            <div class="text-gray-600 text-2xl">
                @if($coursesCount > 0)
                    {{ $coursesCount }}
                @else
                    <p>No courses found</p>
                @endif
            </div>
        </div>
        <div class="flex-1 bg-gray-200 rounded-lg p-4 shadow">
            <div class="text-gray-900 text-lg">Past Exams</div>
            <div class="text-gray-600 text-2xl">
                @if($pastExamsCount > 0)
                    {{ $pastExamsCount }}
                @else
                    <p> No past exams found</p>
                @endif
            </div>
        </div>
        <div class="flex-1 bg-gray-200 rounded-lg p-4 shadow">
            <div class="text-gray-900 text-lg">Lecturers</div>
            <div class="text-gray-600 text-2xl">
                @if($lecturerCount > 0)
                    {{ $lecturerCount }}
                @else
                    <p>No lecturers found</p>
                @endif
            </div>
        </div>

    </div>


  <div class="w-full max-w-full h-[500px] mx-auto">
      <canvas id="myChart"></canvas>
  </div>




</div>


<script>
  document.addEventListener('DOMContentLoaded', function () {
      var ctx = document.getElementById('myChart').getContext('2d');
      var myChart = new Chart(ctx, {
          type: 'bar', // Changed to 'bar' for better visualization of static counts
          data: {
              labels: ['1', '2', '3', '4', '5', '5', '6'],
              datasets: [{
                  label: 'Lecturers',
                  data: [{{ $lecturerCount }}, {{ $lecturerCount + 5 }}, {{ $lecturerCount + 10 }}, {{ $lecturerCount + 15 }}, {{ $lecturerCount + 20 }}, {{ $lecturerCount + 25 }}, {{ $lecturerCount + 30 }}],
                  backgroundColor: 'rgba(255, 99, 132, 0.5)',
                  borderColor: 'rgba(255, 99, 132, 1)',
                  borderWidth: 1
              }, {
                  label: 'Courses',
                  data: [{{ $coursesCount }}, {{ $coursesCount + 2 }}, {{ $coursesCount + 4 }}, {{ $coursesCount + 6 }}, {{ $coursesCount + 8 }}, {{ $coursesCount + 10 }}, {{ $coursesCount + 12 }}],
                  backgroundColor: 'rgba(54, 162, 235, 0.5)',
                  borderColor: 'rgba(54, 162, 235, 1)',
                  borderWidth: 1
              }, {
                  label: 'Past Exams',
                  data: [{{ $pastExamsCount }}, {{ $pastExamsCount + 3 }}, {{ $pastExamsCount + 6 }}, {{ $pastExamsCount + 9 }}, {{ $pastExamsCount + 12 }}, {{ $pastExamsCount + 15 }}, {{ $pastExamsCount + 18 }}],
                  backgroundColor: 'rgba(75, 192, 192, 0.5)',
                  borderColor: 'rgba(75, 192, 192, 1)',
                  borderWidth: 1
              }]
          },
          options: {
            responsive: true,
        maintainAspectRatio: false,
              scales: {
                  y: {
                      beginAtZero: true
                  }
              },
              animation: {
                  duration: 1000
              }
          }
      });
  });
</script>





</body>
</html>