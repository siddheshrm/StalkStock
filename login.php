<?php
session_start();

include 'connection.php';

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

        // Verify the hashed password
        if (password_verify($password, $stored_password)) {
            $_SESSION['email'] = $email;
            $_SESSION['id'] = $row['id'];
            $_SESSION['name'] = $row['name'];

            // Redirect to dashboard.php after login
            header("Location: dashboard.php");
            exit();
        } else {
            // Incorrect password
            echo '<script>alert("Incorrect password.")</script>';
            echo '<script>window.location.href = "index.php";</script>';
            exit();
        }
    } else {
        // User does not exist
        echo '<script>alert("User does not exist.")</script>';
        echo '<script>window.location.href = "index.php";</script>';
        exit();
    }
}

$conn->close();
