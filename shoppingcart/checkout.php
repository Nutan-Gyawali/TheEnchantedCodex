<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ensure user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Please log in to proceed to checkout.'); window.location.href='../login/login.html';</script>";
        exit();
    }

    $user_id = $_SESSION['user_id'];

    // Retrieve cart items
    $cart_sql = "SELECT sc.product_id, sc.quantity, p.name, p.price 
                 FROM shopping_cart sc
                 INNER JOIN products p ON sc.product_id = p.id
                 WHERE sc.user_id = :user_id";
    $cart_stmt = $conn->prepare($cart_sql);
    $cart_stmt->execute([':user_id' => $user_id]);
    $cart_items = $cart_stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($cart_items)) {
        echo "<script>alert('Your cart is empty.'); window.location.href='viewcart.php';</script>";
        exit();
    }

    // Calculate total amounts
    $subtotal = 0;
    foreach ($cart_items as $item) {
        $subtotal += $item['price'] * $item['quantity'];
    }

    $shipping_fee = 50.00; // Fixed shipping fee
    $discount = $_SESSION['discount'] ?? 0.00;
    $grand_total = $subtotal - $discount + $shipping_fee;

    // Insert into orders table
    $order_sql = "INSERT INTO orders (user_id, total_amount, shipping_fee, discount, grand_total) 
                  VALUES (:user_id, :total_amount, :shipping_fee, :discount, :grand_total)";
    $order_stmt = $conn->prepare($order_sql);
    $order_stmt->execute([
        ':user_id' => $user_id,
        ':total_amount' => $subtotal,
        ':shipping_fee' => $shipping_fee,
        ':discount' => $discount,
        ':grand_total' => $grand_total
    ]);

    $order_id = $conn->lastInsertId();

    // Insert order items
    foreach ($cart_items as $item) {
        $order_item_sql = "INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) 
                           VALUES (:order_id, :product_id, :quantity, :price, :subtotal)";
        $order_item_stmt = $conn->prepare($order_item_sql);
        $order_item_stmt->execute([
            ':order_id' => $order_id,
            ':product_id' => $item['product_id'],
            ':quantity' => $item['quantity'],
            ':price' => $item['price'],
            ':subtotal' => $item['price'] * $item['quantity']
        ]);
    }

    // Clear user's cart after checkout
    $clear_cart_sql = "DELETE FROM shopping_cart WHERE user_id = :user_id and product_id = :product_id ";
    $clear_cart_stmt = $conn->prepare($clear_cart_sql);
    $clear_cart_stmt->execute([':user_id' => $user_id]);

    unset($_SESSION['discount']); // Remove applied discount

    echo "<script>alert('Order placed successfully!'); window.location.href='order_success.php?order_id=$order_id';</script>";
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
