<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.3.0/flowbite.min.js"></script>

</head>
<body>
    
    @include('partials.admin-navbar')

    <div class="p-4 sm:ml-64 mt-20">
        <div class="flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4 mt-2 mb-4">
            <div class="flex-1 bg-gray-200 rounded-lg p-4 shadow">
                <div class="text-gray-900 text-lg">Courses</div>
                <div class="text-gray-600 text-2xl">43</div>
            </div>
            <div class="flex-1 bg-gray-200 rounded-lg p-4 shadow">
                <div class="text-gray-900 text-lg">Past Exams</div>
                <div class="text-gray-600 text-2xl">456</div>
            </div>
            <div class="flex-1 bg-gray-200 rounded-lg p-4 shadow">
                <div class="text-gray-900 text-lg">Exams Generated</div>
                <div class="text-gray-600 text-2xl">789</div>
            </div>
            <div class="flex-1 bg-gray-200 rounded-lg p-4 shadow">
                <div class="text-gray-900 text-lg">Lecturers</div>
                <div class="text-gray-600 text-2xl">12</div>
            </div>
        </div>

        
<div class="max-w-sm w-full bg-white rounded-lg shadow dark:bg-gray-200 p-4 md:p-6">
  <div class="flex justify-between mb-5">
    <div>
      <h5 class="leading-none text-3xl font-bold text-gray-900 dark:text-black pb-2">123</h5>
      <p class="text-base font-normal text-gray-500 dark:text-gray-400">Views this week</p>
    </div>
    <div
      class="flex items-center px-2.5 py-0.5 text-base font-semibold text-green-500 dark:text-green-500 text-center">
      23%
      <svg class="w-3 h-3 ms-1" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 14">
        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13V1m0 0L1 5m4-4 4 4"/>
      </svg>
    </div>
  </div>
  <div id="grid-chart"></div>
  <div class="grid grid-cols-1 items-center border-gray-200 border-t dark:border-gray-700 justify-between mt-5">
    <div class="flex justify-between items-center pt-5">
      <!-- Button -->
      <button
        id="dropdownDefaultButton"
        data-dropdown-toggle="lastDaysdropdown"
        data-dropdown-placement="bottom"
        class="text-sm font-medium text-gray-500 dark:text-gray-400 hover:text-gray-900 text-center inline-flex items-center dark:hover:text-black"
        type="button">
        Last 7 days
        <svg class="w-2.5 m-2.5 ms-1.5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 10 6">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 4 4 4-4"/>
        </svg>
      </button>
      <!-- Dropdown menu -->
      <div id="lastDaysdropdown" class="z-10 hidden bg-black divide-y divide-gray-100 rounded-lg shadow w-44 dark:bg-gray-700">
          <ul class="py-2 text-sm text-gray-700 dark:text-gray-200" aria-labelledby="dropdownDefaultButton">
            <li>
              <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Yesterday</a>
            </li>
            <li>
              <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Today</a>
            </li>
            <li>
              <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Last 7 days</a>
            </li>
            <li>
              <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Last 30 days</a>
            </li>
            <li>
              <a href="#" class="block px-4 py-2 hover:bg-gray-100 dark:hover:bg-gray-600 dark:hover:text-white">Last 90 days</a>
            </li>
          </ul>
      </div>
      <a
        href="#"
        class="uppercase text-sm font-semibold inline-flex items-center rounded-lg text-blue-600 hover:text-blue-700 dark:hover:text-blue-500  hover:bg-gray-100 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700 px-3 py-2">
        Views Report
        <svg class="w-2.5 h-2.5 ms-1.5 rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
          <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
        </svg>
      </a>
    </div>
  </div>
</div>

    </div>

<script>

    const options = {
    // set grid lines to improve the readability of the chart, learn more here: https://apexcharts.com/docs/grid/
    grid: {
    show: true,
    strokeDashArray: 4,
    padding: {
        left: 2,
        right: 2,
        top: -26
    },
    },
    series: [
    {
        name: "Developer Edition",
        data: [1500, 1418, 1456, 1526, 1356, 1256],
        color: "#1A56DB",
    },
    {
        name: "Designer Edition",
        data: [643, 413, 765, 412, 1423, 1731],
        color: "#7E3BF2",
    },
    ],
    chart: {
    height: "100%",
    maxWidth: "100%",
    type: "area",
    fontFamily: "Inter, sans-serif",
    dropShadow: {
        enabled: false,
    },
    toolbar: {
        show: false,
    },
    },
    tooltip: {
    enabled: true,
    x: {
        show: false,
    },
    },
    legend: {
    show: true
    },
    fill: {
    type: "gradient",
    gradient: {
        opacityFrom: 0.55,
        opacityTo: 0,
        shade: "#1C64F2",
        gradientToColors: ["#1C64F2"],
    },
    },
    dataLabels: {
    enabled: false,
    },
    stroke: {
    width: 6,
    },
    xaxis: {
    categories: ['01 February', '02 February', '03 February', '04 February', '05 February', '06 February', '07 February'],
    labels: {
        show: false,
    },
    axisBorder: {
        show: false,
    },
    axisTicks: {
        show: false,
    },
    },
    yaxis: {
    show: false,
    labels: {
        formatter: function (value) {
        return '$' + value;
        }
    }
    },
    }

    if (document.getElementById("grid-chart") && typeof ApexCharts !== 'undefined') {
    const chart = new ApexCharts(document.getElementById("grid-chart"), options);
    chart.render();
    }

</script>


</body>
</html>