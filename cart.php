<?php
session_start();
include 'database.php';
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - MikNiks Hot Sauce</title>
    <link rel="shortcut icon" type="image/jpg" href="logo.png"/>
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
                <li><a href="index.php">Continue Shopping</a></li>
                <li><a href="About.php">About</a></li>
                <li><a href="#" class="clearCartButton">Empty</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="cart-section">
            <div class="container">
                <header>
                    <h1>Your Cart</h1>
                </header>

                <div class="listCard"></div>
                <div class="total-amount">Total amount R<span class="total-amount-value"></span></div>
                <div class="card-summary">
                    <h1>Cart Summary</h1>
                    <ul class="listCardSummary"></ul>
                    <div class="checkOut">
                        <div class="total">Total R 0</div>
                        <li><a href="card.php" class="checkoutButton">Card Payment</a></li>
                        <li><button id="cashPaymentButton">Cash Payment</button></li>
                    </div>
                    <p style="font-weight: bold; color: white;">Please Note: Card Payment is Currently not opperational select cash payment option</p>
                    <p style="font-weight: bold; color: white;">Please wait for order confirmation message below, Your cart will be cleared if the message is successful</p>
                    <div id="paymentMessage" style="display: none; color: White; margin-top: 20px;"></div>
                </div>
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
                <p>Email: mikniks.hotsauce@gmail.com</p>
                <p>Phone: 083 607 2706</p>
            </div>
            <div class="FAQ">
                <h3>FAQ</h3>
                <p><a href="FAQ.html">FAQ</a></p>
            </div>
            <div class="FAQ">
                <h3>FAQ</h3>
                <p><a href="FAQ.html">FAQ</a></p>
            </div>
        </div>
    </footer>
    <script src="cart.js"></script>
</body>
</html>
