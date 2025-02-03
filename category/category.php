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

function getCategoryTree($conn, $parentId = null)
{
    $tree = array();
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
        $category = array(
            'id' => $row['id'],
            'name' => $row['category_name'],
            'children' => getCategoryTree($conn, $row['id'])
        );
        $tree[] = $category;
    }

    $stmt->close();
    return $tree;
}

$categories = getCategoryTree($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management</title>
    <link rel="stylesheet" href="category.css">

    <style>

    </style>
</head>

<body>
    <h1>Category Management</h1>
    <br>
    <h2>Add Categories</h2>

    <?php
    if (isset($_SESSION['message'])) {
        $messageClass = strpos($_SESSION['message'], 'Error') !== false ? 'error' : 'success';
        echo "<div class='message {$messageClass}'>" . htmlspecialchars($_SESSION['message']) . "</div>";
        unset($_SESSION['message']);
    }
    ?>

    <div class="dropdown-container">
        <button class="main-dropdown">Add New Categories ▼</button>
        <div class="dropdown-menu" id="mainMenu">
            <div class="dropdown-item">
                <span>Add Top Level Category</span>
                <button class="add-button" onclick="showAddForm(null)">+</button>
            </div>
            <?php
            function renderCategoryMenu($categories)
            {
                foreach ($categories as $category) {
                    $hasChildren = !empty($category['children']);
                    echo '<div class="dropdown-item ' . ($hasChildren ? 'has-children' : '') . '">';
                    echo '<span>' . htmlspecialchars($category['name']) . '</span>';
                    echo '<button class="add-button" onclick="showAddForm(' . $category['id'] . ')">+</button>';
                    if ($hasChildren) {
                        echo '<div class="sub-menu">';
                        renderCategoryMenu($category['children']);
                        echo '</div>';
                    }
                    echo '</div>';
                }
            }
            renderCategoryMenu($categories);
            ?>
            <div class="add-category-form" id="addCategoryForm">
                <form method="POST">
                    <input type="hidden" name="parent_id" id="parent_id">
                    <input type="text" name="category_name" placeholder="Category Name" required>
                    <button type="submit" name="add_category">Add</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const mainDropdown = document.querySelector('.main-dropdown');
            const dropdownMenu = document.querySelector('.dropdown-menu');
            let isOpen = false;

            mainDropdown.addEventListener('click', function() {
                isOpen = !isOpen;
                dropdownMenu.style.display = isOpen ? 'block' : 'none';
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.dropdown-container')) {
                    isOpen = false;
                    dropdownMenu.style.display = 'none';
                }
            });
        });

        function showAddForm(parentId) {
            event.stopPropagation();
            const form = document.getElementById('addCategoryForm');
            const parentIdInput = document.getElementById('parent_id');

            // Show the form
            form.style.display = 'block';

            // Set the parent ID
            parentIdInput.value = parentId !== null ? parentId : '';

            // Position the form after the clicked button
            const button = event.target;
            const item = button.closest('.dropdown-item');
            if (item.nextElementSibling !== form) {
                item.parentNode.insertBefore(form, item.nextElementSibling);
            }
        }
    </script>
</body>

</html>

<?php
$conn->close();
?>
<html>

<head>
    <link rel="stylesheet" href="button.css">
    <style>

    </style>
</head>

<body>
    <form method="POST" action="../AdminPanel/index.php">
        <input type="submit" name="viewCat" value="Go Back To Home">
    </form>






</body>

</html>