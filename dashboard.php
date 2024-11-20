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
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Prompt:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
</head>

<body>
    <header>
        <h1>Welcome to StalkStock, <?php echo htmlspecialchars($user_name); ?>!</h1>
        <p>Alerts will be sent to: <?php echo htmlspecialchars($user_email); ?></p>
    </header>

    <form action="add_product.php" method="POST">
        <input type="url" id="user_product_url" name="user_product_url" placeholder="add new product URL to track" required>
        <button type="submit">Track Product</button>
    </form>

    <main>
        <h2>Your Tracked Products</h2>

        <?php if (count($tracked_products) > 0): ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Product Link</th>
                        <th>Date Added</th>
                        <th>Alert Expiry Date</th>
                        <th>Manage</th>
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
                            <td><?php echo date('Y-m-d', strtotime($product['created_at'])); ?></td>
                            <td><?php echo date('Y-m-d', strtotime($product['alert_expiry'])); ?></td>
                            <td>
                                <form action="delete_product.php" method="POST">
                                    <input type="hidden" name="alert_id" value="<?php echo $product['id']; ?>">
                                    <button type="submit" name="delete_product">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p class="no-products-message">You have no tracked products yet.</p>
        <?php endif; ?>
    </main>

    <a class="logout-link" onclick="logoutConfirmation()">Logout</a>
    <script src="https://kit.fontawesome.com/9dd0cb4077.js" crossorigin="anonymous"></script>
    <script>
        function logoutConfirmation() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to logout?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, logout!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'index.php';
                }
            });
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>
</body>

</html>