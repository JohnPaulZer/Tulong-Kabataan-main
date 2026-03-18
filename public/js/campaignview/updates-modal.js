// All Updates Modal Functions
function showAllUpdatesModal() {
    document.getElementById('allUpdatesModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';

    // Reset all carousels in modal to first slide
    const modalCarousels = document.querySelectorAll('#allUpdatesModal .cpv-update-images-carousel');
    modalCarousels.forEach(carousel => {
        carousel.style.transform = 'translateX(0%)';
    });
}

function closeAllUpdatesModal() {
    document.getElementById('allUpdatesModal').style.display = 'none';
    document.body.style.overflow = 'auto';
}

// Close all updates modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const updatesModal = document.getElementById('allUpdatesModal');
    if (updatesModal) {
        updatesModal.addEventListener('click', function(e) {
            if (e.target === this) {
                closeAllUpdatesModal();
            }
        });
    }
});
