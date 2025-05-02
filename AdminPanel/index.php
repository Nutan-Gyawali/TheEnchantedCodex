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
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="nav.css">
</head>


<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="navbar-content">
            <a href="#" class="navbar-brand">
                <img src="../Landing/logo.png" alt="Brand Logo" class="logo">
                <h1 class="brand-name">The Enchanted Codex</h1>
            </a>
        </div>

    </nav>

    <!-- Sidebar -->
    <div class="sidebar">
        <button class="nav-button active" data-page="../category/view_categories.php">Categories</button>
        <button class="nav-button" data-page="../product/viewProd.php">Products</button>
        <button class="nav-button" data-page="../Discount/viewcoupon.php">Discounts</button>
        <button class="nav-button" data-page="../orders/orderdisplay.php">Orders</button>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Default content (Categories) is loaded here -->
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const navButtons = document.querySelectorAll('.nav-button');
            const mainContent = document.getElementById('mainContent');

            function initializeProductPage() {
                // Initialize Add Product button
                const addProductBtn = document.getElementById('addProduct');
                if (addProductBtn) {
                    addProductBtn.addEventListener('click', function() {
                        window.location.href = '../product/product.php';
                    });
                }
            }

            function loadPage(page) {
                fetch(page)
                    .then(response => response.text())
                    .then(data => {
                        mainContent.innerHTML = data;
                        // Initialize page-specific functionality
                        if (page.includes('viewProd.php')) {
                            initializeProductPage();
                        }
                    })
                    .catch(error => console.error('Error loading the page:', error));
            }

            // Load default page (Categories)
            loadPage('../category/view_categories.php');

            navButtons.forEach(button => {
                button.addEventListener('click', () => {
                    // Remove active class from all buttons
                    navButtons.forEach(btn => btn.classList.remove('active'));
                    button.classList.add('active');

                    // Get the page from data-page attribute
                    const page = button.getAttribute('data-page');
                    loadPage(page);
                });
            });

            // Add global functions for product actions
            window.editProduct = function(id) {
                window.location.href = `../product/edit_product.php?id=${id}`;
            }

            window.deleteProduct = function(id) {
                if (confirm('Are you sure you want to delete this product?')) {
                    fetch('../product/viewProd.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                            },
                            body: `delete_product=1&product_id=${id}`
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                document.querySelector(`[data-product-id="${id}"]`).remove();
                            } else {
                                alert('Error deleting product');
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            alert('Error deleting product');
                        });
                }
            }
        });
    </script>
</body>

</html>