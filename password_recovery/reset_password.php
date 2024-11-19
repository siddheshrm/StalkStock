<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/password_recovery.css">
</head>

<body>
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
            <button type="submit">Reset Password</button>
        </form>

        <p><a href="../index.php">Go to homepage</a></p><br>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const togglePassword1 = document.querySelector('#togglePassword1');
                const password = document.querySelector('#password');
                const togglePassword2 = document.querySelector('#togglePassword2');
                const confirmPassword = document.querySelector('#confirm_password');

                togglePassword1.addEventListener('click', function() {
                    const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                    password.setAttribute('type', type);
                    this.textContent = type === 'password' ? 'Show' : 'Hide';
                });

                togglePassword2.addEventListener('click', function() {
                    const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
                    confirmPassword.setAttribute('type', type);
                    this.textContent = type === 'password' ? 'Show' : 'Hide';
                });
            });
        </script>

        <script>
            function validateForm() {
                var password = document.getElementById("password").value;
                var confirmPassword = document.getElementById("confirm_password").value;

                if (password.length < 8) {
                    alert("Password must be at least 8 characters long.");
                    return false;
                }
                if (password !== confirmPassword) {
                    alert("Passwords do not match.");
                    return false;
                }
                return true;
            }
        </script>

        <?php
        if (isset($_SESSION['error'])) {
            echo "<script>alert('" . $_SESSION['error'] . "');</script>";
            unset($_SESSION['error']);
        }
        ?>
    </div>
</body>

</html>