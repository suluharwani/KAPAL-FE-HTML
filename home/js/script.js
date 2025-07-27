document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('DOMContentLoaded', function() {
    // Load header, navbar and footer
    fetch('partials/header.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('header').innerHTML = data;
            
            // After header loads, load navbar
            fetch('partials/navbar.html')
                .then(response => response.text())
                .then(navData => {
                    document.getElementById('navbar').innerHTML = navData;
                    
                    // Add active class to current page nav item
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
        });
    
    fetch('partials/footer.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('footer').innerHTML = data;
        });
    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Boat booking form handling
    const boatBookingForm = document.getElementById('boatBookingForm');
    if (boatBookingForm) {
        boatBookingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Get form values
            const fromIsland = document.getElementById('fromIsland').value;
            const toIsland = document.getElementById('toIsland').value;
            const departureDate = document.getElementById('departureDate').value;
            const passengers = document.getElementById('passengers').value;
            const boatType = document.getElementById('boatType').value;
            const roundTrip = document.getElementById('roundTrip').checked;
            
            // Here you would typically send this data to a server
            // For demo purposes, we'll just show an alert
            alert(`Pemesanan kapal dari ${fromIsland} ke ${toIsland} pada ${departureDate} untuk ${passengers} penumpang (Tipe: ${boatType}) telah diterima. Kami akan menghubungi Anda untuk konfirmasi.`);
            
            // Reset form
            boatBookingForm.reset();
        });
    }
    
    // FAQ accordion functionality
    const faqQuestions = document.querySelectorAll('.faq-question');
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const answer = this.nextElementSibling;
            const isCollapsed = this.classList.contains('collapsed');
            
            // Close all other answers
            document.querySelectorAll('.faq-answer').forEach(item => {
                if (item !== answer) {
                    item.style.display = 'none';
                    item.previousElementSibling.classList.remove('collapsed');
                }
            });
            
            // Toggle current answer
            if (isCollapsed) {
                answer.style.display = 'block';
                this.classList.remove('collapsed');
            } else {
                answer.style.display = 'none';
                this.classList.add('collapsed');
            }
        });
    });
    
    // Prevent selecting same island for from and to
    const fromIslandSelect = document.getElementById('fromIsland');
    const toIslandSelect = document.getElementById('toIsland');
    
    if (fromIslandSelect && toIslandSelect) {
        fromIslandSelect.addEventListener('change', function() {
            if (this.value === toIslandSelect.value) {
                toIslandSelect.value = '';
            }
        });
        
        toIslandSelect.addEventListener('change', function() {
            if (this.value === fromIslandSelect.value) {
                alert('Pulau tujuan tidak boleh sama dengan pulau asal');
                this.value = '';
            }
        });
    }
});
// Navbar scroll effect
window.addEventListener('scroll', function() {
    const navbar = document.querySelector('.navbar');
    if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
    } else {
        navbar.classList.remove('scrolled');
    }
});
});