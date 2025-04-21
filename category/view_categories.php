<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../AdminPanel/AdminLogin.html");
    exit();
}
// Connect to the database
$host = 'localhost';
$dbname = 'ecommerce';
$user = 'root';
$password = '';
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
//     header("Location: ../AdminPanel/AdminLogin.html");
//     exit();
// }
// Function to get all categories with their subcategories
function getCategoryTree($conn, $parentId = null, $level = 0)
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
            'level' => $level,
            'children' => getCategoryTree($conn, $row['id'], $level + 1)
        );
        $tree[] = $category;
    }

    $stmt->close();
    return $tree;
}

// Function to display the category tree
function displayCategoryTree($categories)
{
    echo '<ul class="category-tree">';
    foreach ($categories as $category) {
        echo '<li>';
        // Indent based on level
        echo str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $category['level']);

        // Display category name
        echo htmlspecialchars($category['name']);

        // Add edit and delete buttons
        echo ' <a href="../category/edit_category.php?id=' . $category['id'] . '" class="btn-edit">Edit</a>';
        echo ' <a href="../category/delete_category.php?id=' . $category['id'] . '" class="btn-delete" onclick="return confirm(\'Are you sure you want to delete this category and all its subcategories?\')">Delete</a>';

        // Recursively display children
        if (!empty($category['children'])) {
            displayCategoryTree($category['children']);
        }
        echo '</li>';
    }
    echo '</ul>';
}

// Get all categories
$categoryTree = getCategoryTree($conn);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories</title>
    <link rel="stylesheet" href="view.css">
    <link rel="stylesheet" href="category.css">
</head>
<style>
    /* Basic Reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    /* Body Styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f8f5ff;
        color: #333;
        padding: 20px;
    }

    /* Heading Styles */
    h1 {
        font-size: 2em;
        color: #6b2fb3;
        text-align: center;
        margin-bottom: 20px;
    }

    /* Category Count */
    .category-count {
        font-size: 1.1em;
        margin-bottom: 20px;
        color: #ff6b00;
        text-align: center;
    }

    /* Add Category Link */
    .add-category {
        display: inline-block;
        padding: 10px 20px;
        background-color: #ff6b00;
        color: white;
        text-decoration: none;
        border-radius: 4px;
        font-size: 1.1em;
        transition: all 0.3s ease;
        margin-bottom: 20px;
        text-align: center;
        justify-content: center;
    }

    .add-category:hover {
        background-color: #ff8c42;
        box-shadow: 0 2px 8px rgba(255, 107, 0, 0.3);
    }

    /* Category Tree Styles */
    .category-tree {
        list-style-type: none;
        padding-left: 20px;
    }

    .category-tree li {
        padding: 10px;
        background-color: #fff;
        border: 1px solid #e0d5f5;
        margin-bottom: 5px;
        border-radius: 4px;
        font-size: 1.1em;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: all 0.3s ease;
    }

    .category-tree li:hover {
        background-color: #fef4eb;
        border-color: #ffb88c;
    }

    .category-tree li a {
        text-decoration: none;
        color: #6b2fb3;
        margin-left: 10px;
    }

    .category-tree li a:hover {
        color: #ff6b00;
        text-decoration: underline;
    }

    .category-tree li .btn-edit {
        color: #8a4fff;
    }

    .category-tree li .btn-delete {
        color: #ff6b00;
    }

    /* Indentation based on level */
    .category-tree li ul {
        margin-top: 10px;
    }

    .category-tree li ul li {
        margin-left: 20px;
    }

    /* Form Styling */
    form {
        text-align: center;
        margin-top: 20px;
    }

    form input[type="submit"] {
        padding: 10px 20px;
        background-color: #6b2fb3;
        color: white;
        border: none;
        border-radius: 4px;
        font-size: 1.1em;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    form input[type="submit"]:hover {
        background-color: #8a4fff;
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(107, 47, 179, 0.3);
    }

    form input[type="submit"]:active {
        background-color: #5a2896;
    }

    /* Responsive Styling */
    @media (max-width: 768px) {
        body {
            padding: 10px;
        }

        .add-category {
            width: 100%;
            text-align: center;
        }

        .category-tree li {
            font-size: 1em;
        }

        form input[type="submit"] {
            width: 100%;
            font-size: 1em;
        }
    }
</style>

<body>
    <h1>Category Management</h1>
    <?php
    // Display total category count
    $totalCategories = 0;
    function countCategories($categories)
    {
        $count = count($categories);
        foreach ($categories as $category) {
            $count += count($category['children']);
        }
        return $count;
    }
    $totalCategories = countCategories($categoryTree);
    echo "<div class='category-count'>Total Categories: $totalCategories</div>";
    ?>
    <a href="../category/category.php" class="add-category">Add New Category</a><?php
                                                                                if (empty($categoryTree)) {
                                                                                    echo "<p>No categories found.</p>";
                                                                                } else {
                                                                                    displayCategoryTree($categoryTree);
                                                                                }
                                                                                ?>
</body>

</html><?php
        $conn->close();
        ?>