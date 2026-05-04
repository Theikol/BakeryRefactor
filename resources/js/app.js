import './bootstrap';
import { createIcons } from 'lucide';

import Alpine from 'alpinejs';
// ======================== CHATBOT ========================
window.chatbotCs = function () {
    const chatbot = document.getElementById('chatbot');
    if (!chatbot) return;

    if (chatbot.classList.contains('hidden')) {
        chatbot.classList.remove('hidden');
        chatbot.classList.add('flex');
    } else {
        chatbot.classList.add('hidden');
        chatbot.classList.remove('flex');
    }
};

function appendChatMessage(text, role = 'bot') {
    const chatBody = document.getElementById('chat-body');
    if (!chatBody) return;

    const wrapper = document.createElement('div');
    wrapper.className = role === 'user' ? 'flex justify-end' : 'flex justify-start';

    const bubble = document.createElement('div');
    bubble.className =
        role === 'user'
            ? 'max-w-[80%] rounded-2xl px-4 py-2 text-sm bg-amber-600 text-white'
            : 'max-w-[80%] rounded-2xl px-4 py-2 text-sm bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-100 shadow';

    bubble.textContent = text;
    wrapper.appendChild(bubble);
    chatBody.appendChild(wrapper);
    chatBody.scrollTop = chatBody.scrollHeight;
}

window.sendChatMessage = async function (message) {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

    const response = await fetch('/chatbot', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
        },
        body: JSON.stringify({ message }),
    });

    return await response.json(); // return full object, bukan cuma .reply
};

window.handleSendChat = async function () {
    const input = document.getElementById('chat-input');
    if (!input) return;

    const message = input.value.trim();
    if (!message) return;

    appendChatMessage(message, 'user');
    input.value = '';

    appendChatMessage('Sedang mengetik...', 'bot');

    try {
        const data = await window.sendChatMessage(message);

        const chatBody = document.getElementById('chat-body');
        if (!chatBody) return;

        chatBody.lastElementChild?.remove(); // hapus "Sedang mengetik..."

        appendChatMessage(data.reply || 'Maaf, saya tidak menemukan jawaban.', 'bot');

        if (data.type === 'product' && data.redirect) {
            setTimeout(() => {
                window.location.href = data.redirect;
            }, 800);
        }
    } catch (error) {
        const chatBody = document.getElementById('chat-body');
        chatBody?.lastElementChild?.remove();
        appendChatMessage('Terjadi kesalahan saat menghubungi chatbot.', 'bot');
        console.error(error);
    }
};

document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('chat-input');
    if (input) {
        input.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                handleSendChat();
            }
        });
    }
});

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