<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - IUEA Exam Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-pink-500 to-yellow-400 flex items-center justify-center font-sans">
    <div class="text-center text-white p-8">
        <div class="text-8xl font-bold drop-shadow-lg mb-4">
            <svg class="w-24 h-24 mx-auto mb-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                </path>
            </svg>
            403
        </div>
        <div class="text-2xl font-semibold mb-2">Access Denied</div>
        <div class="text-lg opacity-90 mb-8">
            You don't have permission to access this resource.<br>
            Please contact the administrator if you believe this is an error.
        </div>
        <a href="{{ route('login') }}"
            class="inline-flex items-center gap-2 bg-white text-pink-500 px-8 py-3 rounded-full font-semibold hover:-translate-y-1 hover:shadow-xl transition-all duration-300 hover:text-yellow-500">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                </path>
            </svg>
            Go to Login
        </a>
    </div>
</body>

</html>