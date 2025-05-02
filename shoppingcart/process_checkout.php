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
        header("Location: ../login/login.html");
        exit();
    }
    // --- After session and DB setup ---

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../login/login.html");
        exit();
    }
    $user_id = $_SESSION['user_id'];

    // Retrieve form data
    $shipping_address = $_POST['shipping_address'];
    $payment_method = $_POST['payment_method'];
    $selected_items = explode(',', $_POST['selected_items']);
    $total_amount = $_POST['total_amount'];

    if (empty($selected_items)) {
        die("No items selected.");
    }

    // Fetch user details
    $stmt = $conn->prepare("SELECT firstname, email, phone FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$user) {
        die("User not found.");
    }

    // Create order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, shipping_address, payment_method, order_status, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $order_status = ($payment_method == 'Khalti') ? 'Pending' : 'Processing';
    $stmt->execute([$user_id, $total_amount, $shipping_address, $payment_method, $order_status]);
    $order_id = $conn->lastInsertId();

    // Prepare reusable statements
    $order_item_stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stock_update_stmt = $conn->prepare("UPDATE products SET quantity = quantity - ? WHERE id = ?");
    $stock_check_stmt = $conn->prepare("SELECT quantity FROM products WHERE id = ?");
    $product_price_stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
    $cart_quantity_stmt = $conn->prepare("SELECT quantity FROM shopping_cart WHERE user_id = ? AND product_id = ?");

    foreach ($selected_items as $product_id) {
        // Get product price
        $product_price_stmt->execute([$product_id]);
        $product = $product_price_stmt->fetch(PDO::FETCH_ASSOC);
        $price = $product['price'];

        // Get quantity from cart
        $cart_quantity_stmt->execute([$user_id, $product_id]);
        $quantity = $cart_quantity_stmt->fetchColumn();

        // Check stock
        $stock_check_stmt->execute([$product_id]);
        $available_stock = $stock_check_stmt->fetchColumn();
        if ($quantity > $available_stock) {
            die("Insufficient stock for product ID $product_id. Available: $available_stock, Requested: $quantity");
        }

        // Insert order item
        $order_item_stmt->execute([$order_id, $product_id, $quantity, $price]);

        // Update stock
        $stock_update_stmt->execute([$quantity, $product_id]);
    }

    // Clear cart
    $placeholders = implode(',', array_fill(0, count($selected_items), '?'));
    $delete_stmt = $conn->prepare("DELETE FROM shopping_cart WHERE user_id = ? AND product_id IN ($placeholders)");
    $delete_stmt->execute(array_merge([$user_id], $selected_items));


    // Handle Khalti payment
    if ($payment_method == 'Khalti') {
        $amount_paisa = $total_amount * 100;
        $return_url = "http://localhost/TheEnchantedCodex/shoppingcart/khalti_callback.php";
        $website_url = "http://localhost/TheEnchantedCodex/";

        $payload = [
            'return_url' => $return_url,
            'website_url' => $website_url,
            'amount' => $amount_paisa,
            'purchase_order_id' => $order_id,
            'purchase_order_name' => 'Order ' . $order_id,
            'customer_info' => [
                'name' => $user['firstname'],
                'email' => $user['email'],
                'phone' => $user['phone']
            ]
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://dev.khalti.com/api/v2/epayment/initiate/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Authorization: Key 6b780237318a499cb73322f31bbccd6c',
                'Content-Type: application/json',
            ],
        ]);

        $response = curl_exec($curl);
        $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $response_data = json_decode($response, true);

        if ($http_status == 200 && isset($response_data['payment_url'])) {
            header('Location: ' . $response_data['payment_url']);
            exit();
        } else {
            echo "Khalti payment initiation failed: " . $response;
            exit();
        }
    } else {
        header('Location: order_success.php');
        exit();
    }
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
