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
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    echo "<script>alert('Order ID not provided.'); window.location.href='orderdisplay.php';</script>";
    exit();
}

$order_id = $_GET['order_id'];

// Fetch order details with user information
$order_sql = "SELECT o.*, u.username, u.email, u.phone 
              FROM orders o 
              INNER JOIN users u ON o.user_id = u.id 
              WHERE o.id = ?";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->execute([$order_id]);
$order = $order_stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo "<script>alert('Order not found.'); window.location.href='orderdisplay.php';</script>";
    exit();
}

// Fetch order items with product details
$items_sql = "SELECT oi.*, p.name AS product_name, p.image_path 
              FROM order_items oi 
              LEFT JOIN products p ON oi.product_id = p.id 
              WHERE oi.order_id = ?";
$items_stmt = $conn->prepare($items_sql);
$items_stmt->execute([$order_id]);
$items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details #<?php echo htmlspecialchars($order_id); ?> - Enchanted Codex</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar custom-navbar">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="../Landing/logo.png" alt="Enchanted Codex Logo" class="logo">
                <h1 class="brand-name">The Enchanted Codex</h1>
            </a>
        </div>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="container">
            <h2>Order Details #<?php echo htmlspecialchars($order_id); ?></h2>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <div class="row">
            <!-- Order Information -->
            <div class="col-md-6">
                <div class="order-section">
                    <div class="section-header">
                        Order Information
                    </div>
                    <div class="section-content">
                        <div class="mb-3">
                            <span class="info-label">Order Date:</span>
                            <span class="info-value"><?php echo htmlspecialchars($order['created_at']); ?></span>
                        </div>
                        <div class="mb-3">
                            <span class="info-label">Status:</span>
                            <span class="status-badge status-<?php echo strtolower($order['order_status']); ?>">
                                <?php echo htmlspecialchars($order['order_status']); ?>
                            </span>
                        </div>
                        <div class="mb-3">
                            <span class="info-label">Total Amount:</span>
                            <span class="info-value">Rs. <?php echo number_format($order['total_amount'], 2); ?></span>
                        </div>
                        <div class="mb-3">
                            <span class="info-label">Payment Method:</span>
                            <span class="info-value"><?php echo htmlspecialchars($order['payment_method']); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Customer Information -->
            <div class="col-md-6">
                <div class="order-section">
                    <div class="section-header">
                        Customer Information
                    </div>
                    <div class="section-content">
                        <div class="mb-3">
                            <span class="info-label">Username:</span>
                            <span class="info-value"><?php echo htmlspecialchars($order['username']); ?></span>
                        </div>
                        <div class="mb-3">
                            <span class="info-label">Email:</span>
                            <span class="info-value"><?php echo htmlspecialchars($order['email']); ?></span>
                        </div>
                        <div class="mb-3">
                            <span class="info-label">Phone:</span>
                            <span class="info-value"><?php echo htmlspecialchars($order['phone']); ?></span>
                        </div>
                        <div class="mb-3">
                            <span class="info-label">Address:</span>
                            <span class="info-value"><?php echo nl2br(htmlspecialchars($order['shipping_address'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="order-section">
            <div class="section-header">
                Order Items
            </div>
            <div class="section-content">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Name</th>
                                <th>Quantity</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($items as $item): ?>
                                <tr>
                                    <td>
                                        <?php if (!empty($item['image_path'])): ?>
                                            <img src="../product/<?php echo htmlspecialchars($item['image_path']); ?>"
                                                alt="<?php echo htmlspecialchars($item['product_name']); ?>"
                                                class="product-image">
                                        <?php else: ?>
                                            <span>No Image</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['product_name'] ?? 'Unknown Product'); ?></td>
                                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                    <td>Rs. <?php echo number_format($item['price'], 2); ?></td>
                                    <td>Rs. <?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                <td><strong>Rs. <?php echo number_format($order['total_amount'], 2); ?></strong></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="mb-5">
            <a href="orderdisplay.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Orders
            </a>
        </div>
    </div>

    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>
</body>

</html>