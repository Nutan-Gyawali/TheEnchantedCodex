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
    $catStmt = $conn->prepare("SELECT * FROM categories");
    $catStmt->execute();
    $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);


    $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : '';
    $categoryFilter = isset($_GET['category']) ? intval($_GET['category']) : null;
    $sortOption = isset($_GET['sort']) ? $_GET['sort'] : null;

    $sql = "SELECT p.*, c.category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE 1 ";

    $params = [];

    if (!empty($searchTerm)) {
        $sql .= "AND (p.name LIKE :search OR p.description LIKE :search) ";
        $params[':search'] = '%' . $searchTerm . '%';
    }

    if ($categoryFilter) {
        $categoryIds = getAllChildCategoryIds($categories, $categoryFilter);
        $placeholders = implode(',', array_fill(0, count($categoryIds), '?'));
        $sql .= "AND p.category_id IN ($placeholders) ";
        $params = array_merge($params, $categoryIds);
    }



    if ($sortOption === 'price_asc') {
        $sql .= "ORDER BY p.price ASC";
    } elseif ($sortOption === 'price_desc') {
        $sql .= "ORDER BY p.price DESC";
    } else {
        $sql .= "ORDER BY p.name";
    }

    $stmt = $conn->prepare($sql);

    $stmt->execute($params);

    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Connection failed: " . $e->getMessage();
}

