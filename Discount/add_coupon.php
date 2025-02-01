<?php
session_start();
require '../db_connection.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../AdminPanel/AdminLogin.html");
    exit();
}

$message = '';
$message_type = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $code = strtoupper(trim($_POST['code']));
    $discount_type = $_POST['discount_type'];
    $discount_value = (float) $_POST['discount_value'];
    $min_order_amount = !empty($_POST['min_order_amount']) ? (float) $_POST['min_order_amount'] : null;
    $max_discount = !empty($_POST['max_discount']) ? (float) $_POST['max_discount'] : null;
    $usage_limit = !empty($_POST['usage_limit']) ? (int) $_POST['usage_limit'] : null;
    $valid_from = !empty($_POST['valid_from']) ? $_POST['valid_from'] : null;
    $valid_until = !empty($_POST['valid_until']) ? $_POST['valid_until'] : null;
    $status = $_POST['status'];

    // Convert valid_until to a timestamp and check if it's expired
    if (!empty($valid_until) && strtotime($valid_until) < time()) {
        $status = 'expired'; // If expiry date is in the past, set status to expired
    }

    try {
        // Check for existing coupon code
        $check_stmt = $conn->prepare("SELECT code FROM discount_coupons WHERE code = ?");
        $check_stmt->bind_param("s", $code);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            throw new Exception("Coupon code already exists!");
        }

        // Insert new coupon
        $insert_sql = "INSERT INTO discount_coupons 
                      (code, discount_type, discount_value, min_order_amount, max_discount, usage_limit, valid_from, valid_until, status) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param(
            "ssddddsss",
            $code,
            $discount_type,
            $discount_value,
            $min_order_amount,
            $max_discount,
            $usage_limit,
            $valid_from,
            $valid_until,
            $status
        );

        if ($insert_stmt->execute()) {
            $_SESSION['message'] = "Coupon added successfully!";
            $_SESSION['message_type'] = 'success';

            // Prevent form resubmission
            header("Location: Discountfront.php");
            exit();
        } else {
            throw new Exception("Error adding coupon: " . $insert_stmt->error);
        }
    } catch (Exception $e) {
        $_SESSION['message'] = $e->getMessage();
        $_SESSION['message_type'] = 'error';

        // Redirect to clear POST data
        header("Location: Discountfront.php");
        exit();
    }
}
