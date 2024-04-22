<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body>

     @include('partials.super-admin-navbar')
<div class="p-4 sm:ml-64 mt-20">
  <div class="">
      <canvas id="myChart"></canvas>
  </div>
</div>



<script>
document.addEventListener('DOMContentLoaded', function () {
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'Nov', 'Dev'],
            datasets: [{
                label: 'Demo Data',
                data: [12, 19, 3, 5, 2, 3, 9, 23,3],
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)',
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                ],
                borderWidth: 1
            }],
            datasets: [{
                label: 'Production Data',
                data: [17, 1, 3, 7, 2, 3, 9, 5,3],
                backgroundColor: [
                    'green',
                ],
                borderColor: [
                    'blue',
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            animation: {
                duration: 2000, // general animation time
            },
            hover: {
                animationDuration: 400, // duration of animations when hovering an item
            },
            responsiveAnimationDuration: 500, // animation duration after a resize
            elements: {
                line: {
                    tension: 0.4 // disables bezier curves
                }
            }
        }
    });
});
</script>



    </body>
</html>