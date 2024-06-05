let list = document.querySelector('.list');
let quantitySpan = document.querySelector('.cart-counter');
let messageContainer = document.querySelector('.message-container');

let listCards = JSON.parse(localStorage.getItem('cart')) || [];
let products = [];

function initApp() {
    let xhr = new XMLHttpRequest();
    xhr.open('GET', 'get_products.php', true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            products = JSON.parse(xhr.responseText);
            console.log("Products:", products);
            products.forEach(function (product) {
                let newDiv = document.createElement('div');
                newDiv.classList.add('item');
                newDiv.innerHTML = `
                    <img src="images/${product.image}" alt="${product.name}">
                    <div class="title">${product.name}</div>
                    <div class="price">R ${product.price}</div>
                    <button onclick="addToCart('${product.id}', event)">Add To Cart</button>`;
                list.appendChild(newDiv);
            });
        } else {
            showMessage("Failed to fetch product data");
        }
    };
    xhr.send();
    updateCartCounter(); // Update the counter on initialization
}

function addToCart(productId, event) {
    if (!isLoggedIn) {
        showMessage("Please log in to add items to the cart", event);
        return;
    }

    console.log("Product ID:", productId);
    let product = products.find(item => item.id === productId);
    console.log("Product to add:", product);

    if (!product) {
        showMessage("Product not found", event);
        return;
    }

    let found = listCards.find(item => item.id === productId);
    if (found) {
        found.quantity++;
    } else {
        let productCopy = { ...product, quantity: 1 };
        listCards.push(productCopy);
    }
    localStorage.setItem('cart', JSON.stringify(listCards));
    updateCartCounter(); // Update the counter when item is added

    showMessage("Item added to cart. See cart page.", event);
}

function updateCartCounter() {
    if (quantitySpan) {
        quantitySpan.textContent = listCards.reduce((total, item) => total + item.quantity, 0);
    }
}

function showMessage(message, event) {
    const messageContainer = document.querySelector('.message-container');
    messageContainer.textContent = message;
    messageContainer.style.display = 'block'; // Show the message container

    // Calculate the position based on the click event
    const rect = event.target.getBoundingClientRect();
    const offsetTop = window.scrollY + rect.top;
    const offsetLeft = window.scrollX + rect.left;

    messageContainer.style.top = `${offsetTop}px`;
    messageContainer.style.left = `${offsetLeft}px`;

    setTimeout(() => {
        messageContainer.style.display = 'none'; // Hide the message container after 2 seconds
    }, 2000);
}

document.addEventListener('DOMContentLoaded', function() {
    if (isLoggedIn) {
        const profileLink = document.getElementById('profile-link');
        const dropdownContent = document.querySelector('.dropdown-content');

        profileLink.addEventListener('click', function(event) {
            event.preventDefault();
            dropdownContent.style.display = (dropdownContent.style.display === 'block') ? 'none' : 'block';
        });

        window.addEventListener('click', function(event) {
            if (!event.target.matches('#profile-link')) {
                if (dropdownContent.style.display === 'block') {
                    dropdownContent.style.display = 'none';
                }
            }
        });
    }
});

initApp();
