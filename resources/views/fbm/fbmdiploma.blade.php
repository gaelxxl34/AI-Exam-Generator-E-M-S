<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fbm Diploma</title>

        <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">   

    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="/assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/navbar.css" rel="stylesheet">

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
       a.visible-link {
            color: deepskyblue; /* Bright color for visibility */
            text-decoration: underline; /* Ensures it's recognized as a link */
        }
    </style>
</head>
<body>
    @include('partials.navbar')


            <!-- Master Fst Section Start -->
    <div class="container-fluid large-margin-bottom ">
        <div class="container">
               <center>
               <div class="align-items-center justify-content-center bg-light py-2 px-1 mt-3">
                    <h2 class="m-0">Fbm Diploma</h2>
                </div>
               </center>

            <table class="min-w-full divide-y divide-gray-200">
                <thead>
                    <tr>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-dark uppercase tracking-wider">Program</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-dark uppercase tracking-wider">Course Unit</th>
                        <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-dark uppercase tracking-wider">Year</th>
                        <th class="px-6 py-3 bg-gray-50">Download</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse ($examsData as $program => $courses)
                        @foreach ($courses as $courseUnit => $details)
                            @foreach ($details as $index => $data)
                                <tr>
                                    <!-- Show program name only in the first row of each new program -->
                                    @if ($index == 0)
                                        <td rowspan="{{ count($details) }}" class="px-6 py-4 whitespace-nowrap text-sm text-dark">{{ $program }}</td>
                                    @endif
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-dark">{{ $courseUnit }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-dark">{{ $data['year'] }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-dark">
                                        <a class="a-moi" href="data:application/pdf;base64,{{ $data['file'] }}" download="Exam_{{ $courseUnit }}_{{ $data['year'] }}.pdf" class="text-indigo-600 hover:text-indigo-900">Download</a>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-4 text-center text-sm text-dark">No data found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- Master Fst Section Slider End -->



    @include('partials.footer')
        <!-- Back to Top -->
    <a href="#" class="btn btn-dark back-to-top"><i class="fa fa-angle-up"></i></a>




    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="/assets/lib/easing/easing.min.js"></script>
    <script src="/assets/lib/owlcarousel/owl.carousel.min.js"></script>


    <!-- Template Javascript -->
    <script src="/assets/js/main.js"></script>
</body>
</html>