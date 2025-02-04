<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ensure user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Please log in to view your orders.'); window.location.href='../login/login.html';</script>";
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Fetch orders for the logged-in user
    $orders_sql = "SELECT * FROM orders WHERE user_id = :user_id ORDER BY created_at DESC";
    $orders_stmt = $conn->prepare($orders_sql);
    $orders_stmt->execute([':user_id' => $user_id]);
    $orders = $orders_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="success.css">
</head>

<body class="container mt-5">
    <h2 class="mb-4">My Orders</h2>

    <?php if (!empty($orders)) { ?>
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Order ID</th>
                        <th>Total Amount</th>
                        <th>Shipping Fee</th>
                        <!-- <th>Discount</th> -->
                        <th>Grand Total</th>
                        <th>Status</th>
                        <th>Order Date</th>
                        <th>Details</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $order) { ?>
                        <tr>
                            <td>#<?php echo htmlspecialchars($order['id']); ?></td>
                            <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>Rs. <?php echo "50"; ?></td>
                            <!-- <td>Rs. <?php echo number_format($order['discount'], 2); ?></td> -->
                            <td><strong>Rs. <?php echo number_format($order['total_amount'], 2); ?></strong></td>
                            <td><span class="badge bg-primary"><?php echo htmlspecialchars($order['order_status']); ?></span></td>
                            <td><?php echo date("d M Y, H:i", strtotime($order['created_at'])); ?></td>
                            <td><a href="order_details.php?order_id=<?php echo htmlspecialchars($order['id']); ?>" class="btn btn-info btn-sm">View</a></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <p>You haven't placed any orders yet.</p>
    <?php } ?>
    <a href="../Landing/explore.php" class="btn btn-primary mt-3">Back to Home</a>
</body>

</html>