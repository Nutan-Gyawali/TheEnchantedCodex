<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login/login.html");
        exit();
    }
    $user_id = $_SESSION['user_id'];

    // Fetch the latest order for the user
    $stmt = $conn->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->execute([$user_id]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        die("No recent orders found.");
    }

    // Fetch order items
    $stmt = $conn->prepare("SELECT oi.product_id, oi.quantity, oi.price, p.name 
                            FROM order_items oi 
                            INNER JOIN products p ON oi.product_id = p.id 
                            WHERE oi.order_id = ?");
    $stmt->execute([$order['id']]);
    $order_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Success</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Order Successful!</h2>
        <p>Your order has been placed successfully.</p>
        <h4>Order Details</h4>
        <p><strong>Order ID:</strong> <?php echo htmlspecialchars($order['id']); ?></p>
        <p><strong>Total Amount:</strong> Rs. <?php echo number_format($order['total_amount'], 2); ?></p>
        <p><strong>Shipping Address:</strong> <?php echo htmlspecialchars($order['shipping_address']); ?></p>
        <p><strong>Payment Method:</strong> <?php echo htmlspecialchars($order['payment_method']); ?></p>
        <h4>Order Items</h4>
        <ul class="list-group">
            <?php foreach ($order_items as $item) { ?>
                <li class="list-group-item">
                    <?php echo htmlspecialchars($item['name']); ?> - Quantity: <?php echo htmlspecialchars($item['quantity']); ?> - Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                </li>
            <?php } ?>
        </ul>
        <a href="index.php" class="btn btn-primary mt-3">Back to Home</a>
    </div>
</body>

</html>