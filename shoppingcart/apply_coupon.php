<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

$response = ['success' => false, 'message' => 'Invalid request.'];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents("php://input"), true);

    if (!isset($data['coupon_code']) || !isset($data['order_amount'])) {
        $response['message'] = "Coupon code and order amount are required.";
        echo json_encode($response);
        exit();
    }

    $coupon_code = $data['coupon_code'];
    $order_amount = floatval($data['order_amount']);

    // Fetch the coupon details
    $query = "SELECT * FROM discount_coupons WHERE code = :coupon_code AND valid_until >= NOW() AND valid_from <= NOW()";
    $stmt = $conn->prepare($query);
    $stmt->execute([':coupon_code' => $coupon_code]);

    if ($stmt->rowCount() > 0) {
        $coupon = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if the coupon has been used up
        if ($coupon['used_count'] >= $coupon['usage_limit']) {
            $response['message'] = "Coupon usage limit has been reached.";
            echo json_encode($response);
            exit();
        }

        // Check if order meets minimum amount required
        if ($order_amount < $coupon['min_order_amount']) {
            $response['message'] = "Minimum order amount required: Rs. " . number_format($coupon['min_order_amount'], 2);
            echo json_encode($response);
            exit();
        }

        // Calculate discount
        $discount = 0;
        if ($coupon['discount_type'] == 'fixed') {
            $discount = $coupon['discount_value'];
        } elseif ($coupon['discount_type'] == 'percentage') {
            $discount = ($order_amount * $coupon['discount_value']) / 100;
            if ($discount > $coupon['max_discount']) {
                $discount = $coupon['max_discount'];
            }
        }

        // Ensure discount does not exceed the total order amount
        if ($discount > $order_amount) {
            $discount = $order_amount;
        }

        // Update used count
        $updateQuery = "UPDATE discount_coupons SET used_count = used_count + 1 WHERE id = :coupon_id";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->execute([':coupon_id' => $coupon['id']]);

        // Return success response with calculated discount
        $response = [
            'success' => true,
            'message' => "Coupon applied successfully!",
            'discount' => $discount,
            'total' => $order_amount - $discount
        ];
    } else {
        $response['message'] = "Invalid or expired coupon.";
    }
} catch (PDOException $e) {
    $response['message'] = "Database error: " . $e->getMessage();
}

echo json_encode($response);
