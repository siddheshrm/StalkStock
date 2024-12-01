<?php
session_start();

include '../connection.php';
include '../alerts.php';

use PHPMailer\PHPMailer\PHPMailer;

require '../PHPMailer/src/Exception.php';
require '../PHPMailer/src/PHPMailer.php';
require '../PHPMailer/src/SMTP.php';

date_default_timezone_set('Asia/Kolkata');

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
            $_SESSION['alert'] = [
                'type' => 'error',
                'text' => 'The provided email is associated with a guest account. Guest accounts do not support password reset. Please register for a full account.',
            ];
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
        $mail->Password = 'abcd efgh ijkl mnop';    // Note: This is a placeholder password for demonstration purposes.
        $mail->SMTPSecure = 'ssl';
        $mail->Port = 465;

        $mail->setFrom('email@gmail.com', 'StalkStock');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Reset Your Password for StalkStock';
        $mail->Body = "
            <!DOCTYPE html>
            <html>

            <head>
                <style>
                    body {
                        font-family: Verdana, sans-serif;
                        background-color: #fff8d6;
                        margin: 0;
                        padding: 0;
                    }
                    .email-container {
                        max-width: 600px;
                        margin: 20px auto;
                        background: #fff0ad;
                        border-radius: 8px;
                        overflow: hidden;
                        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                    }
                    .header {
                        background-color: #ffe985;
                        padding: 20px;
                        text-align: center;
                    }
                    .header h1 {
                        margin: 0;
                        font-size: 2rem;
                    }
                    .content {
                        padding: 20px;
                        color: #333333;
                    }
                    .content p {
                        margin: 10px 0;
                        line-height: 1.6;
                    }
                    .content a {
                        display: inline-block;
                        padding: 10px 20px;
                        margin: 20px 0;
                        background-color: #ffdd47;
                        color: #000;
                        text-decoration: none;
                        border-radius: 4px;
                        font-weight: 500;
                    }
                    .content a:hover {
                        background-color: #ffd61f;
                        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
                    }
                    .footer {
                        background-color: #ffe985;
                        text-align: center;
                        padding: 10px;
                        font-size: 0.9rem;
                        font-weight: 400;
                    }
                </style>
            </head>

            <body>
                <div class='email-container'>
                    <div class='header'>
                        <h1>Password Reset</h1>
                    </div>
                    <div class='content'>
                        <p><b>Hi $name,</b></p>
                        <p>It looks like you’ve requested a password reset for your StalkStock account. No worries! We’ve got you
                            covered.</p>
                        <p>To regain access, simply click the button below and you’ll be back in action in no time:</p>
                        <p style='text-align: center;'>
                            <a href='http://localhost:8080/StalkStock/password_recovery/reset_password.php?token=$token'>
                                Reset My Password
                            </a>
                        </p>
                        <p>🕒 Hurry! This link will only be valid for the next 15 minutes. After that, you'll need to request a
                            fresh password reset.</p>
                        <p>If you didn’t request a password reset, just ignore this email. Your account is safe and sound!</p>
                        <p>Thanks for being part of StalkStock!</p>
                        <p><b>Cheers,<br>Team StalkStock</b></p>
                    </div>
                    <div class='footer'>
                        &copy; 2024 StalkStock. All rights reserved.
                    </div>
                </div>
            </body>

            </html>
        ";

        if ($mail->send()) {
            $_SESSION['alert'] = [
                'type' => 'success',
                'text' => 'A password recovery email has been sent. Please check your inbox.',
            ];
            header('Location: ../index.php');
            exit();
        } else {
            $_SESSION['alert'] = [
                'type' => 'error',
                'text' => 'There was an issue sending the email. Please try again later.'
            ];
            header('Location: ../password_recovery/forgot_password.php');
        }
        $stmt->close();
        $conn->close();
    } else {
        $_SESSION['alert'] = [
            'type' => 'error',
            'text' => 'No account was found with that email address. Please check and try again.'
        ];
        header('Location: ../password_recovery/forgot_password.php');
        $stmt->close();
        $conn->close();
    }
}
