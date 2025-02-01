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
        echo json_encode(["error" => "User not logged in"]);
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $product_id = $_GET['product_id'] ?? null;

    if ($product_id) {
        $query = "DELETE FROM shopping_cart WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $conn->prepare($query);
        $stmt->execute([
            ':user_id' => $user_id,
            ':product_id' => $product_id
        ]);

        echo json_encode(["success" => "Product removed from cart"]);
        header("Location:viewcart.php");
    } else {
        echo json_encode(["error" => "Invalid request"]);
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
