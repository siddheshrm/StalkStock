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

// Pagination setup
$limit = 7;
$page = isset($_GET['page']) ? max((int) $_GET['page'], 1) : 1;
$offset = ($page - 1) * $limit; // Offset calculation

// Query to get user's tracked products with LIMIT and OFFSET
$sql = "SELECT * FROM alerts WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $user_id, $limit, $offset);
$stmt->execute();
$result = $stmt->get_result();

// Fetch data for current page
$tracked_products = [];
while ($row = $result->fetch_assoc()) {
    $tracked_products[] = $row;
}

// Total records count for pagination
$total_sql = "SELECT COUNT(*) AS total FROM alerts WHERE user_id = ?";
$stmt = $conn->prepare($total_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_result = $stmt->get_result();
$total_row = $total_result->fetch_assoc();
$total_records = $total_row['total'];

$total_pages = ceil($total_records / $limit); // Total pages required

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Track product availability effortlessly with StalkStock. Paste product URLs, get email alerts, and never miss your favorite products again. Supports Amazon, HMT Watches, and Meesho.">
    <title>My Dashboard</title>
    <link rel="icon" type="image/png" sizes="32x32" href="media/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="media/favicon-16x16.png">
    <link rel="apple-touch-icon" sizes="180x180" href="media/apple-touch-icon.png">
    <link rel="shortcut icon" href="media/favicon.ico">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" href="https://fonts.gstatic.com/s/poppins/v21/pxiByp8kv8JHgFVrLGT9Z1JlFd2JQEl8qw.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="https://fonts.gstatic.com/s/prompt/v10/-W__XJnvUD7dzB2KdNodREEje60k.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link rel="preload" href="https://fonts.gstatic.com/s/sourgummy/v1/8At5Gs2gPYuNDii97MjjBrLbYfdJvDU5AZfP5qBDfNFCP51H.woff2" as="font" type="font/woff2" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500&family=Prompt:wght@400;600&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/dashboard_responsive.css">
    <link rel="stylesheet" href="css/sweetalert_responsive.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.min.css">
</head>

<body>
    <?php include 'alerts.php'; ?>
    <?php include 'scrolling_text.php'; ?>

    <header>
        <h1>Welcome to StalkStock, <?php echo htmlspecialchars($user_name); ?>!</h1>
        <p>Alerts will be sent to: <?php echo htmlspecialchars($user_email); ?></p>
    </header>

    <form action="add_product.php" method="POST">
        <div class="form-inline-container">
            <input type="url" id="user_product_url" name="user_product_url" placeholder="add new product URL to track"
                required>
            <input type="text" id="user_price" name="user_price" placeholder="enter price in ₹ (optional)">
        </div>
        <button type="submit">Track Product</button>
    </form>

    <main>
        <h2>Your Tracked Products</h2>

        <?php if (count($tracked_products) > 0): ?>
            <table border="1">
                <thead>
                    <tr>
                        <th>No.</th>
                        <th>Price (₹)</th>
                        <th>Product Link</th>
                        <th>Product Added</th>
                        <th>Alert Expiry</th>
                        <th>Manage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sr_no = $offset + 1;
                    foreach ($tracked_products as $product): ?>
                        <tr>
                            <td><?php echo $sr_no++; ?></td>
                            <td>
                                <?php echo is_null($product['price']) ? "N/A" : number_format($product['price'], 2) . " ₹"; ?>
                            </td>
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

            <!-- Pagination Controls -->
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="prev">Previous</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="?page=<?php echo $i; ?>" class="<?php echo $i === $page ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="next">Next</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <p class="no-products-message">You have no tracked products yet.</p>
        <?php endif; ?>
    </main>

    <a class="logout-link" onclick="logoutConfirmation()">Logout</a>

    <script src="https://kit.fontawesome.com/9dd0cb4077.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.18/dist/sweetalert2.all.min.js"></script>

    <script>
        function logoutConfirmation() {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you really want to logout?",
                icon: 'question',
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

    <!-- Update placeholders on page load and on window resize -->
    <script>
        function updatePlaceholder() {
            const urlInput = document.getElementById('user_product_url');
            const priceInput = document.getElementById('user_price');

            if (window.matchMedia("(max-width: 475px)").matches) {
                urlInput.placeholder = "add product URL";
                priceInput.placeholder = " ₹ (optional)";
            } else if (window.matchMedia("(max-width: 768px)").matches) {
                urlInput.placeholder = "add new product URL to track";
                priceInput.placeholder = "price ₹ (optional)";
            }
        }

        window.addEventListener('DOMContentLoaded', updatePlaceholder);
        window.addEventListener('resize', updatePlaceholder);
    </script>
</body>

</html>