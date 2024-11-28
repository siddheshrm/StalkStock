<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&family=Prompt:wght@400;600&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/password_recovery.css">
    <link rel="stylesheet" href="../css/sweetalert_responsive.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
</head>

<body>
    <div class="container">
        <h2>Enter Registered Email</h2>
        <form action="forgot_password_handler.php" method="post">
            <input type="email" id="email" name="email" placeholder="enter your email address" required>
            <button type="submit">Send Recovery Email</button>
        </form>

        <p><a href="../index.php">Go to homepage</a></p><br>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>
    <?php
    // Show error message
    if (isset($_SESSION['error'])) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                text: '" . htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') . "'
            });
        </script>";
        unset($_SESSION['error']);
    }

    // Show success message
    if (isset($_SESSION['message'])) {
        echo "<script>
            Swal.fire({
                icon: 'success',
                text: '" . htmlspecialchars($_SESSION['message'], ENT_QUOTES, 'UTF-8') . "'
            }).then(function() {
                window.location = '../index.php';
            });
        </script>";
        unset($_SESSION['message']);
    }
    ?>
</body>

</html>