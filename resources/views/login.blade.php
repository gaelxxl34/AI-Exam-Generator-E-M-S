<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Primary Meta Tags -->
    <title>Lecturer Login | IUEA Exam Management System - Upload & Manage Exams</title>
    <meta name="title" content="Lecturer Login | IUEA Exam Management System - Upload & Manage Exams">
    <meta name="description"
        content="Secure login portal for IUEA lecturers to upload, manage, and review examination papers. Access the International University of East Africa's digital exam management platform.">
    <meta name="keywords"
        content="IUEA lecturer login, IUEA exam upload, International University of East Africa lecturer portal, IUEA staff login, upload exam papers IUEA, lecturer exam management, IUEA academic portal, exam submission IUEA, IUEA faculty login, university exam system">
    <meta name="author" content="International University of East Africa">
    <meta name="robots" content="index, follow">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://ems.iuea.ac.ug/login">
    <meta property="og:site_name" content="IUEA Exam Management System">
    <meta property="og:title" content="Lecturer Login | IUEA Exam Management System">
    <meta property="og:description"
        content="Secure portal for IUEA lecturers to upload and manage examination papers at International University of East Africa.">
    <meta property="og:image" content="https://ems.iuea.ac.ug/assets/img/iuea logo.png">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">
    <meta property="og:locale" content="en_UG">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:url" content="https://ems.iuea.ac.ug/login">
    <meta name="twitter:title" content="Lecturer Login | IUEA Exam Management System">
    <meta name="twitter:description"
        content="Secure portal for IUEA lecturers to upload and manage examination papers.">
    <meta name="twitter:image" content="https://ems.iuea.ac.ug/assets/img/iuea logo.png">

    <!-- Canonical URL -->
    <link rel="canonical" href="https://ems.iuea.ac.ug/login">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="/assets/img/iuea logo.png">

    <!-- Schema.org structured data -->
    <script type="application/ld+json">
    {
      "@context": "https://schema.org",
      "@type": "WebPage",
      "name": "Lecturer Login - IUEA Exam Management System",
      "description": "Secure login portal for IUEA lecturers to upload and manage examination papers",
      "url": "https://ems.iuea.ac.ug/login",
      "isPartOf": {
        "@type": "WebSite",
        "name": "IUEA Exam Management System",
        "url": "https://ems.iuea.ac.ug"
      },
      "about": {
        "@type": "EducationalOrganization",
        "name": "International University of East Africa",
        "url": "https://www.iuea.ac.ug",
        "logo": "https://ems.iuea.ac.ug/assets/img/iuea logo.png"
      },
      "potentialAction": {
        "@type": "LoginAction",
        "target": {
          "@type": "EntryPoint",
          "urlTemplate": "https://ems.iuea.ac.ug/login",
          "actionPlatform": [
            "http://schema.org/DesktopWebPlatform",
            "http://schema.org/MobileWebPlatform"
          ]
        }
      }
    }
    </script>

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
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #7a0000;
            border-color: #7a0000;
        }

        .btn-primary:disabled {
            background-color: #666;
            border-color: #666;
            cursor: not-allowed;
            opacity: 0.8;
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
        .navbar-brand h1,
        .footer-brand h3 {
            font-weight: 600;
            /* Adjust the weight as needed */
        }

        .navbar {
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2);
            /* Adjust as needed */
        }

        #intro {
            background-image: url(../assets/img/login.webp);

            height: 100vh;
        }


        .navbar .nav-link {
            color: #fff !important;
        }

        /* Loading spinner animation */
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .fa-spinner {
            animation: spin 1s linear infinite;
        }

        /* Logo container styling */
        .logo-container {
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            padding: 1.5rem;
            border-radius: 1rem;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 1rem;
        }

        .logo-container img {
            filter: drop-shadow(0 2px 4px rgba(0, 0, 0, 0.1));
        }

        /* Alert Styles */
        .alert {
            border-radius: 0.5rem;
            font-size: 0.95rem;
            position: relative;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert i {
            margin-right: 0.5rem;
        }

        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }

        .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        .alert .close {
            padding: 0.75rem 1.25rem;
            color: inherit;
            opacity: 0.5;
        }

        .alert .close:hover {
            opacity: 1;
        }

        /* Ensure login container has consistent sizing */
        .bg-white.rounded-5 {
            min-height: auto;
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
                        <div class="bg-white rounded-5 shadow-5-strong p-1" style="font-family: 'Montserrat';">
                            <!-- Logo with white background -->
                            <div class="text-center logo-container">
                                <img src="/assets/img/iuea logo.png"
                                    alt="IUEA Logo - International University of East Africa" class="img-fluid"
                                    style="height: 100px; max-width: 100%; object-fit: contain;">
                            </div>

                            <h2 class="text-center mt-2">Lecturer Sign In</h2>
                            <p class="text-center text-muted mb-3">Access your exam management portal</p>

                            <form id="loginForm" action="{{ route('authenticate') }}" method="POST"
                                class="bg-white rounded-5 shadow-5-strong p-5 pt-3">
                                @csrf

                                <!-- Session Expired Alert -->
                                @if (session('session_expired'))
                                    <div class="alert alert-warning alert-dismissible fade show mb-3" role="alert">
                                        <i class="fas fa-clock"></i> <strong>Session Expired!</strong><br>
                                        {{ session('session_expired') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                <!-- Error Messages -->
                                @if ($errors->has('login_error'))
                                    <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                                        <i class="fas fa-exclamation-triangle"></i> <strong>Login Failed!</strong><br>
                                        {{ $errors->first('login_error') }}
                                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                @endif

                                <!-- Email input -->
                                <div class="form-outline mb-4">
                                    <input type="email" id="email" name="email" class="form-control"
                                        value="{{ old('email') }}" required />
                                    <label class="form-label" for="email">Email address</label>
                                </div>

                                <!-- Password input -->
                                <div class="form-outline mb-4">
                                    <input type="password" id="password" name="password" class="form-control"
                                        required />
                                    <label class="form-label" for="password">Password</label>
                                </div>

                                <!-- Forgot Password Link -->
                                <p class="text-center mb-3">
                                    <a class="a-moi" href="{{ route('forget-password') }}">Forgot Password?</a>
                                </p>
                                <!-- Submit button -->
                                <button type="submit" id="loginBtn" class="btn btn-primary btn-block">
                                    <span id="btnText">Sign in</span>
                                    <span id="btnSpinner" class="d-none">
                                        <i class="fas fa-spinner fa-spin"></i> Signing in...
                                    </span>
                                </button>

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
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#loginForm').on('submit', function (e) {
                // Disable the button to prevent multiple submissions
                $('#loginBtn').prop('disabled', true);

                // Hide the normal text and show the spinner
                $('#btnText').addClass('d-none');
                $('#btnSpinner').removeClass('d-none');

                // Optional: Re-enable button after 10 seconds as a failsafe
                setTimeout(function () {
                    $('#loginBtn').prop('disabled', false);
                    $('#btnText').removeClass('d-none');
                    $('#btnSpinner').addClass('d-none');
                }, 10000);
            });

            // Auto-dismiss alerts after 8 seconds
            setTimeout(function () {
                $('.alert').fadeOut('slow', function () {
                    $(this).remove();
                });
            }, 8000);
        });
    </script>







</body>

</html>