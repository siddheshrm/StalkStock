<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to StalkStock</title>
</head>

<body>
    <header>
        <div class="container">
            <h1>Welcome to StalkStock</h1>
            <p>Your personal product tracker that notifies you when your favorite product is back in stock!</p>
        </div>
    </header>

    <section id="intro">
        <div class="container">
            <h2>How It Works?</h2>
            <p>
                Simply paste the URL of the product you want to track, and we will notify you when it becomes available.
                Never miss out on a sale again!
            </p>
        </div>
    </section>

    <section id="auth-options">
        <div class="container">
            <h2>Get Started</h2>
            <p>Track multiple products by signing up or log in:</p>

            <form method="POST" action="login.php">
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="Password" required>
                    <span id="togglePassword" class="toggle-password">Show</span>
                </div>
                <button type="submit">Login</button>
            </form>

            <p>Don't have an account? <a href="signup.php">Create an account</a></p>
            <p><a href="password_recovery/forgot_password.php">Forgot Password?</a></p>

            <p>Or track products as a guest:</p>
            <form action="guest_track.php" method="POST">
                <input type="text" id="name" name="name" placeholder="Enter your name" min="5" required>
                <input type="url" id="guest_product_url" name="guest_product_url" placeholder="Enter product URL" required>
                <input type="email" id="guest_email" name="guest_email" placeholder="Enter email to receive alerts" required>
                <button type="submit">Track as Guest</button>
            </form>
        </div>
    </section>

    <?php include 'login.php'; ?>
    <?php include 'guest_track.php'; ?>

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
</body>

</html>