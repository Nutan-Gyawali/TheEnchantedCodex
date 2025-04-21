<?php
session_start();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Redirect to login page if not logged in or not an admin
    header("Location: ../login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['order_status'])) {
    $order_id = $_POST['order_id'];
    $order_status = $_POST['order_status'];

    // Debug information
    error_log("Updating order ID: $order_id to status: $order_status");

    // Start transaction
    $conn->beginTransaction();

    try {
        $update_sql = "UPDATE orders SET order_status = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_result = $update_stmt->execute([$order_status, $order_id]);

        if ($update_stmt->rowCount() > 0) {
            $conn->commit();
            $_SESSION['success_message'] = "Order status updated successfully.";
        } else {
            // Check if the order exists
            $check_sql = "SELECT id FROM orders WHERE id = ?";
            $check_stmt = $conn->prepare($check_sql);
            $check_stmt->execute([$order_id]);

            if ($check_stmt->rowCount() > 0) {
                // Order exists but no update occurred (status might be the same)
                $conn->commit();
                $_SESSION['info_message'] = "No changes made. Order status was already set to '$order_status'.";
            } else {
                $conn->rollBack();
                $_SESSION['error_message'] = "Order ID not found.";
            }
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        $_SESSION['error_message'] = "Error: " . $e->getMessage();
    }

    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch all orders
$order_sql = "SELECT o.id, o.user_id, o.total_amount, o.payment_method, o.shipping_address, o.order_status, o.created_at, u.username 
               FROM orders o 
               INNER JOIN users u ON o.user_id = u.id 
               ORDER BY o.created_at DESC";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->execute();
$orders = $order_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Orders | The Enchanted Codex</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="display.css">
    <style>
        /* Add custom styles for highlighting changes */
        .status-changed {
            background-color: rgba(0, 255, 0, 0.1);
            transition: background-color 2s;
        }
    </style>
</head>

<body>
    <!-- Main Content -->
    <div class="container main-content">
        <div class="dashboard-header">
            <h2>Order Management</h2>
            <p>View and manage all customer orders</p>
        </div>

        <!-- Alert Messages -->
        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php
                echo $_SESSION['success_message'];
                unset($_SESSION['success_message']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['info_message'])): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <?php
                echo $_SESSION['info_message'];
                unset($_SESSION['info_message']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php
                echo $_SESSION['error_message'];
                unset($_SESSION['error_message']);
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card orders-card">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>User</th>
                                <th>Total Amount</th>
                                <th>Payment Method</th>
                                <th>Shipping Address</th>
                                <th>Order Status</th>
                                <th>Created At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $order) { ?>
                                <button class="btn btn-sm mt-2" style="background-color: orange; color: white; padding: 8px 16px; border: none; border-radius: 4px;" onclick="location.href='../AdminPanel/index.php'">Go Home</button>

                                <tr>
                                    <button class="btn btn-sm mt-2" style="background-color: orange; color: white; padding: 8px 16px; border: none; border-radius: 4px;" onclick="location.href='../orders/orderdisplay.php'">
                                        Go to Update Page
                                    </button>
                                </tr>

                                <tr id="order-row-<?php echo htmlspecialchars($order['id']); ?>">
                                    <td>#<?php echo htmlspecialchars($order['id']); ?></td>
                                    <td><?php echo htmlspecialchars($order['username']); ?></td>
                                    <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                                    <td><?php echo htmlspecialchars($order['shipping_address']); ?></td>
                                    <td>
                                        <form method="POST" action="" class="status-form">
                                            <input type="hidden" name="order_id" value="<?php echo htmlspecialchars($order['id']); ?>">
                                            <select name="order_status" class="form-select status-select">
                                                <option value="Pending" <?php if ($order['order_status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                                                <option value="Processing" <?php if ($order['order_status'] == 'Processing') echo 'selected'; ?>>Processing</option>
                                                <option value="Shipped" <?php if ($order['order_status'] == 'Shipped') echo 'selected'; ?>>Shipped</option>
                                                <option value="Delivered" <?php if ($order['order_status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                                                <option value="Cancelled" <?php if ($order['order_status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm mt-2" style="background-color: orange; color: white; border: none;">
                                                Update
                                            </button>


                                        </form>


                                    </td>
                                    <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                                    <td>
                                        <a href="../orders/order_details.php?order_id=<?php echo htmlspecialchars($order['id']); ?>" class="btn btn-view">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Highlight the row if status was just updated
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($_POST['order_id']) && !isset($_SESSION['error_message'])): ?>
                const orderRow = document.getElementById('order-row-<?php echo htmlspecialchars($_POST['order_id']); ?>');
                if (orderRow) {
                    orderRow.classList.add('status-changed');
                    setTimeout(() => {
                        orderRow.classList.remove('status-changed');
                    }, 3000);
                }
            <?php endif; ?>

            // Add confirmation before submitting status change
            const statusForms = document.querySelectorAll('.status-form');
            statusForms.forEach(form => {
                form.addEventListener('submit', function(e) {
                    const selectedStatus = this.querySelector('.status-select').value;
                    const orderId = this.querySelector('input[name="order_id"]').value;
                    if (!confirm(`Are you sure you want to update Order #${orderId} status to "${selectedStatus}"?`)) {
                        e.preventDefault();
                    }
                });
            });
        });
    </script>
</body>

</html>