document.addEventListener('DOMContentLoaded', function() {
    let listCard = document.querySelector('.listCard');
    let total = document.querySelector('.total');
    let checkoutButton = document.querySelector('.checkoutButton');
    let clearCartButton = document.querySelector('.clearCartButton');
    let totalAmountSpan = document.querySelector('.total-amount-value');
    let cartMessage = document.createElement('div');
    cartMessage.textContent = "Your cart is empty.";
    let quantitySpan = document.querySelector('.cart-counter');

    let listCards = JSON.parse(localStorage.getItem('cart')) || [];

    function reloadCard() {
        listCard.innerHTML = '';
        let totalPrice = 0;
        if (listCards.length > 0) {
            listCards.forEach((value, key) => {
                totalPrice += value.price * value.quantity;
                let newDiv = document.createElement('li');
                newDiv.innerHTML = `
                    <div>${value.name}</div>
                    <div>R ${(value.price * value.quantity).toFixed(2)}</div>
                    <div>
                        <button onclick="changeQuantity(${key}, ${value.quantity - 1})">-</button>
                        <div class="count">${value.quantity}</div>
                        <button onclick="changeQuantity(${key}, ${value.quantity + 1})">+</button>
                    </div>`;
                listCard.appendChild(newDiv);
            });
            total.innerText = "Total R " + totalPrice.toFixed(2);
            updateTotalAmountDisplay();
            document.querySelector('.total-amount').style.display = 'block';
            document.querySelector('.checkoutButton').style.display = 'block';
            if (listCard.contains(cartMessage)) {
                listCard.removeChild(cartMessage);
            }
            document.querySelector('header h1').textContent = "Your Cart";
        } else {
            total.innerText = "Total R 0";
            document.querySelector('.total-amount').style.display = 'none';
            document.querySelector('.checkoutButton').style.display = 'none';
            listCard.appendChild(cartMessage);
            document.querySelector('header h1').textContent = "Your Cart is Empty";
        }
        updateCartCounter(); // Update the counter on reload
    }

    function updateTotalAmountDisplay() {
        const totalAmount = calculateTotalAmount();
        totalAmountSpan.textContent = totalAmount.toFixed(2);
    }

    function calculateTotalAmount() {
        let totalAmount = 0;
        listCards.forEach((item) => {
            totalAmount += item.price * item.quantity;
        });
        return totalAmount;
    }

    function updateCartCounter() {
        if (quantitySpan) {
            quantitySpan.textContent = listCards.reduce((total, item) => total + item.quantity, 0);
        }
    }

    checkoutButton.addEventListener('click', () => {
        const totalAmount = calculateTotalAmount() * 100;
        fetch('set_total.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                total_amount: totalAmount
            })
        }).then(response => response.json())
          .then(() => {
            window.location.href = 'card.php';
        });
    });

    clearCartButton.addEventListener('click', () => {
        listCards = [];
        localStorage.setItem('cart', JSON.stringify(listCards));
        reloadCard();
        updateCartCounter();
    });

    window.changeQuantity = (key, quantity) => {
        if (quantity <= 0) {
            listCards.splice(key, 1);
        } else {
            listCards[key].quantity = quantity;
        }
        localStorage.setItem('cart', JSON.stringify(listCards));
        reloadCard();
    };

    reloadCard();
    updateCartCounter(); // Initialize the counter on load

    document.getElementById('cashPaymentButton').addEventListener('click', function() {
        let orderDetails = {
            items: [],
            totalPrice: 0
        };
    
        // Populate order details
        listCards.forEach(item => {
            let itemTotalPrice = item.price * item.quantity;
            orderDetails.items.push({
                name: item.name,
                quantity: item.quantity,
                price: item.price,
                total: itemTotalPrice
            });
            orderDetails.totalPrice += itemTotalPrice;
        });
    
        // Send order details to server for processing
        fetch('process_cash_payment.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(orderDetails)
        })
        .then(response => response.json())
        .then(data => {
            // Handle response (e.g., display success message)
            let paymentMessage = document.getElementById('paymentMessage');
            if (data.success) {
                paymentMessage.textContent = 'Confirmation Email has been sent with order details.';
                paymentMessage.style.display = 'block';
    
                // Clear the cart in localStorage
                localStorage.removeItem('cart');
                listCards = [];
                reloadCard();
            } else {
                paymentMessage.textContent = 'Failed to send email: ' + data.message;
                paymentMessage.style.display = 'block';
            }
        })
        .catch(error => {
            // Handle errors
            console.error('Error:', error);
            let paymentMessage = document.getElementById('paymentMessage');
            paymentMessage.textContent = 'An error occurred while sending the email.';
            paymentMessage.style.display = 'block';
        });
    });
    
});
