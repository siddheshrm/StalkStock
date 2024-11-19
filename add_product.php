<?php
session_start();
include 'connection.php';

// Ensure user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['id'];
    $product_url = $_POST['user_product_url'];

    // Basic validation for URL
    if (!filter_var($product_url, FILTER_VALIDATE_URL)) {
        echo '<script>alert("Invalid product URL.")</script>';
        echo '<script>window.location.href = "dashboard.php";</script>';
        exit();
    }

    // Insert the new product URL into the alerts table
    $alert_expiry = date('Y-m-d H:i:s', strtotime("+30 days")); // Set expiry date for alert tracking
    $sql = "INSERT INTO alerts (user_id, url, alert_sent, alert_expiry) 
            VALUES (?, ?, 0, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $product_url, $alert_expiry);

    if ($stmt->execute()) {
        echo '<script>alert("Product URL added successfully.")</script>';
        echo '<script>window.location.href = "dashboard.php";</script>';
    } else {
        echo '<script>alert("Failed to add product URL.")</script>';
        echo '<script>window.location.href = "dashboard.php";</script>';
    }
}

$conn->close();
