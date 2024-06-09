<?php
session_start();

// Debugging: Check if session total_amount is set
if (!isset($_SESSION['total_amount'])) {
    // Set a default value or handle the case where it's not set
    $_SESSION['total_amount'] = 0;
    // Debugging: Log the default value setting
    error_log("Total amount not set in session. Setting to default value 0.");
}

$totalAmount = $_SESSION['total_amount'] / 100; // Convert cents to dollars
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment - MikNiks Hot Sauce</title>
    <link rel="shortcut icon" type="image/jpg" href="logo.png"/>
    <link rel="stylesheet" href="styles.css">
    <style>
        .container {
            text-align: left;
            width: 500px;
            margin: auto;
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-size: 18px;
        }
        button[type="submit"] {
            display: block;
            margin-top: 10px;
            font-size: 18px;
            padding: 10px 20px;
        }
        #card-number-element,
        #card-expiry-element,
        #card-cvc-element {
            font-size: 18px;
        }
        .content {
            max-height: calc(100vh - 200px);
            overflow-y: auto;
        }
    </style>
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
                <li><a href="index.php">Home</a></li>
                <li><a href="cart.php" class="backToCart">Cart</a></li>
                <li><a href="card.php" class="checkoutButton">Checkout</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="payment-section">
            <div class="container">
                <header>
                    <h1>Enter Your Payment Details</h1>
                </header>

                <p id="total-amount">Total Amount: <?php echo 'R' . number_format($totalAmount, 2); ?></p>
                
                <form id="payment-form">
                    <label for="card-number">Card Number</label>
                    <div id="card-number-element"></div>

                    <label for="card-expiry">Expiration Date</label>
                    <div id="card-expiry-element"></div>

                    <label for="card-cvc">CVC</label>
                    <div id="card-cvc-element"></div>

                    <button type="submit">Submit Payment</button>
                </form>
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
                <p>Email: MikNiks.hotsauce@gmail.com</p>
                <p>Phone: 083 607 2706</p>
            </div>
            <div class="FAQ">
                <h3>FAQ</h3>
                <p><a href="FAQ.html">FAQ</a></p>
            </div>
        </div>
    </footer>

    <script src="https://js.stripe.com/v3/"></script>
    <script>
document.addEventListener('DOMContentLoaded', function() {
    fetch('fetch_total_amount.php')
        .then(response => response.json())
        .then(data => {
            if (data.total_amount) {
                document.getElementById('total-amount').textContent = 'Total Amount: R' + (data.total_amount / 100).toFixed(2);
            }
        })
        .catch(error => console.error('Error fetching total amount:', error));
});

var stripe = Stripe('pk_test_51PLV5u08ENRXIGZxzVSJooN2kdiKz6m4qsMW7TBXaDYRB0Hq5ZxwaoyielCjrxFTBGSAp88wPF4IGpIaDFT08RQw00IJnGwcq6');
var elements = stripe.elements();

var style = {
    base: {
        color: "#32325d",
        fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
        fontSmoothing: "antialiased",
        fontSize: "18px",
        "::placeholder": {
            color: "#aab7c4"
        }
    },
    invalid: {
        color: "#fa755a",
        iconColor: "#fa755a"
    }
};

var cardNumberElement = elements.create('cardNumber', { style: style });
cardNumberElement.mount('#card-number-element');

var cardExpiryElement = elements.create('cardExpiry', { style: style });
cardExpiryElement.mount('#card-expiry-element');

var cardCvcElement = elements.create('cardCvc', { style: style });
cardCvcElement.mount('#card-cvc-element');

var form = document.getElementById('payment-form');

form.addEventListener('submit', function(event) {
    event.preventDefault();
    stripe.createPaymentMethod({
        type: 'card',
        card: cardNumberElement,
        billing_details: {
            // Include any additional billing details if needed
        },
    }).then(function(result) {
        if (result.error) {
            console.error('Error creating payment method:', result.error.message);
            alert('Error creating payment method: ' + result.error.message);
        } else {
            fetch('payment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    payment_method_id: result.paymentMethod.id
                })
            }).then(function(response) {
                return response.json();
            }).then(function(paymentResult) {
                console.log('Payment result from server:', paymentResult);
                handleServerResponse(paymentResult);
            }).catch(function(error) {
                console.error('Error in fetch request:', error);
                alert('Error in fetch request: ' + error.message);
            });
        }
    });
});

function handleServerResponse(response) {
    if (response.error) {
        console.error('Server response error:', response.error);
        alert('Server response error: ' + response.error);
    } else if (response.requires_action) {
        stripe.handleCardAction(response.payment_intent_client_secret)
            .then(function(result) {
                if (result.error) {
                    console.error('Card action error:', result.error.message);
                    alert('Card action error: ' + result.error.message);
                } else {
                    fetch('payment.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            payment_intent_id: result.paymentIntent.id
                        })
                    }).then(function(confirmResult) {
                        return confirmResult.json();
                    }).then(handleServerResponse)
                    .catch(function(error) {
                        console.error('Error in second fetch request:', error);
                        alert('Error in second fetch request: ' + error.message);
                    });
                }
            });
    } else {
        // Payment succeeded, redirect to receipt page
        console.log('Payment succeeded!');
        alert('Payment succeeded!');
        window.location.href = 'https://mikniks.great-site.net/MikNiks/index.php'; // Redirect to receipt page
    }
}
    </script>
</body>
</html>

