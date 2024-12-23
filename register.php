<?php
session_start();

include 'connection.php';
include 'alerts.php';

echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>';

date_default_timezone_set('Asia/Kolkata');
$current_time = date('Y-m-d H:i:s');

use PHPMailer\PHPMailer\PHPMailer;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

function sendWelcomeEmail($email, $name)
{
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

    // Email subject and body
    $mail->isHTML(true);
    $mail->Subject = 'Welcome to StalkStock!';
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
                max-width: 800px;
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
                <h1>Welcome to StalkStock!</h1>
            </div>
            <div class='content'>
                <p><b>Hi $name,</b></p>
                <p>Welcome to <b>StalkStock</b>! We're excited to have you on board.</p>
                <p>To get started, simply log in and add your favorite products to track. You’ll be notified whenever
                    there’s an
                    update!</p>
                <p style='text-align: center;'>
                    <a href='https://stalkstock.in/'>
                        Log in here
                    </a>
                </p>
                <p>Thanks for choosing StalkStock!</p>
                <p><b>Cheers,<br>Team StalkStock</b></p>
            </div>
            <div class='footer'>
                &copy; 2024 StalkStock. All rights reserved.
            </div>
        </div>
    </body>

    </html>
    ";

    // Send the email
    if (!$mail->send()) {
        error_log("Error sending welcome email: " . $mail->ErrorInfo);
    }
}

// Registration Logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = strtolower($_POST['email']);
    $password = $_POST['password'];
    $user_captcha = $_POST['captcha'] ?? '';

    // CAPTCHA validation
    if (empty($user_captcha) || $user_captcha !== $_SESSION['captcha']) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'text' => 'Invalid CAPTCHA. Please try again.',
        ];
        header('Location: signup.php');
        exit();
    }

    // Basic Validation
    if (strlen($name) < 5) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'text' => 'Your name must be at least 5 characters long. Please enter a valid name.',
        ];
        header('Location: signup.php');
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'text' => 'The email address you entered is invalid. Please check and enter a valid email.',
        ];
        header('Location: signup.php');
        exit();
    } elseif (strlen($password) < 8) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'text' => 'Your password must be at least 8 characters long. Please enter a stronger password.',
        ];
        header('Location: signup.php');
        exit();
    }
    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>]).{8,}$/', $password)) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'text' => 'Your password must contain at least one lowercase, one uppercase, and one special character (e.g., !@#$%^&*()).',
        ];
        header('Location: signup.php');
        exit();
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id, is_guest FROM users WHERE LOWER(email) = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($user_id, $is_guest);
            $stmt->fetch();

            // Check if is_guest is 0 (email already registered as a non-guest user)
            if ($is_guest == 0) {
                $_SESSION['alert'] = [
                    'type' => 'error',
                    'text' => 'This email address is already registered. Please use a different email.',
                ];
                header('Location: signup.php');
                exit();
            } else {
                // Hash the password before storing it
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Update the guest user to a normal user
                $stmt = $conn->prepare("UPDATE users SET name = ?, password = ?, is_guest = 0, updated_at = ? WHERE id = ?");
                $stmt->bind_param("sssi", $name, $hashed_password, $current_time, $user_id);

                if ($stmt->execute()) {
                    // Send welcome email after registration
                    sendWelcomeEmail($email, $name);

                    // Redirect to login page with success message
                    $_SESSION['alert'] = [
                        'type' => 'success',
                        'text' => 'Registration successful! Please login to continue.',
                    ];
                    header('Location: index.php');
                    exit();
                } else {
                    $_SESSION['alert'] = [
                        'type' => 'error',
                        'text' => 'There was an issue registering your account. Please try again later.',
                    ];
                    header('Location: signup.php');
                    exit();
                }
            }
        } else {
            // Hash the password before storing it
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Insert new user into the database
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, created_at, updated_at) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $hashed_password, $current_time, $current_time);

            if ($stmt->execute()) {
                // Send welcome email after registration
                sendWelcomeEmail($email, $name);

                // Redirect to login page with success message
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'text' => 'Registration successful! Please login to continue.',
                ];
                header('Location: index.php');
                exit();
            } else {
                $_SESSION['alert'] = [
                    'type' => 'error',
                    'text' => 'There was an issue registering your account. Please try again later.',
                ];
                header('Location: signup.php');
            }

            $stmt->close();
        }
    }

    $conn->close();
}
