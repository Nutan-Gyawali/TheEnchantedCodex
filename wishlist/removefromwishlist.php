<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['error_message'] = "Please log in to manage your wishlist.";
    header("Location: ../login/login.php");
    exit();
}

if (!isset($_POST['wishlist_id'])) {
    $_SESSION['error_message'] = "Invalid request.";
    header("Location: index.php");
    exit();
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "DELETE FROM wishlist 
            WHERE id = :wishlist_id 
            AND user_id = :user_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':wishlist_id' => $_POST['wishlist_id'],
        ':user_id' => $_SESSION['user_id']
    ]);

    if ($stmt->rowCount() > 0) {
        $_SESSION['success_message'] = "Item removed from wishlist successfully.";
    } else {
        $_SESSION['error_message'] = "Unable to remove item from wishlist.";
    }
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Database error: " . $e->getMessage();
}

header("Location: index.php");
exit();
