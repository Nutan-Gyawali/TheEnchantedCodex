<?php
session_start();
require '../db_connection.php';

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../AdminPanel/AdminLogin.html");
    exit();
}

// Fetch coupons from the database
$query = "SELECT * FROM discount_coupons ORDER BY id DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Coupons</title>
    <link rel="stylesheet" href="coupon_styles.css">
</head>

<body>
    <div class="container">
        <h2>Discount Coupons</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message <?= $_SESSION['message_type'] ?>">
                <?= htmlspecialchars($_SESSION['message']) ?>
            </div>
            <?php
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
            ?>
        <?php endif; ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Code</th>
                    <th>Type</th>
                    <th>Value</th>
                    <th>Min Order</th>
                    <th>Max Discount</th>
                    <th>Usage Limit</th>
                    <th>Valid From</th>
                    <th>Valid Until</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['id']) ?></td>
                            <td><?= htmlspecialchars($row['code']) ?></td>
                            <td><?= htmlspecialchars(ucfirst($row['discount_type'])) ?></td>
                            <td><?= htmlspecialchars($row['discount_value']) ?></td>
                            <td><?= $row['min_order_amount'] !== null ? htmlspecialchars($row['min_order_amount']) : 'N/A' ?></td>
                            <td><?= $row['max_discount'] !== null ? htmlspecialchars($row['max_discount']) : 'N/A' ?></td>
                            <td><?= $row['usage_limit'] !== null ? htmlspecialchars($row['usage_limit']) : 'Unlimited' ?></td>
                            <td><?= $row['valid_from'] ? htmlspecialchars($row['valid_from']) : 'N/A' ?></td>
                            <td><?= $row['valid_until'] ? htmlspecialchars($row['valid_until']) : 'N/A' ?></td>
                            <td class="<?= $row['status'] == 'active' ? 'active' : 'inactive' ?>">
                                <?= htmlspecialchars(ucfirst($row['status'])) ?>
                            </td>
                            <td>
                                <a href="editcoupon.php?id=<?= $row['id'] ?>" class="edit-btn">Edit</a>
                            </td>
                        </tr>

                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="no-data">No coupons found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <a href="Discountfront.php" class="btn">Add New Coupon</a>
    </div>
</body>

</html>