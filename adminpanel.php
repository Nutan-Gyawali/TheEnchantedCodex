<?php
// Optional: Restrict this page to admin users only
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.html');
    exit();
}

// Add admin-specific functionality here
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            background-color: #fff3e0;
            color: #d35400;
            margin: 50px;
        }

        h1 {
            color: #e67e22;
        }

        button {
            background-color: #e67e22;
            color: white;
            border: none;
            padding: 10px 20px;
            margin: 10px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            transition: background 0.3s ease;
        }

        button:hover {
            background-color: #d35400;
        }
    </style>
</head>

<body>
    <h1>Admin Panel</h1>
    <button onclick="location.href='category/category.php'">Category</button>
    <button onclick="location.href='product/product.php'">Product</button>
</body>

</html>