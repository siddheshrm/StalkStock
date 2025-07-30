<?php
session_start();

if (isset($_SESSION['id'])) {
    header("Location: dashboard.php");
    exit();
}
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&family=Prompt:wght@400;600&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/style_responsive.css">
    <link rel="stylesheet" href="css/sweetalert_responsive.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
</head>

<body>
    <?php include 'alerts.php'; ?>
    <?php include 'scrolling_text.php'; ?>

    <div class="container">
        <!-- Left Section -->
        <div class="left-section">
            <h1>Why StalkStock?</h1>
            <p>Track products and prices across multiple e-commerce platforms or even from sites without mobile apps.
                StalkStock notifies you when your favorite products are back in stock or when they drop to your desired
                price!
            </p>
            <h2>How It Works?</h2>
            <p>Simply paste the web-URL of the product you want to track, enter your desired price (optional), and we'll
                notify you when the product is back in stock or available at your preferred price. Never miss out on a
                deal again!</p>
            <h3>E-commerce Platforms We Support:</h3>
            <div class="supported-websites">
                <div class="website-item">
                    <a href="https://www.amazon.in/" target="_blank"><img src=" ./media/amazon.webp" alt="Amazon"
                            class="website-logo"></a>
                </div>
                <div class="website-item">
                    <a href="https://www.casioindiashop.com/" target="_blank">
                        <img src="./media/casio.webp" alt="Casio India" class="website-logo"></a>
                </div>
                <div class="website-item">
                    <a href="https://www.tatacliq.com/" target="_blank">
                        <img src="./media/tata_cliq.webp" alt="Tata Cliq" class="website-logo"></a>
                </div>
                <div class="website-item">
                    <a href="https://www.hmtwatches.in/" target="_blank">
                        <img src="./media/hmt.webp" alt="HMT Watches" class="website-logo"></a>
                </div>
                <div class="website-item">
                    <a href="https://www.meesho.com/" target="_blank">
                        <img src="./media/meesho.webp" alt="Meesho" class="website-logo"></a>
                </div>
            </div>
            <p style="margin-top: 25px;">Have questions? <a href="FAQ/FAQ.php"><i class="fas fa-question-circle"></i>
                    Visit our FAQ page</a></p>
        </div>

        <!-- Right Section -->
        <div class="right-section">
            <h2>Get Started</h2>

            <form method="POST" action="login.php">
                <input type="email" id="email" name="email" placeholder="enter your email" required>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="enter your password" required>
                    <span id="togglePassword" class="toggle-password">Show</span>
                </div>
                <button type="submit">Login</button>
            </form>

            <p>Don't have an account? <a href="signup.php">Create an account</a></p>
            <p><a href="password_recovery/forgot_password.php">Forgot Password?</a></p>

            <p>Or track products as a guest:</p>
            <form action="guest_track.php" method="POST">
                <input type="text" id="name" name="name" placeholder="enter your name" required>
                <input type="email" id="guest_email" name="guest_email" placeholder="enter email to receive alerts"
                    required>
                <div class="form-inline-container">
                    <input type="url" id="guest_product_url" name="guest_product_url" placeholder="enter product URL"
                        required>
                    <input type="text" id="guest_price" name="guest_price" placeholder="set price (₹) [optional]">
                </div>
                <p id="price-info">**set a price to get alerts for drops; without price, you'll only get availability
                    updates</p>

                <!-- CAPTCHA Section -->
                <div class="captcha-container">
                    <img src="captcha.php" alt="CAPTCHA Image" id="captcha-img">
                    <span id="refresh-captcha" onclick="refreshCaptcha()"
                        style="cursor: pointer; font-size: 2rem;">&#x21bb;</span>
                    <input type="text" id="captcha" name="captcha" placeholder="enter captcha" required>
                </div>

                <button type="submit">Track as Guest</button>
            </form>
            <p>Have feedback? <a
                    href="https://docs.google.com/forms/d/e/1FAIpQLSfyxm4izEIBZGquGHMOc4Kb4rojuqi7-DM3gW0smiotIki-BA/viewform?usp=sf_link"
                    target="_blank">Click here to let us know!</a></p>
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