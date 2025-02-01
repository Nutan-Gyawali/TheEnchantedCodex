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

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Please log in to view your cart.'); window.location.href='../login/login.html';</script>";
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Fetch cart items for the logged-in user
    $cart_sql = "SELECT sc.id, sc.product_id, sc.quantity, p.name AS product_name, p.price, p.image_path
                 FROM shopping_cart sc
                 INNER JOIN products p ON sc.product_id = p.id
                 WHERE sc.user_id = :user_id";
    $cart_stmt = $conn->prepare($cart_sql);
    $cart_stmt->execute([':user_id' => $user_id]);

    $cart_items = $cart_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="../Landing/logo.css">

    <link rel="stylesheet" href="viewcart.css">
    <style>


    </style>
</head>

<body>
    <nav class=" top-nav">
        <div class="logo-container">
            <div class="logo">
                <img src="../Landing/logo.png" alt="EC Logo">
            </div>
            <h1>The Enchanted Codex</h1>
        </div>
        <h1 class="nav-title">My Cart</h1>
        <div class="nav-buttons">
            <button class="nav-button" onclick="location.href='../Landing/explore.php'">
                <i class="fa fa-home"></i>
                Products
            </button>
            <button class="nav-button" onclick="location.href='../wishlist/index.php'">
                <i class="fa fa-heart"></i>
                Wishlist
            </button>
        </div>
    </nav>
    <div class="container mt-5">
        <h2 class="mb-4 custom-heading">Shopping Cart (<?php echo count($cart_items); ?>)</h2>

        <?php if (!empty($cart_items)) { ?>
            <div class="row">
                <div class="col-md-8">
                    <?php foreach ($cart_items as $item) { ?>
                        <div class="product-card d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="form-check me-3">
                                    <input class="form-check-input product-select" type="checkbox" value="<?php echo htmlspecialchars($item['product_id']); ?>" id="product-<?php echo htmlspecialchars($item['product_id']); ?>">
                                </div>
                                <img src="../product/<?php echo htmlspecialchars($item['image_path']); ?>" alt="Product Image" class="product-image me-3">
                                <div>
                                    <h5 class="mb-1"><?php echo htmlspecialchars($item['product_name']); ?></h5>
                                    <p class="mb-2">Rs. <?php echo number_format($item['price']); ?></p>
                                    <form action="update_quantity.php" method="POST" class="d-flex align-items-center gap-2">
                                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($item['product_id']); ?>">
                                        <button type="submit" name="change" value="-1" class="btn update-btn">-</button>
                                        <span class="quantity-value"><?php echo htmlspecialchars($item['quantity']); ?></span>
                                        <button type="submit" name="change" value="1" class="btn update-btn">+</button>
                                    </form>
                                </div>
                            </div>
                            <a href="removefromcart.php?product_id=<?php echo htmlspecialchars($item['product_id']); ?>&user_id=<?php echo htmlspecialchars($user_id); ?>" class="text-danger trash-icon">
                                <i class="fa fa-trash"></i>
                            </a>
                        </div>

                    <?php } ?>
                </div>

                <div class="col-md-4">
                    <div class="order-summary">
                        <h4>Order Summary</h4>
                        <p>Subtotal: <span id="subtotal">0</span></p>

                        <!-- Coupon/Voucher code input -->
                        <!-- Coupon/Voucher code input -->
                        <div class="coupon-section">
                            <label for="coupon-code">Coupon Code</label>
                            <input type="text" id="coupon-code" class="form-control" placeholder="Enter coupon code">
                            <button class="btn custom-button btn-secondary mt-3" onclick="applyCoupon()">Apply Coupon</button>
                            <p id="coupon-message" class="mt-2"></p>
                        </div>


                        <!-- Shipping fee display -->
                        <p>Shipping Fee: Rs. <span id="shipping-fee">50</span></p>

                        <!-- Total display -->
                        <p><strong>Total: Rs. <span id="total">0</span></strong></p>

                        <button class="btn custom-btn btn-primary w-100" onclick="proceedToCheckout()">Proceed to Checkout</button>
                    </div>
                </div>

            <?php } else { ?>
                <p>Your cart is empty.</p>
            <?php } ?>


            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const subtotalElement = document.getElementById('subtotal');
                    const totalElement = document.getElementById('total');
                    const shippingFeeElement = document.getElementById('shipping-fee');
                    const couponMessageElement = document.getElementById('coupon-message');
                    const productCheckboxes = document.querySelectorAll('.product-select');

                    let shippingFee = parseFloat(shippingFeeElement.innerText);
                    let appliedDiscount = 0;

                    function calculateSubtotal() {
                        let subtotal = 0;
                        productCheckboxes.forEach(checkbox => {
                            if (checkbox.checked) {
                                let productCard = checkbox.closest('.product-card');

                                // Get price from the paragraph element directly inside .product-card
                                let priceText = productCard.querySelector('p').innerText.replace('Rs. ', '').replace(',', '');
                                let quantityText = productCard.querySelector('.quantity-value').innerText;

                                let price = parseFloat(priceText);
                                let quantity = parseInt(quantityText);

                                subtotal += price * quantity; // Correct subtotal calculation
                            }
                        });

                        let total = subtotal - appliedDiscount + shippingFee;
                        subtotalElement.innerText = `Rs. ${subtotal.toFixed(2)}`; // Format display
                        totalElement.innerText = `Rs. ${total.toFixed(2)}`;
                    }

                    productCheckboxes.forEach(checkbox => {
                        checkbox.addEventListener('change', calculateSubtotal);
                    });

                    function applyCoupon() {
                        const couponCode = document.getElementById('coupon-code').value.trim();
                        let orderAmount = parseFloat(subtotalElement.innerText.replace('Rs. ', '').replace(',', ''));

                        if (!couponCode) {
                            couponMessageElement.innerText = 'Please enter a coupon code.';
                            return;
                        }

                        fetch('apply_coupon.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify({
                                    coupon_code: couponCode,
                                    order_amount: orderAmount
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log(data); // Debugging: Check actual response

                                if (data.success && data.discount !== undefined) {
                                    appliedDiscount = parseFloat(data.discount) || 0; // Ensure valid number
                                    calculateSubtotal(); // Recalculate total after applying discount
                                    couponMessageElement.innerText = `Coupon applied! Discount: Rs. ${appliedDiscount.toFixed(2)}`;
                                } else {
                                    couponMessageElement.innerText = data.message || 'Invalid coupon code.';
                                }
                            })
                            .catch(error => {
                                console.error('Coupon Error:', error);
                                couponMessageElement.innerText = 'An error occurred while applying the coupon.';
                            });
                    }


                    document.querySelector('.btn-secondary').addEventListener('click', applyCoupon);

                    // Call calculateSubtotal initially to set correct values
                    calculateSubtotal();
                });
            </script>
</body>

</html>