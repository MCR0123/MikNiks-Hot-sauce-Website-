<?php
session_start();
include 'database.php';
$isLoggedIn = false;

$user = array('full_name' => '', 'email' => '');

// Check if the user is logged in
if (isset($_SESSION['user'])) {
    $isLoggedIn = true;

    $user = $_SESSION['user'];
}

// Logout functionality
if (isset($_GET['logout'])) {
    // Clear cart
    unset($_SESSION['cart']);
    
    // Destroy session
    session_destroy();
    
    // Redirect to login page
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MikNiks Hot Sauce</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <div class="logo-container">
            <a href="index.php">
                <img src="logo.png" alt="MikNiks Logo" class="logo">
            </a>
        </div>
        <nav>
            <ul class="nav-links">
                <li><a href="About.php">About</a></li>
                <li>
                    <a href="cart.php">
                        <img src="images/shopping.svg" alt="Shopping Cart" class="nav-cart-icon">
                        <span class="cart-counter">0</span>
                    </a>
                </li>
                <?php if ($isLoggedIn): ?>
                    <li class="profile-menu">
                        <a href="javascript:void(0);" id="profile-link">Profile</a>
                        <div class="dropdown-content">
                            <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
                            <a href="logout.php">Logout</a>
                        </div>
                    </li>
                <?php else: ?>
                    <li><a href="login.php">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main>
        <section class="hero-section">
            <div class="hero-text">
                <h1>Welcome to MikNiks where we bring the heat to your life</h1>
                <a href="About.php">
                    <button class="learn-more-button">Learn More</button>
                </a>
            </div>
            <div class="hero-image">
                <img src="istockphoto-618835956-612x612.png" alt="Hot Sauce Image">
            </div>
        </section>

        <section class="cart-section">
            <div class="container">
                <header>
                    <h1>Our Products</h1>
                </header>
                <div class="message-container"></div>
                <div class="list"></div>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-logo-container"></div>
        <div class="footer-content">
            <div class="address">
                <h3>Address</h3>
                <p>123 Street Name</p>
                <p>Midrand, Gauteng, 1687</p>
                <p>South Africa</p>
            </div>
            <div class="contact-details">
                <h3>Contact Details</h3>
                <p>Email: example@mail.com</p>
                <p>Phone: phone </p>
            </div>
        </div>
    </footer>

    <script>
        const isLoggedIn = <?php echo json_encode($isLoggedIn); ?>;
        const user = <?php echo json_encode($user); ?>;
        console.log(isLoggedIn, user);
    </script>
    <script src="script.js"></script>
    <script src="cart.js"></script>
</body>
</html>

