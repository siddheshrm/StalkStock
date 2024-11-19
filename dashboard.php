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
$sql = "SELECT * FROM alerts WHERE user_id = ? ORDER BY created_at DESC";
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

    <h3>Add New Product URL to Track</h3>
    <form action="add_product.php" method="POST">
        <input type="url" id="user_product_url" name="user_product_url" placeholder="Enter product URL" required>
        <button type="submit">Track Product</button>
    </form>

    <main>
        <h2>Your Tracked Products</h2>

        <?php if (count($tracked_products) > 0): ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>Sr. No.</th>
                        <th>Product URL</th>
                        <th>Product Added Date</th>
                        <th>Product Alert Expiry Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sr_no = 1;
                    foreach ($tracked_products as $product): ?>
                        <tr>
                            <td><?php echo $sr_no++; ?></td>
                            <td>
                                <a href="<?php echo htmlspecialchars($product['url']); ?>" target="_blank">
                                    <?php echo htmlspecialchars($product['url']); ?>
                                </a>
                            </td>
                            <td><?php echo htmlspecialchars($product['created_at']); ?></td>
                            <td><?php echo htmlspecialchars($product['alert_expiry']); ?></td>
                            <td>
                                <form action="delete_product.php" method="POST" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    <input type="hidden" name="alert_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" name="delete_product">Delete</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>You have no tracked products yet.</p>
        <?php endif; ?>
    </main>

    <a href="index.php">Logout</a>
</body>

</html>