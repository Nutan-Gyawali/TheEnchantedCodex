<?php
session_start();
$order_id = $_GET['order_id'] ?? null;

if (!$order_id) {
    echo "<script>alert('Invalid order.'); window.location.href='viewcart.php';</script>";
    exit();
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

<body class="container mt-5">
    <div class="alert alert-success">
        <h3>Thank you for your order!</h3>
        <p>Your order ID is <strong>#<?php echo htmlspecialchars($order_id); ?></strong></p>
        <a href="orders.php" class="btn btn-primary">View Orders</a>
    </div>
</body>

</html>