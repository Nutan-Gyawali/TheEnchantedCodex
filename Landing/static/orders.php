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
?>

<!DOCTYPE html>
<html>

<head>
    <title>Your Orders</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff8f0;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .header {
            background-color: orange;
            padding: 15px 30px;
            display: flex;
            align-items: center;
            color: white;
        }

        .header img {
            height: 40px;
            margin-right: 15px;
        }

        .container {
            padding: 30px;
        }

        h2 {
            color: #e67e22;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
        }

        th,
        td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #f1f1f1;
        }

        th {
            background-color: #ffa726;
            color: white;
        }

        tr:hover {
            background-color: #fff3e0;
        }
    </style>
</head>

<body>

    <div class="header">
        <a href="../explore.php">
            <img src="../logo.png" alt="Logo">
        </a>
        <h1>Ecommerce Orders</h1>
    </div>


    <div class="container">
        <?php
        if ($result->num_rows > 0) {
            echo "<h2>Your Orders</h2>";
            echo "<table>";
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
            echo "<p>You have no orders.</p>";
        }

        $conn->close();
        ?>
    </div>

</body>

</html>