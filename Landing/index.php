<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Enchanted Codex</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="index.css">
    <style>
        .footer-section ul {
            list-style: none;
            padding: 0;
        }

        .footer-section ul li {
            margin: 6px 0;
        }

        .footer-section ul li a {
            color: #e77808;
            text-decoration: none;
            font-weight: 500;
        }

        .footer-section ul li a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <!-- Navigation -->
    <nav>
        <div class="nav-container">
            <div class="logo-container">
                <div class="logo">
                    <img src="logo.png" alt="EC Logo">
                </div>
                <h1>The Enchanted Codex</h1>
            </div>
            <div class="nav-links">
                <a href="explore.php">Shop</a>
                <a href="#about">About</a>
                <a href="#contact">Contact</a>
                <button class="sign-in-btn" onclick="location.href='../login/registration.php'">Sign In</button>

            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <h1>Welcome to The Enchanted Codex</h1>
        <p>Turn a page, Shift a Universe</p>

        <button class="explore-btn" onclick="location.href='explore.php'">
            Explore Collection
            <i class="fas fa-chevron-right"></i>
        </button>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="features-grid">
            <div class="feature-card">
                <i class="fas fa-book"></i>
                <h3>Rare Collections</h3>
                <p>Discover ancient tomes and mystical manuscripts</p>
            </div>
            <div class="feature-card">
                <i class="fa-solid fa-pen-to-square"></i>
                <h3>Book Journals</h3>
                <p>Keep a track of all the books you've read</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-shield-alt"></i>
                <h3>Secure Trading</h3>
                <p>Protected by advanced arcane security measures</p>
            </div>
            <div class="feature-card">
                <i class="fas fa-chart-line"></i>
                <h3>Growing Collection</h3>
                <p>New bookish items added weekly</p>
            </div>
        </div>
    </section>

    <!-- Products Section -->
    <section class="products">
        <h2>Featured Magical Items</h2>
        <div class="products-grid">
            <div class="product-card">
                <img src="books.jpeg" alt="Magical Item 1">
                <div class="product-info">
                    <h3>Books</h3>
                    <p>Rare books you will not find elsewhere.</p>
                    <div style="margin-top: 1rem;">

                        <button class="explore-btn" onclick="location.href='explore.php'">
                            Explore
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class="product-card">
                <img src="journall.jpeg" alt="Magical Item 2">
                <div class="product-info">
                    <h3>Book Journals</h3>
                    <p>Keep track of all the books you've read</p>
                    <div style="margin-top: 1rem;">

                        <button class="explore-btn" onclick="location.href='explore.php'">
                            Explore
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
            <div class=" product-card">
                <img src="bookmark.jpeg" alt="Magical Item 3">
                <div class="product-info">
                    <h3>Bookmarks</h3>
                    <p>Find Prettiest Bookmarks</p>
                    <div style="margin-top: 1rem;">

                        <button class="explore-btn" onclick="location.href='explore.php'">
                            Explore
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
            </div>
            <!-- <div class="product-card">
                <img src="https://via.placeholder.com/400x300" alt="Magical Item 3">
                <div class="product-info">
                    <h3>Book Nooks</h3>
                    <p>Find booknooks and character cards</p>
                    <div style="margin-top: 1rem;">

                        <button class="add-to-cart">Explore</button>
                    </div>
                </div>
            </div> -->
        </div>
    </section>

    <!-- Footer -->
    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h4>About Us</h4>
                <p>Welcome to The Enchanted Codex, a magical haven for book lovers and collectors alike. Our mission is to bring stories to life, connecting readers with rare tomes, enchanting journals, and mystical manuscripts that inspire imagination and adventure. </p>
                <p>
                    📖✨ Turn a page, Shift a Universe ✨📖</p>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="explore.php">Shop</a></li>
                    <li><a href="static/about.html">About</a></li>
                    <li><a href="static/contact.html">Contact</a></li>
                    <li><a href="static/FAQ.html">FAQ</a></li>
                </ul>
            </div>

            <div class="footer-section">
                <h4>Customer Service</h4>
                <ul>
                    <li><a href="static/shipping.html">Shipping Information</a></li>
                    <li><a href="static/returnpolicy.html">Return Policy</a></li>
                    <li><a href="static/payment.html">Payment Options</a></li>

                    <li><a href="static/orders.php">Order History</a>

                </ul>
            </div>
            <div class="footer-section newsletter">
                <h4>Newsletter</h4>
                <p>Subscribe for updates and special offers</p>
                <div style="margin-top: 1rem;">
                    <input type="email" placeholder="Enter your email">
                    <button>Subscribe</button>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Add smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function(e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });

        // Add to cart functionality
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                alert('Item added to cart!');
            });
        });

        // Newsletter subscription
        const newsletterForm = document.querySelector('.newsletter div');
        newsletterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input').value;
            if (email) {
                alert('Thank you for subscribing!');
                this.querySelector('input').value = '';
            }
        });

        // Scroll effect for navigation
        window.addEventListener('scroll', function() {
            const nav = document.querySelector('nav');
            if (window.scrollY > 50) {
                nav.style.background = 'rgba(255, 255, 255, 0.95)';
            } else {
                nav.style.background = 'white';
            }
        });
    </script>
</body>

</html>