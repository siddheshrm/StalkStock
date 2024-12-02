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

                <!-- CAPTCHA Section -->
                <div class="captcha-container">
                    <img src="captcha.php" alt="CAPTCHA Image" id="captcha-img">
                    <span id="refresh-captcha" onclick="refreshCaptcha()" style="cursor: pointer; font-size: 2rem;">&#x21bb;</span>
                    <input type="text" id="captcha" name="captcha" placeholder="enter captcha" required>
                </div>

                <button type="submit">Sign Up</button>
            </form>

            <p>Already have an account? <a href="index.php">Login here</a></p>
            <p>Have feedback? <a href="https://docs.google.com/forms/d/e/1FAIpQLSfyxm4izEIBZGquGHMOc4Kb4rojuqi7-DM3gW0smiotIki-BA/viewform?usp=sf_link" target="_blank">Click here to let us know!</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>
    <script>
        function refreshCaptcha() {
            document.getElementById('captcha-img').src = 'captcha.php?' + Date.now();
        }
    </script>

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