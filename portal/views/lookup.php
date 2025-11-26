<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-search me-2"></i>Find Your Repair</h5>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle me-2"></i><?= htmlspecialchars($error) ?>
                </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Ticket Number</label>
                        <input type="text" class="form-control" name="ticket_number" 
                               placeholder="TKT-000001" value="<?= htmlspecialchars($_POST['ticket_number'] ?? '') ?>" required>
                        <small class="text-muted">Found on your work order receipt</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Phone Number</label>
                        <input type="tel" class="form-control" name="phone" 
                               placeholder="(555) 123-4567" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                        <small class="text-muted">Phone number on file</small>
                    </div>
                    
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search me-1"></i>Check Status
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>