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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/password_recovery.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>
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

    <?php
    // Show error message
    if (isset($_SESSION['error'])) {
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error!',
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
                title: 'Success!',
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