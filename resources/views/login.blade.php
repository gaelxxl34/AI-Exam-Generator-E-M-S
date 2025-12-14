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

    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <style>
        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
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

        .animate-slideDown {
            animation: slideDown 0.3s ease-out;
        }
    </style>
</head>

<body class="font-sans">
    <!-- Background image -->
    <div class="min-h-screen bg-cover bg-center" style="background-image: url(/assets/img/login.webp);">
        <div class="min-h-screen flex items-center justify-center" style="background-color: rgba(0, 0, 0, 0.8);">
            <div class="container mx-auto px-4">
                <div class="flex justify-center">
                    <div class="w-full max-w-md">
                        <div class="bg-white rounded-2xl shadow-2xl p-6">
                            <!-- Logo with white background -->
                            <div
                                class="text-center bg-gradient-to-br from-white to-gray-50 p-6 rounded-xl shadow-sm mb-4">
                                <img src="/assets/img/iuea logo.png"
                                    alt="IUEA Logo - International University of East Africa"
                                    class="h-24 mx-auto object-contain drop-shadow-md">
                            </div>

                            <h2 class="text-2xl font-semibold text-center text-gray-800 mt-4">Lecturer Sign In</h2>
                            <p class="text-center text-gray-500 mb-6">Access your exam management portal</p>

                            <form id="loginForm" action="{{ route('authenticate') }}" method="POST" class="space-y-6">
                                @csrf

                                <!-- Session Expired Alert -->
                                @if (session('session_expired'))
                                    <div class="bg-yellow-50 border border-yellow-200 text-yellow-800 px-4 py-3 rounded-lg animate-slideDown relative"
                                        role="alert" id="sessionAlert">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <div>
                                                <strong class="font-semibold">Session Expired!</strong><br>
                                                <span class="text-sm">{{ session('session_expired') }}</span>
                                            </div>
                                        </div>
                                        <button type="button" onclick="this.parentElement.remove()"
                                            class="absolute top-2 right-2 text-yellow-600 hover:text-yellow-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endif

                                <!-- Error Messages -->
                                @if ($errors->has('login_error'))
                                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg animate-slideDown relative"
                                        role="alert" id="errorAlert">
                                        <div class="flex items-start">
                                            <svg class="w-5 h-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                </path>
                                            </svg>
                                            <div>
                                                <strong class="font-semibold">Login Failed!</strong><br>
                                                <span class="text-sm">{{ $errors->first('login_error') }}</span>
                                            </div>
                                        </div>
                                        <button type="button" onclick="this.parentElement.remove()"
                                            class="absolute top-2 right-2 text-red-600 hover:text-red-800">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                @endif

                                <!-- Email input -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email
                                        address</label>
                                    <input type="email" id="email" name="email"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all"
                                        value="{{ old('email') }}" required placeholder="Enter your email" />
                                </div>

                                <!-- Password input -->
                                <div>
                                    <label for="password"
                                        class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                                    <input type="password" id="password" name="password"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-900 focus:border-transparent transition-all"
                                        required placeholder="Enter your password" />
                                </div>

                                <!-- Forgot Password Link -->
                                <p class="text-center">
                                    <a class="text-red-600 hover:text-red-800 text-sm font-medium transition-colors"
                                        href="{{ route('forget-password') }}">Forgot Password?</a>
                                </p>

                                <!-- Submit button -->
                                <button type="submit" id="loginBtn"
                                    class="w-full bg-gray-900 text-white py-3 px-4 rounded-lg font-semibold hover:bg-red-800 disabled:bg-gray-500 disabled:cursor-not-allowed transition-all duration-300">
                                    <span id="btnText">Sign in</span>
                                    <span id="btnSpinner" class="hidden">
                                        <svg class="animate-spin inline w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Signing in...
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer Start -->
    @include('partials.footer')
    <!-- Footer End -->

    <!-- Back to Top -->
    <a href="#"
        class="fixed bottom-5 right-5 bg-gray-900 text-white p-3 rounded-full shadow-lg hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </a>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const loginForm = document.getElementById('loginForm');
            const loginBtn = document.getElementById('loginBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');

            loginForm.addEventListener('submit', function (e) {
                // Disable the button to prevent multiple submissions
                loginBtn.disabled = true;

                // Hide the normal text and show the spinner
                btnText.classList.add('hidden');
                btnSpinner.classList.remove('hidden');

                // Re-enable button after 10 seconds as a failsafe
                setTimeout(function () {
                    loginBtn.disabled = false;
                    btnText.classList.remove('hidden');
                    btnSpinner.classList.add('hidden');
                }, 10000);
            });

            // Auto-dismiss alerts after 8 seconds
            setTimeout(function () {
                const alerts = document.querySelectorAll('[role="alert"]');
                alerts.forEach(function (alert) {
                    alert.style.transition = 'opacity 0.5s';
                    alert.style.opacity = '0';
                    setTimeout(() => alert.remove(), 500);
                });
            }, 8000);
        });
    </script>
</body>

</html>