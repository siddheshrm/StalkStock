<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Track product availability effortlessly with StalkStock. Paste product URLs, get email alerts, and never miss your favorite products again. Supports Amazon, HMT Watches, and Meesho.">
    <title>StalkStock - Update Password</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../media/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../media/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../media/apple-touch-icon.png">
    <link rel="shortcut icon" href="../media/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&family=Prompt:wght@400;600&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/password_recovery.css">
    <link rel="stylesheet" href="../css/sweetalert_responsive.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
</head>

<body>
    <?php include '../alerts.php'; ?>

    <div class="container">
        <h2>Reset Your Password</h2>
        <form action="reset_password_handler.php" method="post" onsubmit="return validateForm()">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token']); ?>">

            <!-- Password Field with Show/Hide Functionality -->
            <div class="password-container">
                <input type="password" id="password" name="password" placeholder="new password" required>
                <span id="togglePassword1" class="toggle-password">Show</span>
            </div>

            <!-- Confirm Password Field with Show/Hide Functionality -->
            <div class="password-container">
                <input type="password" id="confirm_password" name="confirm_password" placeholder="confirm password" required>
                <span id="togglePassword2" class="toggle-password">Show</span>
            </div>

            <!-- CAPTCHA Section -->
            <div class="captcha-container">
                <img src="../captcha.php" alt="CAPTCHA Image" id="captcha-img">
                <span id="refresh-captcha" onclick="refreshCaptcha()" style="cursor: pointer; font-size: 2rem;">&#x21bb;</span>
                <input type="text" id="captcha" name="captcha" placeholder="enter captcha" required>
            </div>

            <button type="submit">Reset Password</button>
        </form>

        <p><a href="../index.php">Go to homepage</a></p><br>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const togglePassword1 = document.querySelector('#togglePassword1');
                const password = document.querySelector('#password');
                const togglePassword2 = document.querySelector('#togglePassword2');
                const confirmPassword = document.querySelector('#confirm_password');

                togglePassword1.addEventListener('click', function () {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    this.textContent = type === 'password' ? 'Show' : 'Hide';
                });

                togglePassword2.addEventListener('click', function () {
                    const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                    confirmPassword.setAttribute('type', type);
                    this.textContent = type === 'password' ? 'Show' : 'Hide';
                });
            });
        </script>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>
        <script>
            function refreshCaptcha() {
                document.getElementById('captcha-img').src = '../captcha.php?' + Date.now();
            }
        </script>
    </div>
</body>

</html>