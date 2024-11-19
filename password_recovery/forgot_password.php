<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
</head>

<body>
    <div class="container">
        <h2>Enter Registered Email</h2>
        <form action="forgot_password_handler.php" method="post">
            <label for="email">Enter your email address:</label>
            <input type="email" id="email" name="email" required>
            <input type="submit" value="Send Password Recovery Email">
        </form>

        <p><a href="../index.php">Go to homepage</a></p><br>

        <?php
        if (isset($_SESSION['error'])) {
            echo "<script>alert('" . htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') . "');</script>";
            unset($_SESSION['error']);
        }
        ?>
    </div>
</body>

</html>