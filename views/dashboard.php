<div class="row">
    <div class="col-12">
        <h1>Dashboard</h1>
        
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h2 class="text-primary"><?= $stats['companies'] ?></h2>
                        <p class="card-text">Active Companies</p>
                        <a href="/SupporTracker/companies" class="btn btn-primary btn-sm">View Companies</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h2 class="text-success"><?= $stats['assets'] ?></h2>
                        <p class="card-text">Total Assets</p>
                        <a href="/SupporTracker/assets" class="btn btn-success btn-sm">View Assets</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h2 class="text-info"><?= $stats['employees'] ?></h2>
                        <p class="card-text">Employees</p>
                        <a href="/SupporTracker/employees" class="btn btn-info btn-sm">View Employees</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h2 class="text-warning">0</h2>
                        <p class="card-text">Open Tickets</p>
                        <a href="#" class="btn btn-warning btn-sm">Coming Soon</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>