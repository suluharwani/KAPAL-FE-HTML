<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Dashboard<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <!-- Welcome Card -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Selamat Datang, <?= session()->get('userData')['full_name'] ?? 'User' ?></h4>
                <p class="card-text">Sistem Manajemen Kapal dan Open Trip Raja Ampat</p>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Quick Stats -->
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">Total Kapal</h6>
                        <h3 id="total-boats">0</h3>
                    </div>
                    <div class="bg-primary bg-opacity-10 p-3 rounded">
                        <i class="bi bi-boat text-primary fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">Pemesanan Hari Ini</h6>
                        <h3 id="today-bookings">0</h3>
                    </div>
                    <div class="bg-success bg-opacity-10 p-3 rounded">
                        <i class="bi bi-journal-bookmark text-success fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">Open Trip Aktif</h6>
                        <h3 id="active-trips">0</h3>
                    </div>
                    <div class="bg-info bg-opacity-10 p-3 rounded">
                        <i class="bi bi-people text-info fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-muted">Pendapatan Bulan Ini</h6>
                        <h3 id="monthly-revenue">Rp 0</h3>
                    </div>
                    <div class="bg-warning bg-opacity-10 p-3 rounded">
                        <i class="bi bi-currency-dollar text-warning fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <!-- Recent Bookings -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Pemesanan Terbaru</h5>
                <a href="<?= base_url('bookings') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Tanggal</th>
                                <th>Kapal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="recent-bookings">
                            <tr>
                                <td colspan="5" class="text-center">Memuat data...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Upcoming Schedules -->
    <div class="col-md-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Jadwal Mendatang</h5>
                <a href="<?= base_url('schedules') ?>" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush" id="upcoming-schedules">
                    <li class="list-group-item">Memuat data...</li>
                </ul>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Fetch dashboard stats
fetch('<?= base_url('api/dashboard/stats') ?>', {
    headers: {
        'Authorization': 'Bearer <?= session()->get('token') ?>'
    }
})
.then(response => response.json())
.then(data => {
    if (data.success) {
        document.getElementById('total-boats').textContent = data.data.total_boats;
        document.getElementById('today-bookings').textContent = data.data.today_bookings;
        document.getElementById('active-trips').textContent = data.data.active_trips;
        document.getElementById('monthly-revenue').textContent = 'Rp ' + new Intl.NumberFormat('id-ID').format(data.data.monthly_revenue);
    }
});

// Fetch recent bookings
fetch('<?= base_url('api/bookings/recent') ?>', {
    headers: {
        'Authorization': 'Bearer <?= session()->get('token') ?>'
    }
})
.then(response => response.json())
.then(data => {
    const tableBody = document.getElementById('recent-bookings');
    if (data.success && data.data.length > 0) {
        tableBody.innerHTML = '';
        data.data.forEach(booking => {
            let statusClass = '';
            switch(booking.status) {
                case 'confirmed':
                    statusClass = 'success';
                    break;
                case 'pending':
                    statusClass = 'warning';
                    break;
                case 'cancelled':
                    statusClass = 'danger';
                    break;
                default:
                    statusClass = 'secondary';
            }
            
            const row = `
                <tr>
                    <td>#${booking.id}</td>
                    <td>${new Date(booking.created_at).toLocaleDateString('id-ID')}</td>
                    <td>${booking.boat_name}</td>
                    <td><span class="badge bg-${statusClass}">${booking.status}</span></td>
                    <td>
                        <a href="<?= base_url('bookings') ?>/${booking.id}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
            `;
            tableBody.innerHTML += row;
        });
    } else {
        tableBody.innerHTML = '<tr><td colspan="5" class="text-center">Tidak ada data pemesanan</td></tr>';
    }
});

// Fetch upcoming schedules
fetch('<?= base_url('api/schedules/upcoming') ?>', {
    headers: {
        'Authorization': 'Bearer <?= session()->get('token') ?>'
    }
})
.then(response => response.json())
.then(data => {
    const scheduleList = document.getElementById('upcoming-schedules');
    if (data.success && data.data.length > 0) {
        scheduleList.innerHTML = '';
        data.data.forEach(schedule => {
            const item = `
                <li class="list-group-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">${schedule.route_name}</h6>
                            <small class="text-muted">
                                ${new Date(schedule.departure_date).toLocaleDateString('id-ID')} 
                                ${schedule.departure_time}
                            </small>
                        </div>
                        <span class="badge bg-primary">${schedule.available_seats} kursi</span>
                    </div>
                </li>
            `;
            scheduleList.innerHTML += item;
        });
    } else {
        scheduleList.innerHTML = '<li class="list-group-item text-center">Tidak ada jadwal mendatang</li>';
    }
});
</script>
<?= $this->endSection() ?>