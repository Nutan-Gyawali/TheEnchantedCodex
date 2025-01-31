<?php
session_start();
$host = 'localhost';
$dbname = 'ecommerce';
$user = 'root';
$password = '';
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle product submission
if (isset($_POST['add_product'])) {
    $category_id = $_POST['category_id'];
    $name = trim($_POST['name']);
    $price = floatval($_POST['price']);
    $description = trim($_POST['description']);
    $quantity = intval($_POST['quantity']);

    // Handle image upload
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = 'uploads/'; // Ensure you use the correct path to the uploads folder
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = array('jpg', 'jpeg', 'png', 'gif');

        if (in_array($file_extension, $allowed_extensions)) {
            $new_filename = uniqid() . '.' . $file_extension;
            $target_path = $upload_dir . $new_filename;

            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_path)) {
                $image_path = $target_path; // Save the relative path to the image
            } else {
                $_SESSION['error'] = "Failed to upload image.";
                header("Location: " . $_SERVER['PHP_SELF']);
                exit();
            }
        } else {
            $_SESSION['error'] = "Invalid file type. Allowed types: jpg, jpeg, png, gif";
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        }
    }

    // Insert product into database
    $stmt = $conn->prepare("INSERT INTO products (category_id, name, image_path, price, description, quantity) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issdsi", $category_id, $name, $image_path, $price, $description, $quantity);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Product added successfully!";
    } else {
        $_SESSION['error'] = "Error adding product: " . $stmt->error;
    }

    $stmt->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Function to get categories for dropdown
function getCategoriesForDropdown($conn, $parentId = null, $level = 0)
{
    $options = array();

    $sql = "SELECT id, category_name FROM categories WHERE parent_id " .
        ($parentId === null ? "IS NULL" : "= ?") .
        " ORDER BY category_name";

    $stmt = $conn->prepare($sql);
    if ($parentId !== null) {
        $stmt->bind_param("i", $parentId);
    }
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $prefix = str_repeat("—", $level);
        $options[] = array(
            'id' => $row['id'],
            'name' => $prefix . ' ' . $row['category_name']
        );
        $children = getCategoriesForDropdown($conn, $row['id'], $level + 1);
        $options = array_merge($options, $children);
    }

    $stmt->close();
    return $options;
}

$categories = getCategoriesForDropdown($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="styles.css">

</head>

<body>
    <h1>Add New Product</h1>

    <?php
    if (isset($_SESSION['success'])) {
        echo "<div class='message success'>" . htmlspecialchars($_SESSION['success']) . "</div>";
        unset($_SESSION['success']);
    }
    if (isset($_SESSION['error'])) {
        echo "<div class='message error'>" . htmlspecialchars($_SESSION['error']) . "</div>";
        unset($_SESSION['error']);
    }
    ?>

    <div class="form-container">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="category_id">Category:</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select a category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo $category['id']; ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="name">Product Name:</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="form-group">
                <label for="image">Product Image:</label>
                <input type="file" id="image" name="image" accept="image/*" required onchange="previewImage(this)">
                <img id="imagePreview" class="image-preview" style="display:none;">
            </div>

            <div class="form-group">
                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" min="0" required>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" min="0" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea id="description" name="description" required></textarea>
            </div>

            <button type="submit" name="add_product" class="submit-btn">Add Product</button>
            <button onclick="location.href='viewProd.php'" class="submit-btn">View all Products</button>
        </form>
    </div>

    <script>
        function previewImage(input) {
            const preview = document.getElementById('imagePreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>

</html>

<?php
$conn->close();
?>