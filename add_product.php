<?php
session_start();

include 'connection.php';
include 'alerts.php';

echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>';

date_default_timezone_set('Asia/Kolkata');

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
        $_SESSION['alert'] = [
            'type' => 'error',
            'text' => 'The product URL you entered is not valid. Please check the URL and try again.',
        ];
        header('Location: dashboard.php');
        exit();
    }

    // Check if the user is a guest or regular user and apply limit
    $sql = "SELECT is_guest, urls_inserted_today FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $is_guest = $user['is_guest'];
        $urls_inserted_today = $user['urls_inserted_today'];

        // Check for today's URL insertion limit (max 7 URLs per day)
        if (!$is_guest) {
            // Get the count of URLs added by the user today
            $sql = "SELECT COUNT(*) as count FROM alerts WHERE user_id = ? AND DATE(created_at) = CURDATE()";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $alert_data = $result->fetch_assoc();

            $urls_added_today = $alert_data['count'];

            // Check if the user has already added 7 URLs today
            if ($urls_added_today >= 7) {
                $_SESSION['alert'] = [
                    'type' => 'warning',
                    'text' => 'You can only add up to 7 product URLs per day. Please try again tomorrow.',
                ];
                header('Location: dashboard.php');
                exit();
            }
        }

        // If limit not exceeded, proceed to add the URL
        $alert_expiry = date('Y-m-d H:i:s', strtotime("+60 days")); // Set expiry date for alert tracking
        $sql = "INSERT INTO alerts (user_id, url, alert_expiry) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $user_id, $product_url, $alert_expiry);

        if ($stmt->execute()) {
            // Increment urls_inserted_today for regular users
            $sql = "UPDATE users SET urls_inserted_today = urls_inserted_today + 1 WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();

            $_SESSION['alert'] = [
                'type' => 'success',
                'text' => 'The product URL has been successfully added to your tracking list.',
            ];
            header('Location: dashboard.php');
        } else {
            $_SESSION['alert'] = [
                'type' => 'error',
                'text' => 'There was an issue adding the product URL. Please try again later.',
            ];
            header('Location: dashboard.php');
            exit();
        }
    } else {
        $_SESSION['alert'] = [
            'type' => 'error',
            'text' => 'The user could not be found. Please check the details and try again.',
        ];
        header('Location: dashboard.php');
        exit();
    }
}

$conn->close();
?>