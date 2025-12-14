<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password - IUEA Exam Management System</title>
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
    </style>
</head>

<body class="font-sans">
    <!-- Reset Password Form -->
    <div class="min-h-screen bg-cover bg-center" style="background-image: url(/assets/img/login.webp);">
        <div class="min-h-screen flex items-center justify-center" style="background-color: rgba(0, 0, 0, 0.8);">
            <div class="container mx-auto px-4">
                <div class="flex justify-center">
                    <div class="w-full max-w-md">
                        <div class="bg-white rounded-2xl shadow-2xl p-8">
                            <!-- Logo -->
                            <div class="text-center mb-6">
                                <img src="/assets/img/iuea logo.png" alt="IUEA Logo" class="h-20 mx-auto mb-4">
                                <h2 class="text-2xl font-semibold text-gray-800">Reset Password</h2>
                                <p class="text-gray-500 mt-2">Enter your email to receive a reset link</p>
                            </div>

                            <form id="resetPasswordForm" action="{{ route('forget-password.action') }}" method="POST"
                                class="space-y-6">
                                @csrf

                                <!-- Success/Error Messages -->
                                @if (session('status'))
                                    <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ session('status') }}
                                        </div>
                                    </div>
                                @endif
                                @if (session('error'))
                                    <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                                </path>
                                            </svg>
                                            {{ session('error') }}
                                        </div>
                                    </div>
                                @endif

                                <!-- Email input -->
                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email
                                        address</label>
                                    <input type="email" id="email" name="email"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent transition-all"
                                        required placeholder="Enter your email" />
                                </div>

                                <!-- Reset Password button -->
                                <button type="submit" id="resetBtn"
                                    class="w-full bg-red-600 text-white py-3 px-4 rounded-lg font-semibold hover:bg-red-700 disabled:bg-gray-400 disabled:cursor-not-allowed transition-all duration-300">
                                    <span id="btnText">Send Reset Link</span>
                                    <span id="btnSpinner" class="hidden">
                                        <svg class="animate-spin inline w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Sending...
                                    </span>
                                </button>

                                <!-- Back to Login -->
                                <p class="text-center">
                                    <a href="{{ route('login') }}"
                                        class="text-gray-600 hover:text-red-600 text-sm font-medium transition-colors">
                                        ← Back to Login
                                    </a>
                                </p>
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
    <a href="#"
        class="fixed bottom-5 right-5 bg-gray-900 text-white p-3 rounded-full shadow-lg hover:bg-gray-700 transition-colors">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </a>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const resetForm = document.getElementById('resetPasswordForm');
            const resetBtn = document.getElementById('resetBtn');
            const btnText = document.getElementById('btnText');
            const btnSpinner = document.getElementById('btnSpinner');

            resetForm.addEventListener('submit', function (e) {
                // Disable the button to prevent multiple submissions
                resetBtn.disabled = true;

                // Hide the normal text and show the spinner
                btnText.classList.add('hidden');
                btnSpinner.classList.remove('hidden');

                // Re-enable button after 10 seconds as a failsafe
                setTimeout(function () {
                    resetBtn.disabled = false;
                    btnText.classList.remove('hidden');
                    btnSpinner.classList.add('hidden');
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