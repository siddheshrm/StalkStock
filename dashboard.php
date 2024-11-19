<?php
session_start();

// Check if user is logged in by verifying session variables
if (!isset($_SESSION['id'])) {
    header("Location: index.php");
    exit();
}

include 'connection.php';

// Fetch user details from the session
$user_id = $_SESSION['id'];
$user_name = $_SESSION['name'];
$user_email = $_SESSION['email'];

// Query to get user's tracked products (or any other user-specific data)
$sql = "SELECT * FROM alerts WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch the data
$tracked_products = [];
while ($row = $result->fetch_assoc()) {
    $tracked_products[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
</head>

<body>
    <header>
        <h1>Welcome to StalkStock, <?php echo htmlspecialchars($user_name); ?>!</h1>
        <p>Alerts will be sent to: <?php echo htmlspecialchars($user_email); ?></p>
    </header>

    <a href="logout.php">Logout</a>
</body>

</html>