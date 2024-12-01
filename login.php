<?php
session_start();

include 'connection.php';
include 'alerts.php';

echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = strtolower($_POST['email']);
    $password = $_POST['password'];

    // Query the database to check if the user exists
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $stored_password = $row['password'];
        $is_guest = $row['is_guest'];

        if ($is_guest) {
            // If the user is a guest, show a specific alert for guests
            $_SESSION['alert'] = [
                'type' => 'warning',
                'text' => 'You cannot log in as a guest with a password. Please register for a full account.',
            ];
            header('Location: index.php');
            exit();
        } else {
            // Verify the hashed password for non-guest users
            if (password_verify($password, $stored_password)) {
                $_SESSION['email'] = $email;
                $_SESSION['id'] = $row['id'];
                $_SESSION['name'] = $row['name'];

                // Set success message
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'text' => 'Login successful! Redirecting you to your dashboard.',
                ];
                header('Location: dashboard.php');
                exit();
            } else {
                // Incorrect password for registered users
                $_SESSION['alert'] = [
                    'type' => 'error',
                    'text' => 'The password you entered is incorrect. Please try again.',
                ];
                header('Location: index.php');
                exit();
            }
        }
    } else {
        // User does not exist
        $_SESSION['alert'] = [
            'type' => 'error',
            'text' => 'No user was found with that email address. Please check and try again.',
        ];
        header('Location: index.php');
        exit();
    }
}

$conn->close();
