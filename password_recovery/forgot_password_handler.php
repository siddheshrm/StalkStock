<?php
include '../connection.php';

use PHPMailer\PHPMailer\PHPMailer;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

date_default_timezone_set('Asia/Kolkata');

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = strtolower($_POST['email']);

    // Check if email exists in the database
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $name = $row['name'];
        $is_guest = $row['is_guest'];

        // Handle guest account scenario
        if ($is_guest == 1) {
            $_SESSION['error'] = 'The provided email is associated with a guest account. Guest accounts do not support password reset. Please register for a full account.';
            $stmt->close();
            $conn->close();
            header('Location: ../password_recovery/forgot_password.php');
            exit();
        }

        // Email found, generate 100-character reset token
        $token = bin2hex(random_bytes(50));
        $expiry = date("Y-m-d H:i:s", strtotime('+15 minutes'));

        // Update user record with reset token and expiry time
        $sql = "UPDATE users SET reset_token = ?, reset_token_expiration = ? WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();

        // Send password recovery email
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'email@gmail.com';
        $mail->Password = 'abcd efgh ijkl efgh';    // Note: This is a placeholder password for demonstration purposes.
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('email@gmail.com', 'StalkStock');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Reset Your Password for StalkStock';
        $mail->Body = "<b>Hi $name,</b><br><br>It looks like you’ve requested a password reset for your StalkStock account. No worries! We’ve got you covered.<br><br>To regain access, simply click the button below and you’ll be back in action in no time:<br><a href='http://localhost:8080/StalkStock/password_recovery/reset_password.php?token=$token'>Reset My Password</a><br><br>🕒 Hurry! This link will only be valid for the next 15 minutes. After that, you'll need to request a fresh password reset.<br><br>If you didn’t request a password reset, just ignore this email. Your account is safe and sound!<br><br>Thanks for being part of StalkStock!<br><br><b>Cheers,<br>Team StalkStock</b>";

        if ($mail->send()) {
            $_SESSION['message'] = 'Password recovery email has been sent. Please check your inbox.';
            $stmt->close();
            $conn->close();
            header('Location: ../password_recovery/forgot_password.php');
            exit();
        } else {
            $_SESSION['error'] = 'Error in sending email. Mailer Error: ' . $mail->ErrorInfo;
            $stmt->close();
            $conn->close();
            header('Location: ../password_recovery/forgot_password.php');
            exit();
        }
    } else {
        $_SESSION['error'] = 'No account found with that email address.';
        $stmt->close();
        $conn->close();
        header('Location: ../password_recovery/forgot_password.php');
        exit();
    }
}
