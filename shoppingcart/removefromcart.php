<?php
session_start();

// Get product ID from the form
if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];

    // Remove product from the cart
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
    }
}

// Redirect back to the cart
header("Location: viewcart.php");
exit;
