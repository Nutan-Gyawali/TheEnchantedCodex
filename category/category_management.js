// Function to count total categories including children
function countCategories(categories) {
    let count = categories.length;
    categories.forEach(category => {
        if (category.children) {
            count += countCategories(category.children);
        }
    });
    return count;
}

// Function to create category HTML
function createCategoryHtml(category) {
    let html = `<li>
${category.name}
<a href="../category/edit_category.php?id=${category.id}" class="btn-edit">Edit</a>
<a href="../category/delete_category.php?id=${category.id}" class="btn-delete" 
onclick="return confirm('Are you sure you want to delete this category and all its subcategories?')">Delete</a>`;

    if (category.children && category.children.length > 0) {
        html += '<ul class="category-tree">';
        category.children.forEach(child => {
            html += createCategoryHtml(child);
        });
        html += '</ul>';
    }

    html += '</li>';
    return html;
}
// Function to load categories
async function loadCategories() {
    try {
        const response = await fetch('get_categories.php');
        const categories = await response.json();

        // Update category count
        const totalCategories = countCategories(categories);
        document.getElementById('categoryCount').textContent =
            `Total Categories: ${totalCategories}`;

        // Create category tree
        let treeHtml = '<ul class="category-tree">';
        categories.forEach(category => {
            treeHtml += createCategoryHtml(category);
        });
        treeHtml += '</ul>';

        document.getElementById('categoryTree').innerHTML = treeHtml;
    } catch (error) {
        console.error('Error loading categories:', error);
        document.getElementById('categoryTree').innerHTML =
            '<p>Error loading categories. Please try again later.</p>';
    }
}