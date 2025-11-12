<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Access Denied - IUEA Exam Management System</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Montserrat', sans-serif;
        }

        .error-container {
            text-align: center;
            color: white;
            padding: 2rem;
        }

        .error-code {
            font-size: 8rem;
            font-weight: bold;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.2);
        }

        .error-message {
            font-size: 1.5rem;
            margin: 1rem 0;
        }

        .error-description {
            font-size: 1rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .btn-home {
            background-color: white;
            color: #fa709a;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-home:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.2);
            color: #fee140;
            text-decoration: none;
        }
    </style>
</head>

<body>
    <div class="error-container">
        <div class="error-code">
            <i class="fas fa-lock"></i> 403
        </div>
        <div class="error-message">Access Denied</div>
        <div class="error-description">
            You don't have permission to access this resource.<br>
            Please contact the administrator if you believe this is an error.
        </div>
        <a href="{{ route('login') }}" class="btn-home">
            <i class="fas fa-home"></i> Go to Login
        </a>
    </div>
</body>

</html>