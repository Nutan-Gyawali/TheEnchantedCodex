<?php
// delete_category.php
session_start();
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

// Function to get all subcategories
function getSubcategories($conn, $categoryId)
{
    $subcategories = array();
    $sql = "SELECT id FROM categories WHERE parent_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $categoryId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $subcategories[] = $row['id'];
        $subcategories = array_merge($subcategories, getSubcategories($conn, $row['id']));
    }

    $stmt->close();
    return $subcategories;
}

// Start transaction
$conn->begin_transaction();

try {
    // Get all subcategories
    $categoriesToDelete = array_merge([$categoryId], getSubcategories($conn, $categoryId));

    // Delete the categories
    $sql = "DELETE FROM categories WHERE id IN (" . implode(',', array_fill(0, count($categoriesToDelete), '?')) . ")";
    $stmt = $conn->prepare($sql);

    $types = str_repeat('i', count($categoriesToDelete));
    $stmt->bind_param($types, ...$categoriesToDelete);

    if ($stmt->execute()) {
        $conn->commit();
        $_SESSION['success'] = "Category and all subcategories deleted successfully.";
    } else {
        throw new Exception("Error deleting category");
    }

    $stmt->close();
} catch (Exception $e) {
    $conn->rollback();
    $_SESSION['error'] = "Error deleting category: " . $e->getMessage();
}

// Redirect back to category list
header("Location: view_categories.php");
exit;

$conn->close();
