<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">

    <link href="../assets/css/navbar.css" rel="stylesheet">
      <!-- Custom CSS to override Bootstrap primary color -->
      <style>
        .btn-primary {
            background-color: black;
            border-color: black;
        }

        .btn-primary:hover {
            background-color: #7a0000;
            border-color: #7a0000;
        }

        .text-primary {
            color: red !important;
        }
        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
        }

        /* Custom CSS to make text bolder */
        .navbar-brand h1, .footer-brand h3 {
            font-weight: 600; /* Adjust the weight as needed */
        }
        .navbar {
            box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2); /* Adjust as needed */
        }
        #intro {
        background-image: url(../assets/img/login.webp);
    
        height: 100vh;
      }


      .navbar .nav-link {
        color: #fff !important;
      }


    </style>

</head>
<body>


   

<!-- login.blade.php -->

<!-- Background image -->
<div id="intro" class="bg-image shadow-2-strong">
    <div class="mask d-flex align-items-center h-100" style="background-color: rgba(0, 0, 0, 0.8);">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-5 col-md-8">
                    <div class="bg-white rounded-5 shadow-5-strong p-1"  style="font-family: 'Montserrat';">
                        <!-- Simple Text Header -->

                        <div class="text-center mb-2">
                            <img src="https://online.iuea.ac.ug/pluginfile.php/1/theme_remui/logo/1709968828/IUEA%20Logo%20-%20Moodle%201280x525.png" alt="Logo" class="img-fluid" style="height: 100px;"> <!-- The img-fluid class makes the image responsive -->
                        </div>


                        <h2 class="text-center mt-2 ">Sign in</h2>

                        <form id="loginForm" action="{{ route('authenticate') }}" method="POST" class="bg-white rounded-5 shadow-5-strong p-5">
                             @csrf
                            <!-- Email input -->
                            <div class="form-outline mb-4">
                                <input type="email" id="email" name="email" class="form-control" required/>
                                <label class="form-label" for="email">Email address</label>
                            </div>

                            <!-- Password input -->
                            <div class="form-outline mb-4">
                                <input type="password" id="password" name="password" class="form-control" required/>
                                <label class="form-label" for="password">Password</label>
                            </div>

                            <!-- Forgot Password Link -->
                            <p class="text-center mb-3">
                                <a class="a-moi" href="{{ route('forget-password') }}">Forgot Password?</a>
                            </p>
                            <!-- Submit button -->
                            <button type="submit" class="btn btn-primary btn-block">Sign in</button>

                      
                            @if ($errors->has('login_error'))
                                <p class="mt-3 text-danger">{{ $errors->first('login_error') }}</p>
                            @endif


                         
                        </form>   

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Background image -->



    <!-- Footer Start -->
        @include('partials.footer')
    <!-- Footer End -->


    <!-- Back to Top -->
    <a href="#" class="btn btn-dark back-to-top"><i class="fa fa-angle-up"></i></a>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>







</body>
</html>
