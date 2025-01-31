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
        <button class="main-dropdown">Categories ▼</button>
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
    <form method="POST" action="view_categories.php">
        <input type="submit" name="viewCat" value="View All Categories">
    </form>
    <form method="POST" action="../adminpanel.php">
        <input type="submit" name="welcome" value="Go to Home Page">
    </form>



    <button id="category" onclick="location.href='../product/product.php'">Go to Products Page</button>

</body>

</html>