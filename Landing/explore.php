<?php
session_start();
$products = [];
$error = '';

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';

    if (!empty($searchTerm)) {
        $sql = "SELECT p.*, c.category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.name LIKE :search OR p.description LIKE :search 
                ORDER BY p.name";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':search', '%' . $searchTerm . '%', PDO::PARAM_STR);
    } else {
        $sql = "SELECT p.*, c.category_name 
                FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id 
                ORDER BY p.name";
        $stmt = $conn->prepare($sql);
    }

    $stmt->execute();
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
    <title>Product Listing</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="logo.css" rel="stylesheet">
    <link href="explore.css" rel="stylesheet">
</head>

<body>
    <nav class="top-nav">
        <div class="logo-container">
            <div class="logo">
                <img src="logo.png" alt="EC Logo">
            </div>
            <h1>The Enchanted Codex</h1>
        </div>
        <h1 class="nav-title">All Products</h1>
        <div class="nav-buttons">
            <button class="nav-button" onclick="location.href='../wishlist/index.php'">
                <i class="fa fa-heart"></i>
                Wishlist
            </button>
            <button class="nav-button" onclick="location.href='../shoppingcart/viewcart.php'">
                <i class="fa fa-shopping-cart"></i>
                Cart
            </button>
        </div>
    </nav>

    <!-- Search Bar -->
    <div class="search-container" style="text-align: center; margin: 20px;">
        <form method="GET" action="">
            <input type="text" name="search" placeholder="Search for products..."
                value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>"
                style="padding: 8px; width: 300px; border-radius: 5px; border: 1px solid #ccc;">
            <button type="submit" style="padding: 8px 12px; border: none; background-color:rgb(231, 120, 8); color: white; border-radius: 5px; cursor: pointer;">
                <i class="fa fa-search"></i> Search
            </button>
            <?php if (!empty($searchTerm)): ?>
                <a href="index.php" style="margin-left: 10px; text-decoration: none; color: red;">Clear Search</a>
            <?php endif; ?>
        </form>
    </div>

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
                    <p>Category: <?php echo htmlspecialchars($product['category_name']); ?></p>
                    <p>Price: NRs.<?php echo number_format($product['price'], 2); ?></p>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>

                    <p class="<?php echo $product['quantity'] < 1 ? 'out-of-stock' : ''; ?>">
                        <?php if ($product['quantity'] < 1): ?>
                            Out of Stock
                        <?php else: ?>
                            In Stock: <?php echo $product['quantity']; ?> units
                        <?php endif; ?>
                    </p>

                    <div class="action-buttons">
                        <form method="POST" action="../shoppingcart/addtocart.php" style="display: inline;">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button name="cart" type="submit" style="background: none; border: none; cursor: pointer;">
                                <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                            </button>
                        </form>

                        <form method="POST" action="../wishlist/addtowishlist.php" style="display: inline;">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button name="wishlist" type="submit" style="background: none; border: none; cursor: pointer;">
                                <i class="fa fa-heart" aria-hidden="true"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align:center;">No products found.</p>
        <?php endif; ?>
    </div>
</body>

</html>
<?php $conn = null; ?>