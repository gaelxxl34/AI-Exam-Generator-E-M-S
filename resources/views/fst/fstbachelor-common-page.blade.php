<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courses</title>

    <!-- Google Web Fonts -->
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&display=swap" rel="stylesheet">   

    <!-- Font Awesome -->
    <link href="https://cdnjse.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">

    <!-- Libraries Stylesheet -->
    <link href="../assets/lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">

    <!-- Customized Bootstrap Stylesheet -->
    <link href="../assets/css/style.css" rel="stylesheet">
    <link href="../assets/css/navbar.css" rel="stylesheet">
    <style>
    .a{
        color: rgb(44, 43, 43) !important;
    }
    .a:hover {
        color: #7a0000 !important;
    }
</style>
</head>
<body>


    @include('partials.navbar')

    <div class="container-fluid large-margin-bottom">
        <div class="container">
            <center>
               <div class="align-items-center justify-content-center bg-light py-2 px-4 mt-3">
                    <h2 class="m-0">Programme + Year + Semester Courses</h2>
               </div>
            </center>

            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th style="color: black;">Course Name</th>
                        <th style="color: black;">Year</th>
                        <th style="color: black;">Exam Download</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Course 1 -->
                    <tr>
                        <td rowspan="6" style="color: black;">Course 1</td>
                        <td>2019</td>
                        <td><a href="path-to-exam-2019" download class="a" style="color: black;">Download 2019</a></td>
                    </tr>
                    <tr>
                        <td>2020</td>
                        <td><a href="path-to-exam-2020" download class="a" style="color: black;">Download 2020</a></td>
                    </tr>
                    <tr>
                        <td>2021</td>
                        <td><a href="path-to-exam-2021" download class="a" style="color: black;">Download 2021</a></td>
                    </tr>
                    <tr>
                        <td>2022</td>
                        <td><a href="path-to-exam-2022" download class="a" style="color: black;">Download 2022</a></td>
                    </tr>
                    <tr>
                        <td>2023</td>
                        <td><a href="path-to-exam-2023" download class="a" style="color: black;">Download 2023</a></td>
                    </tr>
                    <tr>
                        <td>2024</td>
                        <td><a href="path-to-exam-2024" download class="a" style="color: black;">Download 2024</a></td>
                    </tr>
                    <!-- Course 2 -->
                    <!-- Repeat the pattern for each course -->
                </tbody>
            </table>
        </div>
    </div>

    @include('partials.footer')
        <!-- Back to Top -->
    <a href="#" class="btn btn-dark back-to-top"><i class="fa fa-angle-up"></i></a>




    <!-- JavaScript Libraries -->
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.bundle.min.js"></script>
    <script src="../assets/lib/easing/easing.min.js"></script>
    <script src="../assets/lib/owlcarousel/owl.carousel.min.js"></script>


    <!-- Template Javascript -->
    <script src="../assets/js/main.js"></script>
</body>
</html>