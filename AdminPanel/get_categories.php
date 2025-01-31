<?php
header('Content-Type: application/json');

// Connect to the database
$host = 'localhost';
$dbname = 'ecommerce';
$user = 'root';
$password = '';
$conn = new mysqli($host, $user, $password, $dbname);

if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

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

// Get all categories
$categoryTree = getCategoryTree($conn);

// Return JSON response
echo json_encode($categoryTree);

$conn->close();
