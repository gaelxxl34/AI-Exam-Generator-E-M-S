<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Server Error - IUEA Exam Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen bg-gradient-to-br from-fuchsia-500 to-rose-500 flex items-center justify-center font-sans">
    <div class="text-center text-white p-8">
        <div class="text-8xl font-bold drop-shadow-lg mb-4">
            <svg class="w-24 h-24 mx-auto mb-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                </path>
            </svg>
            500
        </div>
        <div class="text-2xl font-semibold mb-2">Server Error</div>
        <div class="text-lg opacity-90 mb-8">
            Something went wrong on our end. We're working to fix it.<br>
            Please try again in a few moments.
        </div>
        <a href="{{ route('login') }}"
            class="inline-flex items-center gap-2 bg-white text-rose-500 px-8 py-3 rounded-full font-semibold hover:-translate-y-1 hover:shadow-xl transition-all duration-300 hover:text-fuchsia-500">
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