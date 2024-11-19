<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = strtolower($_POST['email']);
    $password = $_POST['password'];

    // Basic Validation
    if (strlen($name) < 5) {
        echo '<script>alert("Name must be at least 5 characters long.")</script>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<script>alert("Invalid email address.")</script>';
    } elseif (strlen($password) < 8) {
        echo '<script>alert("Password must be at least 8 characters long.")</script>';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE LOWER(email) = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            echo '<script>alert("Email already exists. Please use a different email.")</script>';
        } else {
            // Hash the password before storing it
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Insert user into the database
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed_password);

            try {
                if ($stmt->execute()) {
                    // Redirect to login page with success message
                    echo '<script>alert("Registration successful! Please login to continue.")</script>';
                    echo '<script>window.location.href = "index.php";</script>';
                    exit();
                } else {
                    echo '<script>alert("Error: Unable to register user. Please try again later.")</script>';
                }
            } catch (mysqli_sql_exception $e) {
                // Handle specific exceptions, e.g., unique constraint violation
                echo '<script>alert("Error: Unable to register user. Please try again later.")</script>';
                error_log("SQL Error: " . $e->getMessage());
            }
            $stmt->close();
        }
    }

    $conn->close();
}
