<?php
include 'connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $product_url = $_POST['guest_product_url'];
    $email = strtolower($_POST['guest_email']);

    // Basic Validation
    if (strlen($name) < 5) {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>';
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Error!",
                        text: "Name must be at least 5 characters long.",
                        icon: "error",
                        confirmButtonText: "OK"
                    }).then(() => {
                        window.location.href = "index.php";
                    });
                });
              </script>';
        exit();
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>';
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Error!",
                        text: "Invalid email address.",
                        icon: "error",
                        confirmButtonText: "OK"
                    }).then(() => {
                        window.location.href = "index.php";
                    });
                });
              </script>';
        exit();
    } elseif (empty($product_url) || !filter_var($product_url, FILTER_VALIDATE_URL)) {
        echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>';
        echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Error!",
                        text: "Invalid product URL.",
                        icon: "error",
                        confirmButtonText: "OK"
                    }).then(() => {
                        window.location.href = "index.php";
                    });
                });
              </script>';
        exit();
    } else {
        // Check if email exists in users table (whether it's a guest or registered)
        $sql_check_user = "SELECT id, is_guest FROM users WHERE email = ?";
        $stmt_check_user = $conn->prepare($sql_check_user);
        $stmt_check_user->bind_param("s", $email);
        $stmt_check_user->execute();
        $result_check_user = $stmt_check_user->get_result();

        if ($result_check_user->num_rows > 0) {
            // User exists, get user id and check if it's a guest
            $row = $result_check_user->fetch_assoc();
            $user_id = $row['id'];
            $is_guest = $row['is_guest'];

            // If user is not a guest and already registered
            if ($is_guest == 0) {
                echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>';
                echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Error!",
                        text: "This email is already associated with a regular user account.",
                        icon: "error",
                        confirmButtonText: "OK"
                    }).then(() => {
                        window.location.href = "index.php";
                    });
                });
              </script>';
                exit();
            }
        } else {
            // If user does not exist, insert guest user
            $sql_insert_guest = "INSERT INTO users (name, email, is_guest) VALUES (?, ?, 1)";
            $stmt_insert_guest = $conn->prepare($sql_insert_guest);
            $stmt_insert_guest->bind_param("ss", $name, $email);
            $stmt_insert_guest->execute();

            // Get the new user ID
            $user_id = $stmt_insert_guest->insert_id;
        }

        // Insert a new alert for this product tracking
        $alert_expiry = date('Y-m-d H:i:s', strtotime("+30 days")); // Set expiry date for alert tracking
        $sql_insert_alert = "INSERT INTO alerts (user_id, url, alert_expiry) VALUES (?, ?, ?)";
        $stmt_insert_alert = $conn->prepare($sql_insert_alert);
        $stmt_insert_alert->bind_param("iss", $user_id, $product_url, $alert_expiry);

        // Execute the alert insertion
        if ($stmt_insert_alert->execute()) {
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>';
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Success!",
                        text: "Your product tracking has been saved successfully. You will receive alerts soon.",
                        icon: "success",
                        confirmButtonText: "OK"
                    }).then(() => {
                        window.location.href = "index.php";
                    });
                });
              </script>';
            exit();
        } else {
            echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>';
            echo '<script>
                document.addEventListener("DOMContentLoaded", function() {
                    Swal.fire({
                        title: "Error!",
                        text: "Error saving product tracking. Please try again later.",
                        icon: "error",
                        confirmButtonText: "OK"
                    }).then(() => {
                        window.location.href = "index.php";
                    });
                });
              </script>';
        }

        // Close prepared statements
        $stmt_insert_alert->close();
        $stmt_insert_guest->close();
        $stmt_check_user->close();
    }
}

$conn->close();
