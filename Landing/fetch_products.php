<?php
function fetchFilteredProducts($search = '', $category = '', $sort = '')
{
    $products = [];
    $error = '';
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "ecommerce";

    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "SELECT p.*, c.category_name FROM products p 
                LEFT JOIN categories c ON p.category_id = c.id WHERE 1";

        if (!empty($search)) {
            $sql .= " AND (p.name LIKE :search OR p.description LIKE :search)";
        }

        if (!empty($category)) {
            $sql .= " AND p.category_id = :category";
        }

        if ($sort == "low") {
            $sql .= " ORDER BY p.price ASC";
        } elseif ($sort == "high") {
            $sql .= " ORDER BY p.price DESC";
        } else {
            $sql .= " ORDER BY p.name";
        }

        $stmt = $conn->prepare($sql);

        if (!empty($search)) {
            $stmt->bindValue(':search', '%' . $search . '%');
        }

        if (!empty($category)) {
            $stmt->bindValue(':category', $category);
        }

        $stmt->execute();
        $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }

    if (!empty($error)) {
        echo "<p style='color: red; text-align: center;'>$error</p>";
    } elseif (empty($products)) {
        echo "<p style='text-align:center;'>No products found.</p>";
    } else {
        foreach ($products as $product): ?>
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
                    <form method="POST" action="../shoppingcart/addtocart.php" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <button type="submit" style="background: none; border: none; cursor: pointer;">
                            <i class="fa fa-shopping-cart"></i>
                        </button>
                    </form>
                    <form method="POST" action="../wishlist/addtowishlist.php" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                        <button type="submit" style="background: none; border: none; cursor: pointer;">
                            <i class="fa fa-heart"></i>
                        </button>
                    </form>
                </div>
            </div>
<?php
        endforeach;
    }

    $conn = null;
}
?>