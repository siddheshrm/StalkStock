<?php
session_start();

include 'connection.php';
echo '<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>';

// Ensure user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_product'])) {
    $alert_id = $_POST['alert_id'];

    // Check if the product belongs to the logged-in user
    $sql = "SELECT alert_expiry FROM alerts WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $alert_id, $_SESSION['id']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $alert_expiry = $row['alert_expiry'];

        // Delete the product URL
        $sql = "DELETE FROM alerts WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $alert_id, $_SESSION['id']);

        if ($stmt->execute()) {
            // Decrement urls_inserted_today only if the alert is not expired
            if ($alert_expiry > date('Y-m-d H:i:s')) {
                $sql = "UPDATE users SET urls_inserted_today = urls_inserted_today - 1 
                        WHERE id = ? AND is_guest = 0 AND urls_inserted_today > 0";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $_SESSION['id']);
                $stmt->execute();
            }

            $_SESSION['alert'] = [
                'type' => 'warning',
                'text' => 'The product URL has been successfully deleted from your tracking list.',
            ];
            header('Location: dashboard.php');
            exit();
        } else {
            $_SESSION['alert'] = [
                'type' => 'error',
                'text' => 'There was an issue deleting the product URL. Please try again later.',
            ];
            header('Location: dashboard.php');
            exit();
        }
    } else {
        $_SESSION['alert'] = [
            'type' => 'error',
            'text' => 'You cannot delete this product URL because it does not belong to your account.',
        ];
        header('Location: dashboard.php');
        exit();
    }
}

$conn->close();
?>