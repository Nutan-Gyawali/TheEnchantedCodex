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
        echo "<script>alert('Please log in to proceed with checkout.'); window.location.href='../login/login.html';</script>";
        exit();
    }

    $user_id = $_SESSION['user_id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['selected_items'])) {
        $selected_items = explode(',', $_POST['selected_items']);

        if (empty($selected_items)) {
            echo "<script>alert('No items selected for checkout.'); window.location.href='view_cart.php';</script>";
            exit();
        }

        // Fetch selected cart items
        $placeholders = implode(',', array_fill(0, count($selected_items), '?'));
        $stmt = $conn->prepare("SELECT sc.product_id, sc.quantity, p.price 
                               FROM shopping_cart sc 
                               INNER JOIN products p ON sc.product_id = p.id 
                               WHERE sc.user_id = ? AND sc.product_id IN ($placeholders)");
        $stmt->execute(array_merge([$user_id], $selected_items));
        $cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        echo "<script>alert('Invalid request.'); window.location.href='view_cart.php';</script>";
        exit();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>

<body>
    <div class="container mt-5">
        <h2>Checkout</h2>
        <form action="process_checkout.php" method="POST">
            <input type="hidden" name="selected_items" value="<?php echo htmlspecialchars($_POST['selected_items']); ?>">
            <div class="mb-3">
                <label for="shipping_address" class="form-label">Shipping Address</label>
                <input type="text" class="form-control" id="shipping_address" name="shipping_address" required>
            </div>
            <div class="mb-3">
                <label for="payment_method" class="form-label">Payment Method</label>
                <select class="form-select" id="payment_method" name="payment_method" required>
                    <option value="COD">Cash on Delivery</option>
                    <option value="Khalti">Khalti</option>
                    <option value="eSewa">eSewa</option>
                </select>
            </div>
            <h4>Order Summary</h4>
            <ul class="list-group mb-3">
                <?php
                $total = 0;
                foreach ($cart_items as $item) {
                    $subtotal = $item['quantity'] * $item['price'];
                    $total += $subtotal;
                ?>
                    <li class="list-group-item">
                        Product ID: <?php echo htmlspecialchars($item['product_id']); ?> - Quantity: <?php echo htmlspecialchars($item['quantity']); ?> - Rs. <?php echo number_format($subtotal, 2); ?>
                    </li>
                <?php } ?>
            </ul>
            <p><strong>Total Amount: Rs. <?php echo number_format($total, 2); ?></strong></p>
            <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
            <button type="submit" class="btn btn-primary">Place Order</button>
        </form>
    </div>
</body>

</html>