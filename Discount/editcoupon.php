<?php
session_start();
require '../db_connection.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../AdminPanel/AdminLogin.html");
    exit();
}

// Check if an ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "Invalid coupon ID.";
    $_SESSION['message_type'] = "error";
    header("Location: viewcoupon.php");
    exit();
}

$coupon_id = $_GET['id'];

// Fetch coupon details
$query = "SELECT * FROM discount_coupons WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $coupon_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $_SESSION['message'] = "Coupon not found.";
    $_SESSION['message_type'] = "error";
    header("Location: viewcoupon.php");
    exit();
}

$coupon = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Coupon</title>
    <link rel="stylesheet" href="discount.css">
</head>

<body>
    <div class="container">
        <h2>Edit Coupon</h2>

        <form action="update_coupon.php" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($coupon['id']) ?>">

            <div class="form-group">
                <label>Coupon Code:</label>
                <input type="text" name="code" value="<?= htmlspecialchars($coupon['code']) ?>" required>
            </div>

            <div class="form-group">
                <label>Discount Type:</label>
                <select name="discount_type" required>
                    <option value="fixed" <?= $coupon['discount_type'] == 'fixed' ? 'selected' : '' ?>>Fixed</option>
                    <option value="percentage" <?= $coupon['discount_type'] == 'percentage' ? 'selected' : '' ?>>Percentage</option>
                </select>
            </div>

            <div class="form-group">
                <label>Discount Value:</label>
                <input type="number" step="0.01" name="discount_value" value="<?= htmlspecialchars($coupon['discount_value']) ?>" required>
            </div>

            <div class="form-group">
                <label>Minimum Order Amount:</label>
                <input type="number" step="0.01" name="min_order_amount" value="<?= htmlspecialchars($coupon['min_order_amount']) ?>">
            </div>

            <div class="form-group">
                <label>Maximum Discount (for %):</label>
                <input type="number" step="0.01" name="max_discount" value="<?= htmlspecialchars($coupon['max_discount']) ?>">
            </div>

            <div class="form-group">
                <label>Usage Limit:</label>
                <input type="number" name="usage_limit" value="<?= htmlspecialchars($coupon['usage_limit']) ?>">
            </div>

            <div class="form-group">
                <label>Valid From:</label>
                <input type="datetime-local" name="valid_from" value="<?= $coupon['valid_from'] ?>">
            </div>

            <div class="form-group">
                <label>Valid Until:</label>
                <input type="datetime-local" name="valid_until" value="<?= $coupon['valid_until'] ?>">
            </div>

            <div class="form-group">
                <label>Status:</label>
                <select name="status">
                    <option value="active" <?= $coupon['status'] == 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= $coupon['status'] == 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>

            <button type="submit">Update Coupon</button>
        </form>
    </div>
</body>

</html>