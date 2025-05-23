<?php
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

// Handle category addition
if (isset($_POST['add_category'])) {
    $category_name = trim($_POST['category_name']);
    $parent_id = empty($_POST['parent_id']) ? NULL : $_POST['parent_id'];

    // Check for duplicate
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM categories WHERE category_name = ? AND parent_id <=> ?");
    $stmt->bind_param("si", $category_name, $parent_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if ($row['count'] > 0) {
        $_SESSION['message'] = "Error: Category already exists!";
    } else {
        $stmt = $conn->prepare("INSERT INTO categories (category_name, parent_id) VALUES (?, ?)");
        $stmt->bind_param("si", $category_name, $parent_id);
        if ($stmt->execute()) {
            $_SESSION['message'] = "Category added successfully!";
        } else {
            $_SESSION['message'] = "Error adding category!";
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Function to get all categories
function getAllCategories($conn)
{
    $categories = [];
    $result = $conn->query("SELECT id, category_name, parent_id FROM categories ORDER BY category_name");
    while ($row = $result->fetch_assoc()) {
        $categories[] = $row;
    }
    return $categories;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Category</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #FFF3E0;
            padding: 2rem;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        h1 {
            color: #BF360C;
            margin-bottom: 2rem;
            text-align: center;
        }

        .category-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        label {
            color: #BF360C;
            font-weight: bold;
        }

        input,
        select {
            padding: 0.8rem;
            border: 2px solid #FFCCBC;
            border-radius: 5px;
            font-size: 1rem;
        }

        button {
            background-color: #FF9800;
            color: white;
            padding: 1rem 2rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #F57C00;
        }

        .message {
            padding: 1rem;
            margin-bottom: 1.5rem;
            border-radius: 5px;
            text-align: center;
        }

        .success {
            background-color: #C8E6C9;
            color: #2E7D32;
        }

        .error {
            background-color: #FFCDD2;
            color: #C62828;
        }

        .navigation {
            margin-top: 2rem;
            text-align: center;
        }

        .navigation a {
            color: #FF5722;
            text-decoration: none;
            margin: 0 1rem;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .navigation a:hover {
            background-color: #FFE0B2;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Add New Category</h1>

        <?php if (isset($_SESSION['message'])): ?>
            <div class="message <?php echo strpos($_SESSION['message'], 'Error') !== false ? 'error' : 'success'; ?>">
                <?= htmlspecialchars($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <form class="category-form" method="POST">
            <div class="form-group">
                <label for="category_name">Category Name:</label>
                <input type="text" id="category_name" name="category_name" required>
            </div>

            <div class="form-group">
                <label for="parent_id">Parent Category:</label>
                <select id="parent_id" name="parent_id">
                    <option value="">-- Top Level Category --</option>
                    <?php foreach (getAllCategories($conn) as $category): ?>
                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['category_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" name="add_category">Add Category</button>
        </form>

        <div class="navigation">

            <a href="../AdminPanel/index.php">Back to Home</a>
            <a href="../product/product.php">Go to Products</a>
        </div>
    </div>
</body>

</html>

<?php $conn->close(); ?>