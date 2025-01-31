<?php
session_start();
$response = ['success' => false, 'message' => ''];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if user is logged in (assuming user_id is stored in session)
    if (!isset($_SESSION['user_id'])) {
        $_SESSION['error_message'] = "Please log in to add items to your cart.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Get product_id from POST request
    if (!isset($_POST['product_id'])) {
        throw new Exception("Product ID is required.");
    }

    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];
    $quantity = 1; // Default initial quantity

    // Check if item already exists in the cart
    $check_sql = "SELECT id FROM shopping_cart WHERE user_id = :user_id AND product_id = :product_id";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute([
        ':user_id' => $user_id,
        ':product_id' => $product_id
    ]);

    if ($check_stmt->rowCount() > 0) {
        $_SESSION['error_message'] = "This item is already in your cart. You can update the quantity on the cart page.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    } else {
        // Add new item to the cart
        $insert_sql = "INSERT INTO shopping_cart (user_id, product_id, quantity, created_at, updated_at) VALUES (:user_id, :product_id, :quantity, NOW(), NOW())";
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->execute([
            ':user_id' => $user_id,
            ':product_id' => $product_id,
            ':quantity' => $quantity
        ]);

        $_SESSION['success_message'] = "Item added to cart successfully!";
    }
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Database error: " . $e->getMessage();
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
}

// Redirect back to the previous page
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
