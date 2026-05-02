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
            // Update cart count badge
            updateCartCount(response.data.cart_count);
            
            // Tampilkan modal popup
            showCartModal(response.data.product);
            
            // Tampilkan toast success
            Toastify({
                text: response.data.message,
                duration: 3000,
                gravity: "top",
                position: "right",
                backgroundColor: "linear-gradient(to right, #00b09b, #96c93d)",
            }).showToast();
        }
    })
    .catch(error => {
        const errorMessage = error.response?.data?.message || 'Error adding to cart';
        Toastify({
            text: errorMessage,
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
            Toastify({
                text: response.data.message,
                duration: 2000,
                gravity: "top",
                position: "right",
                backgroundColor: "linear-gradient(to right, #4facfe, #00f2fe)",
            }).showToast();
            
            setTimeout(() => {
                window.location.href = response.data.redirect;
            }, 500);
        }
    })
    .catch(error => {
        const errorMessage = error.response?.data?.message || 'Error processing buy now';
        Toastify({
            text: errorMessage,
            duration: 3000,
            gravity: "top",
            position: "right",
            backgroundColor: "linear-gradient(to right, #ff5f6d, #ffc371)",
        }).showToast();
    });
};

// Modal untuk tampilkan produk yang ditambahkan
function showCartModal(product) {
    const modal = document.getElementById('cart-modal');
    if (!modal) return;
    
    const modalContent = modal.querySelector('.modal-content');
    if (modalContent) {
        // Update konten modal
        const productImage = modalContent.querySelector('.modal-product-image');
        const productName = modalContent.querySelector('.modal-product-name');
        const productPrice = modalContent.querySelector('.modal-product-price');
        const productQty = modalContent.querySelector('.modal-product-qty');
        
        if (productImage) productImage.src = product.image;
        if (productName) productName.textContent = product.name;
        if (productPrice) productPrice.textContent = 'Rp ' + (product.price || 0).toLocaleString('id-ID');
        if (productQty) productQty.textContent = product.quantity + 'x';
    }
    
    // Tampilkan modal
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

// Tutup modal
window.closeCartModal = function() {
    const modal = document.getElementById('cart-modal');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
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
    
    // Close modal when clicking outside
    const modal = document.getElementById('cart-modal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeCartModal();
            }
        });
    }
});

document.addEventListener("DOMContentLoaded", () => {
    createIcons();
});

window.Alpine = Alpine;
Alpine.start();