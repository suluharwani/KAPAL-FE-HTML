<?= $this->include('templates/admin_header') ?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Total Boats</h6>
                        <h2 class="mb-0">15</h2>
                    </div>
                    <i class="bi bi-boat fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Today's Bookings</h6>
                        <h2 class="mb-0">8</h2>
                    </div>
                    <i class="bi bi-calendar-check fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">Pending Payments</h6>
                        <h2 class="mb-0">5</h2>
                    </div>
                    <i class="bi bi-cash-coin fs-1"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title">New Messages</h6>
                        <h2 class="mb-0">3</h2>
                    </div>
                    <i class="bi bi-envelope fs-1"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Bookings</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Booking Code</th>
                                <th>Customer</th>
                                <th>Date</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>BK-20230801-001</td>
                                <td>John Doe</td>
                                <td>2023-08-01</td>
                                <td><span class="badge bg-success">Paid</span></td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>BK-20230801-002</td>
                                <td>Jane Smith</td>
                                <td>2023-08-02</td>
                                <td><span class="badge bg-warning text-dark">Pending</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Recent Activities</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">10 min ago</small>
                            <p class="mb-0">New booking created</p>
                        </div>
                        <span class="badge bg-primary rounded-pill">New</span>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">1 hour ago</small>
                            <p class="mb-0">Payment verified</p>
                        </div>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <small class="text-muted">2 hours ago</small>
                            <p class="mb-0">New boat added</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?= $this->include('templates/admin_footer') ?>