<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "ecommerce";

// Handle product deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product']) && isset($_POST['product_id'])) {
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $product_id = $_POST['product_id'];

        // Prepare and execute delete statement
        $sql = "DELETE FROM products WHERE id = :product_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();

        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    } catch (PDOException $e) {
        // Return error response
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}

// Fetch products
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT p.*, c.category_name 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id 
            ORDER BY p.name";

    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo '<div class="error-message">Connection failed: ' . htmlspecialchars($e->getMessage()) . '</div>';
    exit;
}

$conn = null;
?>

<head>
    <link rel="stylesheet" href="../product/viewprod.css">
</head>
<div class="products-container">
    <h1>All Products</h1>
    <div class="product-controls">
        <button id="addProduct" class="action-button">Add Product</button>
        <style>

        </style>
    </div>

    <div class="product-grid">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="product-card" data-product-id="<?php echo $product['id']; ?>">
                    <?php if ($product['image_path']): ?>
                        <img src="<?php echo htmlspecialchars('../product/uploads/' . basename($product['image_path'])); ?>"
                            alt="<?php echo htmlspecialchars($product['name']); ?>"
                            class="product-image">
                    <?php endif; ?>

                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p>Category: <?php echo htmlspecialchars($product['category_name']); ?></p>
                    <p>Price: NRs.<?php echo number_format($product['price'], 2); ?></p>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>

                    <p class="stock-status <?php echo $product['quantity'] < 1 ? 'out-of-stock' : 'in-stock'; ?>">
                        <?php if ($product['quantity'] < 1): ?>
                            Out of Stock
                        <?php else: ?>
                            In Stock: <?php echo $product['quantity']; ?> units
                        <?php endif; ?>
                    </p>

                    <div class="action-buttons">
                        <button class="edit-btn" onclick="editProduct(<?php echo $product['id']; ?>)">Edit</button>
                        <button class="delete-btn" onclick="deleteProduct(<?php echo $product['id']; ?>)">Delete</button>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="no-products-message">No products available.</div>
        <?php endif; ?>
    </div>
</div>

<script>
    function deleteProduct(id) {
        if (confirm('Are you sure you want to delete this product?')) {
            fetch('../product/viewProd.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `delete_product=1&product_id=${id}`
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const productCard = document.querySelector(`[data-product-id="${id}"]`);
                        if (productCard) {
                            productCard.remove();
                        }
                        alert('Product deleted successfully');
                    } else {
                        throw new Error(data.error || 'Error deleting product');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting product: ' + error.message);
                });
        }
    }
</script>