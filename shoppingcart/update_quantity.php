<?php
session_start();
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ensure the user is logged in
    if (!isset($_SESSION['user_id'])) {
        echo json_encode(["error" => "User not logged in"]);
        exit();
    }

    $user_id = $_SESSION['user_id'];

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $product_id = $_POST['product_id'];
        $change = intval($_POST['change']); // -1 for decrease, 1 for increase

        // Fetch current quantity
        $query = "SELECT quantity FROM shopping_cart WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $conn->prepare($query);
        $stmt->execute([':user_id' => $user_id, ':product_id' => $product_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $new_quantity = max(1, $result['quantity'] + $change); // Prevent negative quantity

            // Update quantity in database
            $update_query = "UPDATE shopping_cart SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id";
            $update_stmt = $conn->prepare($update_query);
            $update_stmt->execute([
                ':quantity' => $new_quantity,
                ':user_id' => $user_id,
                ':product_id' => $product_id
            ]);

            echo json_encode(["success" => "Quantity updated", "new_quantity" => $new_quantity]);
        } else {
            echo json_encode(["error" => "Product not found in cart"]);
        }
    }
} catch (PDOException $e) {
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}
