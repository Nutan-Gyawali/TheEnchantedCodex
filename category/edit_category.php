<?php
// edit_category.php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../AdminPanel/AdminLogin.html");
    exit();
}
$host = 'localhost';
$dbname = 'ecommerce';
$user = 'root';
$password = '';

$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get category ID from URL
$categoryId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryName = trim($_POST['category_name']);
    $parentId = !empty($_POST['parent_id']) ? (int)$_POST['parent_id'] : null;

    // Validate input
    if (empty($categoryName)) {
        $error = "Category name is required.";
    } else {
        // Update category
        $sql = "UPDATE categories SET category_name = ?, parent_id = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($parentId === null) {
            $stmt->bind_param("si", $categoryName, $categoryId);
        } else {
            $stmt->bind_param("sii", $categoryName, $parentId, $categoryId);
        }

        if ($stmt->execute()) {
            header("Location: ../AdminPanel/index.php");
            exit;
        } else {
            $error = "Error updating category: " . $conn->error;
        }
        $stmt->close();
    }
}

// Get category details
$sql = "SELECT id, category_name, parent_id FROM categories WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$result = $stmt->get_result();
$category = $result->fetch_assoc();
$stmt->close();

// Get all categories for parent selection
$sql = "SELECT id, category_name FROM categories WHERE id != ? ORDER BY category_name";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $categoryId);
$stmt->execute();
$categories = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Category</title>
    <link rel="stylesheet" href="editcategory.css">
</head>

<body>
    <h1>Edit Category</h1>

    <?php if (isset($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" class="category-form">
        <div class="form-group">
            <label for="category_name">Category Name:</label>
            <input type="text" id="category_name" name="category_name"
                value="<?php echo htmlspecialchars($category['category_name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="parent_id">Parent Category:</label>
            <select name="parent_id" id="parent_id">
                <option value="">No Parent (Top Level)</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>"
                        <?php echo ($category['parent_id'] == $cat['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['category_name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-buttons">
            <button type="submit" class="btn-save">Save Changes</button>
            <a href="../AdminPanel/index.php" class="btn-cancel">Cancel</a>
        </div>
    </form>
</body>

</html>