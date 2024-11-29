<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Track product availability effortlessly with StalkStock. Paste product URLs, get email alerts, and never miss your favorite products again. Supports Amazon, HMT Watches, and Meesho.">
    <title>StalkStock - Join Us</title>
    <link rel="icon" type="image/png" sizes="32x32" href="media/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="media/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="media/apple-touch-icon.png">
    <link rel="shortcut icon" href="media/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.gstatic.com/s/poppins/v21/pxiByp8kv8JHgFVrLGT9Z1JlFd2JQEl8qw.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="https://fonts.gstatic.com/s/prompt/v10/-W__XJnvUD7dzB2KdNodREEje60k.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="https://fonts.gstatic.com/s/sourgummy/v1/8At5Gs2gPYuNDii97MjjBrLbYfdJvDU5AZfP5qBDfNFCP51H.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&family=Prompt:wght@400;600&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style_responsive.css">
    <link rel="stylesheet" href="css/sweetalert_responsive.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
</head>

<body>
    <div class="container">
        <!-- Left Section -->
        <div class="left-section">
            <h1>Create Your Account</h1>
            <p>Sign up to track multiple products and get alerts delivered to your inbox!</p>
            <h2>Why Sign Up?</h2>
            <p>With an account, you can track multiple products, receive notifications when they're back in stock, and manage your product alerts all in one place.</p>
        </div>

        <!-- Right Section -->
        <div class="right-section">
            <h2>Sign Up</h2>
            <form action="register.php" method="POST">
                <input type="text" id="name" name="name" placeholder="enter your name" required>
                <input type="email" id="email" name="email" placeholder="enter your email" required>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="password" required>
                    <span id="togglePassword" class="toggle-password">Show</span>
                </div>
                <button type="submit">Sign Up</button>
            </form>

            <p>Already have an account? <a href="index.php">Login here</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>
    <?php
    // Check if an alert message is set in session and display it
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                Swal.fire({
                    text: '" . $alert['text'] . "',
                    icon: '" . $alert['type'] . "',
                    confirmButtonText: 'OK'
                });
            });
        </script>";

        unset($_SESSION['alert']);
    }
    ?>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');

            togglePassword.addEventListener('click', function () {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.textContent = type === 'password' ? 'Show' : 'Hide';
            });
        });
    </script>
</body>

</html>