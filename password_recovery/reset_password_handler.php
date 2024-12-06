<?php
session_start();

include '../connection.php';
include '../alerts.php';

date_default_timezone_set('Asia/Kolkata');
$current_time = date('Y-m-d H:i:s');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $user_captcha = $_POST['captcha'] ?? '';

    // CAPTCHA validation
    if (empty($user_captcha) || $user_captcha !== $_SESSION['captcha']) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'text' => 'Invalid CAPTCHA. Please try again.',
        ];
        header('Location: ../password_recovery/reset_password.php?token=' . urlencode($token));
        exit();
    }

    // Password validations
    if ($password !== $confirm_password) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'text' => 'The passwords you entered do not match. Please try again.',
        ];
        header('Location: ../password_recovery/reset_password.php?token=' . urlencode($token));
        exit();
    }

    if (strlen($password) < 8) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'text' => 'Your password must be at least 8 characters long. Please enter a stronger password.',
        ];
        header('Location: ../password_recovery/reset_password.php?token=' . urlencode($token));
        exit();
    }

    if (!preg_match('/(?=.*[a-z])(?=.*[A-Z])(?=.*[!@#$%^&*(),.?":{}|<>])/', $password)) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'text' => 'Your password must contain at least one lowercase, one uppercase, and one special character (e.g., !@#$%^&*()).',
        ];
        header('Location: ../password_recovery/reset_password.php?token=' . urlencode($token));
        exit();
    }

    $sql = "SELECT * FROM users WHERE reset_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Token does not exist in the database, invalid URL
        $_SESSION['alert'] = [
            'type' => 'error',
            'text' => 'The reset link is invalid. Please request a new one to update your password.',
        ];
        header('Location: ../password_recovery/reset_password.php?token=' . urlencode($token));
        exit();
    } else {
        $row = $result->fetch_assoc();

        // Check if the token has expired
        if ($row['reset_token_expiration'] <= $current_time) {
            // Token has expired
            $_SESSION['alert'] = [
                'type' => 'error',
                'text' => 'Your reset link has expired. Please request a new one to update your password.',
            ];
            header('Location: ../password_recovery/reset_password.php?token=' . urlencode($token));
            exit();
        }
    }

    // Hash the new password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiration = NULL, updated_at = ? WHERE reset_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $hashed_password, $current_time, $token);
    if ($stmt->execute()) {
        $_SESSION['alert'] = [
            'type' => 'success',
            'text' => 'Your password has been reset successfully. You can now log in to your account.',
        ];
        header('Location: ../index.php');
    } else {
        $_SESSION['alert'] = [
            'type' => 'error',
            'text' => 'Something went wrong. Please try again later.',
        ];
        header('Location: ../password_recovery/reset_password.php?token=' . urlencode($token));
    }
    $stmt->close();
}
$conn->close();
