<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>404 - Page Not Found</title>

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="<?php echo asset('css/bootstrap.min.css'); ?>">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo asset('css/styles.css'); ?>">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .error-container {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            text-align: center;
            padding: 20px;
        }

        .error-code {
            font-size: 10rem;
            font-weight: 900;
            color: #0d6efd;
            animation: bounce 1.5s infinite;
        }

        .error-message {
            font-size: 1.5rem;
            color: #6c757d;
            margin-bottom: 20px;
        }

        .btn-home {
            padding: 10px 25px;
            font-size: 1.1rem;
        }

        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-15px);
            }
        }
    </style>
</head>
<body>

<div class="error-container">
    <div class="error-code">404</div>
    <div class="error-message">Oops! The page you are looking for doesn't exist.</div>
    <a href="<?php echo url('/'); ?>" class="btn btn-primary btn-home">Go to Home</a>
</div>

<!-- Bootstrap JS -->
<script src="<?php echo asset('js/bootstrap.bundle.min.js'); ?>"></script>

</body>
</html>
