<?php
session_start();
require '../db_connection.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../AdminPanel/AdminLogin.html");
    exit();
}

// Handle the form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $code = strtoupper(trim($_POST['code']));
    $discount_type = $_POST['discount_type'];
    $discount_value = (float) $_POST['discount_value'];
    $min_order_amount = !empty($_POST['min_order_amount']) ? (float) $_POST['min_order_amount'] : null;
    $max_discount = !empty($_POST['max_discount']) ? (float) $_POST['max_discount'] : null;
    $usage_limit = !empty($_POST['usage_limit']) ? (int) $_POST['usage_limit'] : null;
    $valid_from = !empty($_POST['valid_from']) ? $_POST['valid_from'] : null;
    $valid_until = !empty($_POST['valid_until']) ? $_POST['valid_until'] : null;
    $status = $_POST['status'];

    // Check if the coupon should be marked as expired
    if (!empty($valid_until) && strtotime($valid_until) < time()) {
        $status = 'expired'; // If expiry date is in the past, set status to expired
    }

    // Update the coupon
    $update_sql = "UPDATE discount_coupons SET 
                   code = ?, discount_type = ?, discount_value = ?, min_order_amount = ?, 
                   max_discount = ?, usage_limit = ?, valid_from = ?, valid_until = ?, status = ?
                   WHERE id = ?";

    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param(
        "ssddddsssi",
        $code,
        $discount_type,
        $discount_value,
        $min_order_amount,
        $max_discount,
        $usage_limit,
        $valid_from,
        $valid_until,
        $status,
        $id
    );

    if ($stmt->execute()) {
        $_SESSION['message'] = "Coupon updated successfully!";
        $_SESSION['message_type'] = 'success';
    } else {
        $_SESSION['message'] = "Error updating coupon: " . $stmt->error;
        $_SESSION['message_type'] = 'error';
    }

    header("Location: viewcoupon.php");
    exit();
}
