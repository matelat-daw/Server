// frontend/components/product-card/product-card.js
document.addEventListener("DOMContentLoaded", function() {
    const productCards = document.querySelectorAll('.product-card');

    productCards.forEach(card => {
        card.addEventListener('click', function() {
            const productId = this.dataset.id;
            // Redirect to product details page or perform an action
            window.location.href = `/product-details.html?id=${productId}`;
        });
    });
});