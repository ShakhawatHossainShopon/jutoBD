document.addEventListener('DOMContentLoaded', function() {
    const thumbnails = document.querySelectorAll('.pdp-thumbnail');
    const mainImage = document.getElementById('main-product-image');

    thumbnails.forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            // Change main image src
            mainImage.src = this.dataset.imageUrl;

            // Update active thumbnail class
            thumbnails.forEach(t => t.classList.remove('pdp-thumbnail-active'));
            this.classList.add('pdp-thumbnail-active');
        });
    });
});
