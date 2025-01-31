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
        $_SESSION['error_message'] = "Please log in to add items to your wishlist.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Get product_id from POST request
    if (!isset($_POST['product_id'])) {
        throw new Exception("Product ID is required");
    }

    $user_id = $_SESSION['user_id'];
    $product_id = $_POST['product_id'];

    // Check if item already exists in wishlist
    $check_sql = "SELECT id FROM wishlist WHERE user_id = :user_id AND product_id = :product_id";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->execute([
        ':user_id' => $user_id,
        ':product_id' => $product_id
    ]);

    if ($check_stmt->rowCount() > 0) {
        $_SESSION['error_message'] = "This item is already in your wishlist.";
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }

    // Add item to wishlist
    $insert_sql = "INSERT INTO wishlist (user_id, product_id) VALUES (:user_id, :product_id)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->execute([
        ':user_id' => $user_id,
        ':product_id' => $product_id
    ]);

    $_SESSION['success_message'] = "Item added to wishlist successfully!";
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Database error: " . $e->getMessage();
} catch (Exception $e) {
    $_SESSION['error_message'] = $e->getMessage();
}

// Redirect back to the previous page
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
