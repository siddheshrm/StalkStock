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
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>';
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Error!!",
                        text: "Invalid product URL.",
                        icon: "error",
                        confirmButtonText: "OK"
                    }).then(() => {
                        window.location.href = "dashboard.php";
                    });
                });
                </script>';
    }

    // Insert the new product URL into the alerts table
    $alert_expiry = date('Y-m-d H:i:s', strtotime("+30 days")); // Set expiry date for alert tracking
    $sql = "INSERT INTO alerts (user_id, url, alert_expiry) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $product_url, $alert_expiry);

    if ($stmt->execute()) {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>';
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Success!",
                        text: "Product URL added successfully!",
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        window.location.href = "dashboard.php";
                    });
                });
                </script>';
    } else {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>';
        echo '<script>
                    document.addEventListener("DOMContentLoaded", function() {
                        Swal.fire({
                            title: "Error!",
                            text: "Failed to add product URL.",
                            icon: "error",
                            confirmButtonText: "OK"
                        }).then(() => {
                            window.location.href = "dashboard.php";
                        });
                    });
                  </script>';
        exit();
    }
}

$conn->close();
