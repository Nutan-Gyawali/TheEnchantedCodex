<?php
session_start();

// DB connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check login
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Please log in to view order details.'); window.location.href='../login/login.html';</script>";
        exit();
    }

    if (!isset($_GET['order_id'])) {
        echo "<script>alert('Invalid order ID.'); window.location.href='my_orders.php';</script>";
        exit();
    }

    $order_id = $_GET['order_id'];
    $user_id = $_SESSION['user_id'];

    // Fetch order
    $order_sql = "SELECT * FROM orders WHERE id = :order_id AND user_id = :user_id";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->execute([':order_id' => $order_id, ':user_id' => $user_id]);
    $order = $order_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo "<script>alert('Order not found.'); window.location.href='my_orders.php';</script>";
        exit();
    }

    // Fetch items
    $items_sql = "SELECT oi.*, p.name, p.image_path, p.price 
                  FROM order_items oi
                  JOIN products p ON oi.product_id = p.id
                  WHERE oi.order_id = :order_id";
    $items_stmt = $conn->prepare($items_sql);
    $items_stmt->execute([':order_id' => $order_id]);
    $items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Order Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="order_detail.css">
</head>


<body class="container mt-5">
    <div class="header">
        <img src="../Landing/logo.png" alt="Logo" class="logo"> <!-- replace with your logo path -->
        <h2>Order #<?php echo htmlspecialchars($order['id']); ?></h2>
    </div>

    <!-- <h2 class="mb-4">Order #<?php echo htmlspecialchars($order['id']); ?></h2> -->

    <p><strong>Status:</strong> <?php echo htmlspecialchars($order['order_status']); ?></p>
    <p><strong>Order Date:</strong> <?php echo date("d M Y, H:i", strtotime($order['created_at'])); ?></p>
    <p><strong>Total Amount:</strong> Rs. <?php echo number_format($order['total_amount'], 2); ?></p>
    <p><strong>Shipping Fee:</strong> Rs. 50.00</p>
    <p><strong>Grand Total:</strong> <strong>Rs. <?php echo number_format($order['total_amount'], 2); ?></strong></p>

    <h4 class="mt-4">Items in this order:</h4>
    <?php if (!empty($items)) { ?>
        <div class="table-responsive">
            <table class="table table-bordered mt-3">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item) { ?>
                        <tr>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td><img src="../product/<?php echo htmlspecialchars($item['image_path']); ?>" alt="Product Image" class="product-image me-3"></td>
                            <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td>Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <p>No items found for this order.</p>
    <?php } ?>
    <a href="orders.php" class="btn btn-secondary mt-3">Back to Orders</a>
</body>

</html>