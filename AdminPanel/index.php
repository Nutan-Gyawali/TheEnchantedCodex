<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dynamic Page Layout</title>

    <link rel="stylesheet" href="style.css">
    <script src="../category/category_management.js"></script>

    <style>

    </style>
</head>

<body>
    <div class="sidebar">
        <button class="nav-button active" data-page="categories">Categories</button>
        <button class="nav-button" data-page="products">Products</button>
        <button class="nav-button" data-page="discounts">Discounts</button>
    </div>

    <div class="main-content">
        <div id="categories" class="page active">
            <h1>Category Management</h1>
            <div id="categoryCount" class="category-count"></div>
            <a href="../category/addcategory.php" class="add-category">Add New Category</a>
            <div id="categoryTree"></div>
        </div>

        <div id="products" class="page">
            <h2>Products</h2>
            <!-- Your products content -->
        </div>

        <div id="discounts" class="page">
            <h2>Discounts</h2>
            <!-- Your discounts content -->
        </div>
    </div>

    <script>
        // Navigation code
        const navButtons = document.querySelectorAll('.nav-button');
        const pages = document.querySelectorAll('.page');

        navButtons.forEach(button => {
            button.addEventListener('click', () => {
                navButtons.forEach(btn => btn.classList.remove('active'));
                pages.forEach(page => page.classList.remove('active'));

                button.classList.add('active');
                const pageId = button.dataset.page;
                document.getElementById(pageId).classList.add('active');

                if (pageId === 'categories') {
                    loadCategories();
                }
            });
        });

        // Load categories on initial page load
        loadCategories();
    </script>
</body>

</html>