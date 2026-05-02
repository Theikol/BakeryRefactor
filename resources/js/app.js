import './bootstrap';
import { createIcons } from 'lucide';

import Alpine from 'alpinejs';

window.addToCart = function(productId, qty = 1) {
    axios.post('/cart/add', {
        product_id: productId,
        qty: qty
    })
    .then(response => {
        if (response.data.success) {
            Toastify({
                text: response.data.message,
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
            }).showToast();
            updateCartCount(response.data.cart_count);
        }
    })
    .catch(error => {
        Toastify({
            text: 'Error adding to cart',
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
        }).showToast();
    });
};

window.buyNow = function(productId, qty = 1) {
    axios.post('/cart/buy-now', {
        product_id: productId,
        qty: qty
    })
    .then(response => {
        if (response.data.success) {
            window.location.href = response.data.redirect;
        }
    })
    .catch(error => {
        Toastify({
            text: 'Error processing buy now',
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
        }).showToast();
    });
};

function updateCartCount(count) {
    const cartCountEl = document.getElementById('cart-count');
    if (cartCountEl) {
        if (count > 0) {
            cartCountEl.textContent = count;
            cartCountEl.classList.remove('hidden');
        } else {
            cartCountEl.classList.add('hidden');
        }
    }
}

// Initialize cart count on page load
document.addEventListener('DOMContentLoaded', function() {
    axios.get('/cart/count')
        .then(response => {
            updateCartCount(response.data.count);
        })
        .catch(error => {
            console.log('Error fetching cart count');
        });
});

document.addEventListener("DOMContentLoaded", () => {
    createIcons();
});

window.Alpine = Alpine;
Alpine.start();