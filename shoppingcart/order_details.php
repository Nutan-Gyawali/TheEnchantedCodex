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
        echo "<script>alert('Please log in to view your order details.'); window.location.href='../login/login.html';</script>";
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $order_id = $_GET['order_id'] ?? null;

    if (!$order_id) {
        echo "<script>alert('Invalid order ID.'); window.location.href='orders.php';</script>";
        exit();
    }

    // Fetch the order
    $order_sql = "SELECT * FROM orders WHERE id = :order_id AND user_id = :user_id";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->execute([':order_id' => $order_id, ':user_id' => $user_id]);
    $order = $order_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo "<script>alert('Order not found.'); window.location.href='orders.php';</script>";
        exit();
    }

    // Fetch order items
    $items_sql = "SELECT oi.*, p.name, p.image_path 
                  FROM order_items oi
                  INNER JOIN products p ON oi.product_id = p.id
                  WHERE oi.order_id = :order_id";
    $items_stmt = $conn->prepare($items_sql);
    $items_stmt->execute([':order_id' => $order_id]);
    $order_items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body class="container mt-5">
    <h2 class="mb-4">Order #<?php echo htmlspecialchars($order['id']); ?></h2>
    <p><strong>Order Status:</strong> <span class="badge bg-primary"><?php echo htmlspecialchars($order['order_status']); ?></span></p>
    <p><strong>Order Date:</strong> <?php echo date("d M Y, H:i", strtotime($order['created_at'])); ?></p>
    <p><strong>Total Amount:</strong> Rs. <?php echo number_format($order['grand_total'], 2); ?></p>

    <h3 class="mt-4">Ordered Items</h3>
    <div class="table-responsive">
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Product</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_items as $item) { ?>
                    <tr>
                        <td>
                            <img src="../product/<?php echo htmlspecialchars($item['image_path']); ?>" alt="Product Image" width="50" height="50">
                            <?php echo htmlspecialchars($item['name']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                        <td>Rs. <?php echo number_format($item['subtotal'], 2); ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <a href="orders.php" class="btn btn-secondary mt-3">Back to Orders</a>
</body>

</html>