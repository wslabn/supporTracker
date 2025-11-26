<!-- Reports Dashboard -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-primary border-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Customers</div>
                        <div class="h5 mb-0 fw-bold"><?= $stats['total_customers'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-people fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-success border-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">Total Tickets</div>
                        <div class="h5 mb-0 fw-bold"><?= $stats['total_tickets'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-ticket-perforated fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-warning border-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-warning text-uppercase mb-1">Open Tickets</div>
                        <div class="h5 mb-0 fw-bold"><?= $stats['open_tickets'] ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-exclamation-triangle fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-start border-info border-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col">
                        <div class="text-xs fw-bold text-info text-uppercase mb-1">Total Revenue</div>
                        <div class="h5 mb-0 fw-bold">$<?= number_format($stats['total_revenue'], 0) ?></div>
                    </div>
                    <div class="col-auto">
                        <i class="bi bi-currency-dollar fs-2 text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h6 class="m-0 fw-bold text-primary">Available Reports</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-graph-up fs-1 text-primary mb-3"></i>
                                <h6>Revenue Report</h6>
                                <p class="text-muted small">Monthly and yearly revenue analysis</p>
                                <button class="btn btn-outline-primary btn-sm">Generate</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-clock-history fs-1 text-success mb-3"></i>
                                <h6>Time Tracking</h6>
                                <p class="text-muted small">Technician time and productivity</p>
                                <button class="btn btn-outline-success btn-sm">Generate</button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4 mb-3">
                        <div class="card h-100">
                            <div class="card-body text-center">
                                <i class="bi bi-people fs-1 text-info mb-3"></i>
                                <h6>Customer Report</h6>
                                <p class="text-muted small">Customer activity and billing</p>
                                <button class="btn btn-outline-info btn-sm">Generate</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>