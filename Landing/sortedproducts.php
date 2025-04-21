<?php
session_start();
$products = [];
$error = '';
$categories = [];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch all categories
    $catStmt = $conn->query("SELECT id, category_name FROM categories ORDER BY category_name");
    $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);

    // Get filters
    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
    $categoryFilter = isset($_GET['category']) ? trim($_GET['category']) : '';
    $sort = isset($_GET['sort']) ? trim($_GET['sort']) : '';

    $sql = "SELECT p.*, c.category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            WHERE 1";

    if (!empty($searchTerm)) {
        $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
    }
    if (!empty($categoryFilter)) {
        $sql .= " AND p.category_id = :category";
    }

    // Sorting logic
    if ($sort === "low") {
        $sql .= " ORDER BY p.price ASC";
    } elseif ($sort === "high") {
        $sql .= " ORDER BY p.price DESC";
    } else {
        $sql .= " ORDER BY p.name";
    }

    $stmt = $conn->prepare($sql);

    if (!empty($searchTerm)) {
        $stmt->bindValue(':search', '%' . $searchTerm . '%', PDO::PARAM_STR);
    }
    if (!empty($categoryFilter)) {
        $stmt->bindValue(':category', $categoryFilter, PDO::PARAM_INT);
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
    <title>Sorted Products</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="logo.css" rel="stylesheet">
    <link href="explore.css" rel="stylesheet">
</head>

<body>
    <nav class="top-nav">
        <div class="logo-container">
            <div class="logo"><img src="logo.png" alt="EC Logo"></div>
            <h1>The Enchanted Codex</h1>
        </div>
        <h1 class="nav-title">Sorted Products</h1>
        <div class="nav-buttons">
            <button onclick="location.href='../wishlist/index.php'" class="nav-button">
                <i class="fa fa-heart"></i> Wishlist
            </button>
            <button onclick="location.href='../shoppingcart/viewcart.php'" class="nav-button">
                <i class="fa fa-shopping-cart"></i> Cart
            </button>
        </div>
    </nav>

    <div class="search-container" style="text-align:center; margin:20px;">
        <form method="GET" action="sortedproducts.php">
            <input type="text" name="search" placeholder="Search..."
                value="<?php echo htmlspecialchars($searchTerm); ?>"
                style="padding:8px; width:220px; border-radius:5px; border:1px solid #ccc;">

            <select name="category" style="padding:8px; border-radius:5px;">
                <option value="">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>"
                        <?php echo ($categoryFilter == $cat['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['category_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <select name="sort" style="padding:8px; border-radius:5px;">
                <option value="">Sort By</option>
                <option value="low" <?php echo ($sort == 'low') ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="high" <?php echo ($sort == 'high') ? 'selected' : ''; ?>>Price: High to Low</option>
            </select>

            <button type="submit"
                style="padding:8px 12px; background:orange; color:white; border:none; border-radius:5px;">
                <i class="fa fa-search"></i> Go
            </button>

            <?php if ($searchTerm || $categoryFilter || $sort): ?>
                <a href="sortedproducts.php" style="margin-left:10px; color:red;">Clear Filters</a>
            <?php endif; ?>
        </form>
    </div>

    <?php if ($error): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <div class="product-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <?php if ($product['image_path']): ?>
                        <img src="<?php echo htmlspecialchars('../product/uploads/' . basename($product['image_path'])); ?>"
                            alt="<?php echo htmlspecialchars($product['name']); ?>" class="product-image">
                    <?php endif; ?>

                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p>Category: <?php echo htmlspecialchars($product['category_name']); ?></p>
                    <p>Price: NRs.<?php echo number_format($product['price'], 2); ?></p>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>

                    <p class="<?php echo $product['quantity'] < 1 ? 'out-of-stock' : ''; ?>">
                        <?php echo $product['quantity'] < 1 ? 'Out of Stock' : "In Stock: {$product['quantity']} units"; ?>
                    </p>

                    <div class="action-buttons">
                        <form method="POST" action="../shoppingcart/addtocart.php" style="display: inline;">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" style="background: none; border: none; cursor: pointer;">
                                <i class="fa fa-shopping-cart"></i>
                            </button>
                        </form>
                        <form method="POST" action="../wishlist/addtowishlist.php" style="display: inline;">
                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                            <button type="submit" style="background: none; border: none; cursor: pointer;">
                                <i class="fa fa-heart"></i>
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