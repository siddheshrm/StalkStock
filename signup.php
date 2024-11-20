<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create new account</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
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
                <input type="text" id="name" name="name" placeholder="enter your name" min="5" required>
                <input type="email" id="email" name="email" placeholder="enter your email" required>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="password" min="8" required>
                    <span id="togglePassword" class="toggle-password">Show</span>
                </div>
                <button type="submit">Sign Up</button>
            </form>

            <p>Already have an account? <a href="index.php">Login here</a></p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');

            togglePassword.addEventListener('click', function() {
                const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
                password.setAttribute('type', type);
                this.textContent = type === 'password' ? 'Show' : 'Hide';
            });
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js" defer></script>
</body>

</html>