// Function to build category tree
function buildCategoryTree($categories, $parent_id = 0)
{
    $branch = [];
    foreach ($categories as $category) {
        if ($category['parent_id'] == $parent_id) {
            $children = buildCategoryTree($categories, $category['id']);
            if ($children) {
                $category['children'] = $children;
            }
            $branch[] = $category;
        }
    }
    return $branch;
}
function getAllChildCategoryIds($categories, $parentId)
{
    $ids = [$parentId];
    foreach ($categories as $category) {
        if ($category['parent_id'] == $parentId) {
            $ids = array_merge($ids, getAllChildCategoryIds($categories, $category['id']));
        }
    }
    return $ids;
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
    <link href="sidebar.css" rel="stylesheet">
    <style>
        .category-list ul {
            list-style-type: none;
            padding-left: 0;
        }

        .category-list li {
            margin: 4px 0;
        }

        .category-list a {
            text-decoration: none;
            padding: 5px 10px;
            display: block;
            color: #333;
        }

        .category-list a:hover {
            background-color: rgba(233, 140, 20, 0.1);
            color: rgb(233, 140, 20);
        }

        .category-list .nested {
            padding-left: 20px;
        }

        /* Global Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: #fff9f2;
            color: #333;
            line-height: 1.6;
        }

        .container {
            display: flex;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 15px;
        }

        /* Top Navigation */
        .top-nav {
            background-color: #ff8c00;
            color: white;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .logo-container {
            display: flex;
            align-items: center;
        }

        .logo {
            width: 40px;
            height: 40px;
            margin-right: 10px;
        }

        .logo img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .nav-title {
            font-size: 1.5rem;
            font-weight: 500;
        }

        .nav-buttons {
            display: flex;
            gap: 15px;
        }

        .nav-button {
            background-color: #fff;
            color: #ff8c00;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            cursor: pointer;
            font-weight: 500;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .nav-button:hover {
            background-color: #ff7b00;
            color: #fff;
        }

        .nav-button i {
            font-size: 1.1rem;
        }

        /* Sidebar */
        .sidebar {
            width: 250px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-right: 20px;
            margin-top: 20px;
            height: fit-content;
        }

        .collapse-btn {
            width: 100%;
            background-color: #ff8c00;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            text-align: left;
            cursor: pointer;
            font-weight: 500;
            margin-bottom: 15px;
            transition: background-color 0.3s;
        }

        .collapse-btn:hover {
            background-color: #ff7b00;
        }

        .category-list ul {
            list-style-type: none;
            padding-left: 0;
        }

        .category-list li {
            margin: 8px 0;
        }

        .category-list a {
            text-decoration: none;
            padding: 8px 10px;
            display: block;
            color: #555;
            border-radius: 4px;
            transition: all 0.2s ease;
            font-weight: 400;
        }

        .category-list a:hover {
            background-color: rgba(255, 140, 0, 0.15);
            color: #ff8c00;
            font-weight: 500;
        }

        .category-list .nested {
            padding-left: 20px;
        }

        /* Main Content */
        .main-content {
            flex: 1;
            padding: 20px 0;
        }

        /* Search and Sort */
        .search-sort-container {
            margin-bottom: 25px;
        }

        .search-form {
            display: flex;
            align-items: center;
            gap: 10px;
            background-color: #fff;
            padding: 12px 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .search-form input[type="text"] {
            flex: 1;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .search-form input[type="text"]:focus {
            border-color: #ff8c00;
            outline: none;
        }

        .search-form select {
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: white;
            font-size: 0.9rem;
            cursor: pointer;
        }

        .search-form button {
            background-color: #ff8c00;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px 15px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .search-form button:hover {
            background-color: #ff7b00;
        }

        .clear-search {
            color: #ff8c00;
            text-decoration: none;
            font-size: 0.9rem;
            margin-left: 10px;
        }

        .clear-search:hover {
            text-decoration: underline;
        }

        /* Product Grid */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 25px;
        }

        .product-card {
            background-color: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }

        .product-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .product-card h3 {
            color: #333;
            font-size: 1.2rem;
            margin: 15px 15px 10px;
            height: 2.4em;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .product-card p {
            color: #666;
            margin: 8px 15px;
            font-size: 0.95rem;
        }

        .product-card p:nth-of-type(2) {
            color: #ff8c00;
            font-weight: 600;
            font-size: 1.1rem;
        }

        .out-of-stock {
            color: #ff3b30 !important;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            justify-content: space-around;
            padding: 15px;
            border-top: 1px solid #f0f0f0;
            margin-top: 10px;
        }

        .action-buttons button {
            padding: 10px;
            width: 45%;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .action-buttons button:hover {
            transform: scale(1.1);
        }

        .action-buttons button i {
            font-size: 1.2rem;
            color: #ff8c00;
        }

        .action-buttons button i:hover {
            color: #ff7b00;
        }

        /* Alerts */
        .alert {
            padding: 12px 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-weight: 500;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        /* Responsive Adjustments */
        @media screen and (max-width: 992px) {
            .container {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                margin-right: 0;
                margin-bottom: 20px;
            }

            .top-nav {
                flex-direction: column;
                gap: 10px;
                padding: 15px;
            }

            .nav-buttons {
                width: 100%;
                justify-content: center;
            }

            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            }
        }

        @media screen and (max-width: 576px) {
            .search-form {
                flex-direction: column;
                align-items: stretch;
            }

            .product-grid {
                grid-template-columns: 1fr;
            }

            .nav-title {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<script>
    function toggleSidebar() {
        const list = document.getElementById('category-list');
        list.style.display = (list.style.display === 'none' || list.style.display === '') ? 'block' : 'none';
    }
</script>

<body>
    <nav class="top-nav">
        <div class="logo-container">
            <div class="logo">
                <a href="index.php">
                    <img src="logo.png" alt="EC Logo">
                </a>
            </div>
            <h1>The Enchanted Codex</h1>
        </div>
        <a href="explore.php" class="nav-title">
            <h1>All Products</h1>
        </a>

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
    <div class="container">
        <!-- Sidebar for categories -->
        <div class="sidebar">
            <button class="collapse-btn" onclick="toggleSidebar()">☰ Categories</button>
            <div id="category-list" class="category-list">
                <?php
                $catStmt = $conn->prepare("SELECT * FROM categories ORDER BY category_name");
                $catStmt->execute();
                $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
                $categoryTree = buildCategoryTree($categories);

                function renderCategoryTree($tree)
                {
                    echo "<ul>";
                    foreach ($tree as $cat) {
                        echo "<li><a href='?category={$cat['id']}'>" . htmlspecialchars($cat['category_name']) . "</a>";
                        if (isset($cat['children'])) {
                            echo "<div class='nested'>";
                            renderCategoryTree($cat['children']);
                            echo "</div>";
                        }
                        echo "</li>";
                    }
                    echo "</ul>";
                }

                renderCategoryTree($categoryTree);
                ?>
            </div>
        </div>

        <!-- Main content -->
        <div class="main-content">
            <!-- Search + Sort -->
            <div class="search-sort-container">
                <form method="GET" action="" class="search-form">
                    <input type="text" name="search" placeholder="Search for products..."
                        value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
                    <select name="sort" onchange="this.form.submit()">
                        <option value="">Sort by</option>
                        <option value="price_asc" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_desc" <?php echo (isset($_GET['sort']) && $_GET['sort'] === 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                    </select>
                    <button type="submit"><i class="fa fa-search"></i></button>
                    <?php if (!empty($searchTerm)): ?>
                        <a href="index.php" class="clear-search">Clear Search</a>
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
        </div>
    </div>
</body>

</html>
<?php $conn = null; ?>