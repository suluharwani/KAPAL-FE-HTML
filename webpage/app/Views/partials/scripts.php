<!-- app/Views/partials/scripts.php -->
<!-- JavaScript Libraries -->
<script src="<?= base_url('assets/js/bootstrap.bundle.min.js') ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>

<!-- Custom Scripts -->
<script>
// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// Navbar active state
document.addEventListener('DOMContentLoaded', function() {
    const currentPage = window.location.pathname.split('/').pop() || 'index.html';
    document.querySelectorAll('.nav-link').forEach(link => {
        if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
        }
        
        // Handle dropdown parent active state
        if (link.parentElement.classList.contains('dropdown')) {
            const dropdownItems = link.nextElementSibling.querySelectorAll('.dropdown-item');
            dropdownItems.forEach(item => {
                if (item.getAttribute('href') === currentPage) {
                    link.classList.add('active');
                }
            });
        }
    });
});
</script>