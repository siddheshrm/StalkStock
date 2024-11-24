<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to StalkStock</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
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
                <input type="text" id="name" name="name" placeholder="enter your name" min="5" required>
                <input type="url" id="guest_product_url" name="guest_product_url" placeholder="enter product URL"
                    required>
                <input type="email" id="guest_email" name="guest_email" placeholder="enter email to receive alerts"
                    required>
                <button type="submit">Track as Guest</button>
            </form>
        </div>
    </div>

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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js" defer></script>
</body>

</html>