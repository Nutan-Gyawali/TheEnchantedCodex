<?php
session_start();
$error = ''; // Initialize the error variable
$products = []; // Initialize the products array

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Debug: Print session data
    echo "Debug: Session data:<br>";
    var_dump($_SESSION);

    $_SESSION['error_message'] = "Please log in to view your wishlist.";
    header("Location: ../login/login.html");
    exit();
}

// Rest of your code remains the same

// If we reach here, user is logged in
// Debug: Print userid
//echo "Logged in user ID: " . $_SESSION['user_id'];
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get wishlist items with product details
    $sql = "SELECT p.*, c.category_name, w.id as wishlist_id 
            FROM wishlist w 
            JOIN products p ON w.product_id = p.id 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE w.user_id = :user_id 
            ORDER BY p.name";

    $stmt = $conn->prepare($sql);
    $stmt->execute([':user_id' => $_SESSION['user_id']]);  // Changed :userid to :user_id
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Connection failed: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Wishlist</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="wishlist.css">
    <link rel="stylesheet" href="../Landing/logo.css">

    <style>

    </style>
</head>

<body>

    <nav class=" top-nav">
        <div class="logo-container">
            <div class="logo">
                <img src="../Landing/logo.png" alt="EC Logo">
            </div>
            <h1>The Enchanted Codex</h1>
        </div>
        <h1 class="nav-title">My Wishlist</h1>
        <div class="nav-buttons">
            <button class="nav-button" onclick="location.href='../Landing/explore.php'">
                <i class="fa fa-home"></i>
                Products
            </button>
            <button class="nav-button" onclick="location.href='../shoppingcart/viewcart.php'">
                <i class="fa fa-shopping-cart"></i>
                Cart
            </button>
        </div>
    </nav>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert success"><?php echo htmlspecialchars($_SESSION['success_message']); ?></div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert error"><?php echo htmlspecialchars($_SESSION['error_message']); ?></div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="product-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <?php if ($product['image_path']): ?>
                        <img src="<?php echo htmlspecialchars('../product/uploads/' . basename($product['image_path'])); ?>"
                            alt="<?php echo htmlspecialchars($product['name']); ?>"
                            class="product-image">
                    <?php endif; ?>

                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <!--<p>Category: <?php echo htmlspecialchars($product['category_name']); ?></p> -->
                    <p>Price: NRs.<?php echo number_format($product['price'], 2); ?></p>
                    <!-- <p><?php echo htmlspecialchars($product['description']); ?></p> -->

                    <p class="<?php echo $product['quantity'] < 1 ? 'out-of-stock' : ''; ?>">
                        <?php if ($product['quantity'] < 1): ?>
                            Out of Stock
                        <?php else: ?>
                            In Stock: <?php echo $product['quantity']; ?> units
                        <?php endif; ?>
                    </p>

                    <div class="action-buttons">
                        <!-- Add to Cart Button -->
                        <form method="POST" action="../shoppingcart/addtocart.php" style="display: inline;">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" style="background: none; border: none; cursor: pointer;">
                                <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                            </button>
                        </form>

                        <!-- Remove from Wishlist Button -->
                        <form method="POST" action="removefromwishlist.php" style="display: inline;">
                            <input type="hidden" name="wishlist_id" value="<?php echo $product['wishlist_id']; ?>">
                            <button type="submit" class="remove-button">
                                <i class="fa fa-trash" aria-hidden="true"></i> Remove
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-wishlist">
                <p>Your wishlist is empty.</p>
                <button class="nav-button" onclick="location.href='../userside/viewproduct.php'">
                    Continue Shopping
                </button>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>
<?php $conn = null; ?>