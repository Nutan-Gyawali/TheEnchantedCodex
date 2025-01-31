<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Discount Coupon</title>
    <link rel="stylesheet" href="discount.css">
</head>

<body>
    <div class="container">
        <h2>Add Discount Coupon</h2>

        <?php
        session_start();
        if (isset($_SESSION['message'])):
        ?>
            <div class="message <?= $_SESSION['message_type'] ?>">
                <?= htmlspecialchars($_SESSION['message']) ?>
            </div>
            <?php
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            ?>
        <?php endif; ?>

        <form class="coupon-form" method="POST" action="add_coupon.php">
            <div class="form-group">
                <label>Coupon Code:</label>
                <input type="text" name="code" required>
            </div>

            <div class="form-group">
                <label>Discount Type:</label>
                <select name="discount_type" required>
                    <option value="fixed">Fixed</option>
                    <option value="percentage">Percentage</option>
                </select>
            </div>

            <div class="form-group">
                <label>Discount Value:</label>
                <input type="number" step="0.01" name="discount_value" required>
            </div>

            <div class="form-group">
                <label>Minimum Order Amount:</label>
                <input type="number" step="0.01" name="min_order_amount">
            </div>

            <div class="form-group">
                <label>Maximum Discount (for %):</label>
                <input type="number" step="0.01" name="max_discount">
            </div>

            <div class="form-group">
                <label>Usage Limit:</label>
                <input type="number" name="usage_limit">
            </div>

            <div class="form-group">
                <label>Valid From:</label>
                <input type="datetime-local" name="valid_from">
            </div>

            <div class="form-group">
                <label>Valid Until:</label>
                <input type="datetime-local" name="valid_until">
            </div>

            <div class="form-group">
                <label>Status:</label>
                <select name="status">
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>
            </div>

            <button type="submit">Add Coupon</button>
        </form>
    </div>
</body>

</html>