<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Track product availability effortlessly with StalkStock. Paste product URLs, get email alerts, and never miss your favorite products again. Supports Amazon, HMT Watches, and Meesho.">
    <title>StalkStock - Reset Link</title>
    <link rel="icon" type="image/png" sizes="32x32" href="../media/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="../media/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="../media/apple-touch-icon.png">
    <link rel="shortcut icon" href="../media/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.gstatic.com/s/poppins/v21/pxiByp8kv8JHgFVrLGT9Z1JlFd2JQEl8qw.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="https://fonts.gstatic.com/s/prompt/v10/-W__XJnvUD7dzB2KdNodREEje60k.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="https://fonts.gstatic.com/s/sourgummy/v1/8At5Gs2gPYuNDii97MjjBrLbYfdJvDU5AZfP5qBDfNFCP51H.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&family=Prompt:wght@400;600&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/password_recovery.css">
    <link rel="stylesheet" href="../css/sweetalert_responsive.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
</head>

<body>
    <?php include '../alerts.php'; ?>

    <div class="container">
        <h2>Enter Registered Email</h2>
        <form action="forgot_password_handler.php" method="post">
            <input type="email" id="email" name="email" placeholder="enter your email address" required>
            <button type="submit">Send Recovery Email</button>
        </form>

        <p><a href="../index.php">Go to homepage</a></p><br>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>
</body>

</html>