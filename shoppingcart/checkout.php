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

        // Fetch selected cart items with product names
        $placeholders = implode(',', array_fill(0, count($selected_items), '?'));
        $stmt = $conn->prepare("SELECT sc.product_id, sc.quantity, p.name, p.price 
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
    <link rel="stylesheet" href="checkout.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .btn-orange {
            background-color: #ff7700;
            color: white;
            width: 100%;
            font-size: 18px;
            padding: 12px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease-in-out, transform 0.2s;
            font-weight: bold;
        }

        .btn-orange:hover {
            background-color: #cc5d00;
            transform: scale(1.05);
        }
    </style>

</head>

<body>
    <div class="container checkout-container">
        <h2 class="checkout-title">Checkout</h2>
        <form action="process_checkout.php" method="POST">
            <input type="hidden" name="selected_items" value="<?php echo htmlspecialchars($_POST['selected_items']); ?>">

            <!-- Shipping Address -->
            <div class="mb-3">
                <label for="shipping_address" class="form-label">Shipping Address</label>
                <input type="text" class="form-control" id="shipping_address" name="shipping_address" required>
            </div>

            <!-- Payment Method Selection with Images -->
            <div class="mb-3">
                <label class="form-label">Payment Method</label>
                <div class="payment-options">
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="COD" required>
                        <img src="../cod.png" alt="Cash on Delivery">
                        <span>Cash on Delivery</span>
                    </label>
                    <label class="payment-option">
                        <input type="radio" name="payment_method" value="Khalti" required>
                        <img src="../khalti.png" alt="Khalti">
                        <span>Khalti</span>
                    </label>
                </div>
            </div>

            <!-- Order Summary -->
            <h4 class="order-summary-title">Order Summary</h4>
            <ul class="list-group mb-3 order-summary">
                <?php
                $total = 0;
                foreach ($cart_items as $item) {
                    $subtotal = $item['quantity'] * $item['price'];
                    $total += $subtotal;
                ?>
                    <li class="list-group-item">
                        <strong><?php echo htmlspecialchars($item['name']); ?></strong> <br>
                        Quantity: <?php echo htmlspecialchars($item['quantity']); ?> <br>
                        Price: Rs. <?php echo number_format($subtotal, 2); ?>
                    </li>
                <?php } ?>
            </ul>
            <p class="total-amount"><strong>Total Amount: Rs. <?php echo number_format($total, 2); ?></strong></p>

            <input type="hidden" name="total_amount" value="<?php echo $total; ?>">
            <button type="submit" class="btn btn-orange">Place Order</button>
        </form>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const paymentOptions = document.querySelectorAll(".payment-option input");

            paymentOptions.forEach(option => {
                option.addEventListener("change", function() {
                    document.querySelectorAll(".payment-option").forEach(el => {
                        el.style.border = "2px solid #ddd"; // Reset border
                    });
                    this.closest(".payment-option").style.border = "3px solid #ff7700"; // Highlight selected
                });
            });
        });
    </script>

</body>

</html>