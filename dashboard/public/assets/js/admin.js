// Toggle sidebar
document.getElementById('sidebarCollapse').addEventListener('click', function() {
    document.getElementById('sidebar').classList.toggle('active');
});

// Initialize tooltips
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// Handle form submissions with confirmation
document.querySelectorAll('.confirm-submit').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        if (confirm('Apakah Anda yakin?')) {
            this.submit();
        }
    });
});

// DataTable initialization (if using DataTables)
if (typeof $ !== 'undefined' && $.fn.DataTable) {
    $('.datatable').DataTable({
        responsive: true,
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.4/i18n/id.json'
        }
    });
}

// Handle image preview for file inputs
document.querySelectorAll('.image-preview-input').forEach(input => {
    input.addEventListener('change', function() {
        const file = this.files[0];
        const preview = document.getElementById(this.dataset.preview);
        
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            
            reader.readAsDataURL(file);
        }
    });
});