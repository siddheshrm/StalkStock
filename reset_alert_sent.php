<?php
include 'connection.php';
include 'logs/log_handler.php';

try {
    // Reset alerts_sent to 0 for regular users at midnight
    $query = "UPDATE alerts
              JOIN users ON alerts.user_id = users.id
              SET alerts.alerts_sent = 0
              WHERE users.is_guest = 0";

    if ($conn->query($query) === TRUE) {
        $affectedRows = $conn->affected_rows;
        write_log("Alerts reset successfully for regular users. Rows affected: $affectedRows.");
    } else {
        write_log("Error resetting alerts: " . $conn->error);
    }
} catch (Exception $e) {
    write_log("Exception occurred while resetting alerts: " . $e->getMessage());
} finally {
    if (isset($conn)) {
        $conn->close();
    }
}
?>