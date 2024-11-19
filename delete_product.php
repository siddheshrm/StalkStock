<?php
session_start();
include 'connection.php';

// Ensure user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_product'])) {
    $alert_id = $_POST['alert_id'];

    // Prepare and execute the delete query
    $sql = "DELETE FROM alerts WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $alert_id, $_SESSION['id']);

    if ($stmt->execute()) {
        echo '<script>alert("Product URL deleted successfully.")</script>';
        echo '<script>window.location.href = "dashboard.php";</script>';
    } else {
        echo '<script>alert("Failed to delete product URL.")</script>';
        echo '<script>window.location.href = "dashboard.php";</script>';
    }
}

$conn->close();
