// Carousel state management
const carouselStates = {};

function moveCarousel(updateId, direction) {
    // Get the button that was clicked to find the correct container
    const clickedButton = event.target;
    const container = clickedButton.closest('.cpv-update-images');

    if (!container) return;

    const carousel = container.querySelector('.cpv-update-images-carousel');
    const images = container.querySelectorAll('.cpv-update-image');

    // Create a unique key for this specific carousel instance
    const carouselKey = `${updateId}_${container.closest('#allUpdatesModal') ? 'modal' : 'page'}`;

    // Get current state or initialize
    if (!carouselStates[carouselKey]) {
        carouselStates[carouselKey] = 0;
    }

    let currentIndex = carouselStates[carouselKey];
    let newIndex = currentIndex + direction;

    // Handle wrap-around
    if (newIndex < 0) newIndex = images.length - 1;
    if (newIndex >= images.length) newIndex = 0;

    // Update carousel position
    carousel.style.transform = `translateX(-${newIndex * 100}%)`;
    carouselStates[carouselKey] = newIndex;
}

// Initialize all carousels on page load
document.addEventListener('DOMContentLoaded', function() {
    const carousels = document.querySelectorAll('.cpv-update-images');
    carousels.forEach(container => {
        const updateId = container.dataset.updateId;
        const isModal = container.closest('#allUpdatesModal');
        const carouselKey = `${updateId}_${isModal ? 'modal' : 'page'}`;

        carouselStates[carouselKey] = 0;
        container.querySelector('.cpv-update-images-carousel').style.transform = 'translateX(0%)';
    });
});
