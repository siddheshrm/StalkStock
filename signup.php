<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create new account</title>
</head>

<body>
    <header>
        <div class="container">
            <h1>Create Your Account</h1>
            <p>Sign up to track multiple products and get alerts delivered to your inbox!</p>
        </div>
    </header>

    <section id="signup">
        <div class="container">
            <h2>Sign Up</h2>

            <form action="signup.php" method="POST">
                <input type="text" id="name" name="name" placeholder="Enter your name" min="5" required>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                <div class="password-container">
                    <input type="password" id="password" name="password" placeholder="Password" min="8" required>
                    <span id="togglePassword" class="toggle-password">Show</span>
                </div>
                <button type="submit">Sign Up</button>
            </form>

            <p>Already have an account? <a href="index.php">Login here</a></p>
        </div>
    </section>

    <?php include 'register.php'; ?>

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