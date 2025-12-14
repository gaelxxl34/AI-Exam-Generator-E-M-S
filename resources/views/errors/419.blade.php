<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Session Expired - IUEA Exam Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Auto-redirect after 3 seconds
        setTimeout(function () {
            window.location.href = "{{ route('login') }}";
        }, 3000);
    </script>
</head>

<body class="min-h-screen bg-gradient-to-br from-pink-200 to-blue-300 flex items-center justify-center font-sans">
    <div class="text-center text-white p-8">
        <div class="text-8xl font-bold drop-shadow-lg mb-4">
            <svg class="w-24 h-24 mx-auto mb-4 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
        </div>
        <div class="text-2xl font-semibold mb-2">Session Expired</div>
        <div class="text-lg opacity-90 mb-8">
            Your session has expired due to inactivity.<br>
            Redirecting you to login page...
        </div>
        <a href="{{ route('login') }}"
            class="inline-flex items-center gap-2 bg-white text-blue-400 px-8 py-3 rounded-full font-semibold hover:-translate-y-1 hover:shadow-xl transition-all duration-300 hover:text-pink-400">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1">
                </path>
            </svg>
            Login Now
        </a>
    </div>
</body>

</html>