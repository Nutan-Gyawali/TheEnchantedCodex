<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// your DB connection file

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "Please log in to view your orders.";
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user's orders
$query = "SELECT * FROM orders WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "<h2>Your Orders</h2>";
    echo "<table border='1' cellpadding='10'>";
    echo "<tr>
            <th>Order ID</th>
            <th>Total Amount</th>
            <th>Payment Method</th>
            <th>Shipping Address</th>
            <th>Order Status</th>
            <th>Created At</th>
            
          </tr>";

    while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$row['id']}</td>
                <td>{$row['total_amount']}</td>
                <td>{$row['payment_method']}</td>
                <td>{$row['shipping_address']}</td>
                <td>{$row['order_status']}</td>
                <td>{$row['created_at']}</td>
                
              </tr>";
    }

    echo "</table>";
} else {
    echo "You have no orders.";
}

$conn->close();
