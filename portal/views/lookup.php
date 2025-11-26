<div class="row min-vh-100">
    <!-- Left Column - Company Branding -->
    <div class="col-lg-6 d-flex align-items-center justify-content-center bg-primary bg-opacity-10">
        <div class="text-center p-4">
            <?php if (!empty($company_logo)): ?>
                <img src="<?= htmlspecialchars($company_logo) ?>" alt="Company Logo" class="img-fluid mb-4" style="max-height: 120px;">
            <?php endif; ?>
            <h4 class="text-primary mb-4">Customer Portal</h4>
            <div class="row g-3">
                <div class="col-12">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-search text-primary me-3 fs-4"></i>
                        <div class="text-start">
                            <h6 class="mb-1">Check Repair Status</h6>
                            <small class="text-body-secondary">Track your device repair progress</small>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-chat-dots text-primary me-3 fs-4"></i>
                        <div class="text-start">
                            <h6 class="mb-1">Message Technicians</h6>
                            <small class="text-body-secondary">Communicate directly with our team</small>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-clock text-primary me-3 fs-4"></i>
                        <div class="text-start">
                            <h6 class="mb-1">Real-Time Updates</h6>
                            <small class="text-body-secondary">Get notified of progress changes</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Right Column - Login Form -->
    <div class="col-lg-6 d-flex align-items-center justify-content-center">
        <div class="w-100" style="max-width: 400px;">
            <div class="card border-0 shadow">
                <div class="card-header bg-transparent border-0 text-center pb-0">
                    <h4 class="mb-0"><i class="bi bi-search me-2"></i>Find Your Repair</h4>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Ticket Number</label>
                            <input type="text" class="form-control form-control-lg" name="ticket_number" 
                                   placeholder="TKT-000001" value="<?= htmlspecialchars($_POST['ticket_number'] ?? '') ?>" required>
                            <small class="text-body-secondary">Found on your work order receipt</small>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Phone Number</label>
                            <input type="tel" class="form-control form-control-lg" name="phone" 
                                   placeholder="(555) 123-4567" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                            <small class="text-body-secondary">Phone number on file</small>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg w-100">
                            <i class="bi bi-search me-2"></i>Check Status
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>