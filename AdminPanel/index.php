<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Page Layout</title>
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
        <button class="nav-button active" data-page="categories.php">Categories</button>
        <button class="nav-button" data-page="products.php">Products</button>
        <button class="nav-button" data-page="discounts.php">Discounts</button>
        <button class="nav-button" data-page="orders.php">Orders</button>
    </div>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Default content (Categories) is loaded here -->
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const navButtons = document.querySelectorAll('.nav-button');
            const mainContent = document.getElementById('mainContent');

            function loadPage(page) {
                fetch(page)
                    .then(response => response.text())
                    .then(data => {
                        mainContent.innerHTML = data;
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
        });
    </script>
</body>

</html>