<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $pidx = $_GET['pidx'];
    $status = $_GET['status'];
    $purchase_order_id = $_GET['purchase_order_id'];
    $transaction_id = $_GET['transaction_id'] ?? null;

    // Verify payment via lookup API
    $curl = curl_init();
    curl_setopt_array($curl, [
        CURLOPT_URL => 'https://dev.khalti.com/api/v2/epayment/lookup/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode(['pidx' => $pidx]),
        CURLOPT_HTTPHEADER => [
            'Authorization: key 6b780237318a499cb73322f31bbccd6c',
            'Content-Type: application/json',
        ],
    ]);

    $response = curl_exec($curl);
    $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    curl_close($curl);

    $response_data = json_decode($response, true);

    if ($http_status == 200 && $response_data['status'] == 'Completed') {
        // Update order status and transaction ID
        $stmt = $conn->prepare("UPDATE orders SET order_status = 'Paid', transaction_id = ?, payment_method = 'Khalti' WHERE id = ?");
        $stmt->execute([$transaction_id, $purchase_order_id]);

        header('Location: order_success.php');
    } else {
        // Update order status to failed
        $stmt = $conn->prepare("UPDATE orders SET order_status = 'Pending' WHERE id = ?");
        $stmt->execute([$purchase_order_id]);
        header('Location: payment_failed.php');
    }
    exit();
} catch (PDOException $e) {
    die("Database error: " . $e->getMessage());
}
