<?php
session_start();

include '../connection.php';
include '../alerts.php';

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

    $sql = "SELECT * FROM users WHERE reset_token = ? AND reset_token_expiration > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $_SESSION['alert'] = [
            'type' => 'error',
            'text' => 'Your request is either invalid or has expired. Please create a new one to update your password.',
        ];
        header('Location: ../password_recovery/reset_password.php?token=' . urlencode($token));
        exit();
    }

    // Hash the new password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "UPDATE users SET password = ?, reset_token = NULL, reset_token_expiration = NULL WHERE reset_token = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $hashed_password, $token);
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
