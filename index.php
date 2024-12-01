<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Track product availability effortlessly with StalkStock. Paste product URLs, get email alerts, and never miss your favorite products again. Supports Amazon, HMT Watches, and Meesho.">
    <title>StalkStock - Track Products</title>
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
    <?php include 'alerts.php'; ?>

    <div class="container">
        <!-- Left Section -->
        <div class="left-section">
            <h1>Welcome to StalkStock</h1>
            <p>Your personal product tracker that notifies you when your favorite product is back in stock!</p>
            <h2>How It Works?</h2>
            <p>Simply paste the URL of the product you want to track, and we will notify you when it becomes available.
                Never miss out on a sale again!</p>
            <h3>E-commerce Platforms We Support:</h3>
            <div class="supported-websites">
                <div class="website-item">
                    <a href="https://www.amazon.in/" target="_blank"><img src=" ./media/amazon.webp" alt="Amazon"
                            class="website-logo"></a>
                </div>
                <div class="website-item">
                    <a href="https://www.meesho.com/" target="_blank">
                        <img src="./media/meesho.webp" alt="Meesho" class="website-logo"></a>
                </div>
                <div class="website-item">
                    <a href="https://www.hmtwatches.in/" target="_blank">
                        <img src="./media/hmt.webp" alt="HMT Watches" class="website-logo"></a>
                </div>
            </div>
        </div>

        <!-- Right Section -->
        <div class="right-section">
            <h2>Get Started</h2>
            <p>Track multiple products by signing up or log in:</p>

            <form method="POST" action="login.php">
                <input type="email" id="email" name="email" placeholder="enter your email" required>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="password" required>
                    <span id="togglePassword" class="toggle-password">Show</span>
                </div>
                <button type="submit">Login</button>
            </form>

            <p>Don't have an account? <a href="signup.php">Create an account</a></p>
            <p><a href="password_recovery/forgot_password.php">Forgot Password?</a></p>

            <p>Or track products as a guest:</p>
            <form action="guest_track.php" method="POST">
                <input type="text" id="name" name="name" placeholder="enter your name" required>
                <input type="url" id="guest_product_url" name="guest_product_url" placeholder="enter product URL"
                    required>
                <input type="email" id="guest_email" name="guest_email" placeholder="enter email to receive alerts"
                    required>
                <button type="submit">Track as Guest</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>
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