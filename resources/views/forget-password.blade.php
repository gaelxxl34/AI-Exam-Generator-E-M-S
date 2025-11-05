<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">
    <link href="../assets/css/navbar.css" rel="stylesheet">

    <!-- Custom CSS to override Bootstrap primary color -->
    <style>
        .btn-primary {
            background-color: red;
            border-color: red;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: darkred;
            border-color: darkred;
        }

        .btn-primary:disabled {
            background-color: #999;
            border-color: #999;
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
    </style>

</head>

<body>




    <!-- Reset Password Form -->
    <div id="intro" class="bg-image shadow-2-strong">
        <div class="mask d-flex align-items-center h-100" style="background-color: rgba(0, 0, 0, 0.8);">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-xl-5 col-md-8">
                        <div class="bg-white rounded-5 shadow-5-strong p-5" style="font-family: 'Montserrat';">
                            <!-- Reset Password Header -->
                            <h2 class="text-center mb-4">Reset Password</h2>

                            <form id="resetPasswordForm" action="{{ route('forget-password.action') }}" method="POST">
                                @csrf <!-- CSRF Token -->

                                <!-- Email input -->
                                <div class="form-outline mb-4">
                                    <input type="email" id="email" name="email" class="form-control" required />
                                    <label class="form-label" for="email">Email address</label>
                                </div>

                                <!-- Reset Password button -->
                                <button type="submit" id="resetBtn" class="btn btn-primary btn-block">
                                    <span id="btnText">Send Reset Link</span>
                                    <span id="btnSpinner" class="d-none">
                                        <i class="fas fa-spinner fa-spin"></i> Sending...
                                    </span>
                                </button>

                                <!-- Success/Error Messages -->
                                @if (session('status'))
                                    <p class="mt-3 text-success">{{ session('status') }}</p>
                                @endif
                                @if (session('error'))
                                    <p class="mt-3 text-danger">{{ session('error') }}</p>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Reset Password Form -->





    <!-- Footer Start -->
    @include('partials.footer')
    <!-- Footer End -->







    <!-- Back to Top -->
    <a href="#" class="btn btn-dark back-to-top"><i class="fa fa-angle-up"></i></a>


    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>

    <script>
        $(document).ready(function () {
            $('#resetPasswordForm').on('submit', function (e) {
                // Disable the button to prevent multiple submissions
                $('#resetBtn').prop('disabled', true);

                // Hide the normal text and show the spinner
                $('#btnText').addClass('d-none');
                $('#btnSpinner').removeClass('d-none');

                // Optional: Re-enable button after 10 seconds as a failsafe
                setTimeout(function () {
                    $('#resetBtn').prop('disabled', false);
                    $('#btnText').removeClass('d-none');
                    $('#btnSpinner').addClass('d-none');
                }, 10000);
            });
        });
    </script>




    <!-- Firebase Integration -->
    <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-app-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/9.0.0/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/ui/6.0.1/firebase-ui-auth.js"></script>







</body>

</html>