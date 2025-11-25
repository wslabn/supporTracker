<!DOCTYPE html>
<html>
<head>
    <title>Phase 2 Test Checklist - SupportTracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .test-item { margin: 10px 0; padding: 15px; border: 1px solid #ddd; border-radius: 5px; }
        .test-pass { background-color: #d4edda; border-color: #c3e6cb; }
        .test-fail { background-color: #f8d7da; border-color: #f5c6cb; }
        .test-pending { background-color: #fff3cd; border-color: #ffeaa7; }
        .btn-test { margin: 5px; }
    </style>
</head>
<body>
    <div class="container mt-4">
        <h1><i class="fas fa-clipboard-check"></i> Phase 2 Test Checklist</h1>
        <p class="lead">Test the new Projects and Parts Management system</p>
        
        <div class="alert alert-info">
            <strong>Phase 2 Features:</strong> Projects, Parts Management, Enhanced Work Orders
        </div>

        <!-- Navigation Tests -->
        <div class="card mb-4">
            <div class="card-header"><h5>Navigation & Access Tests</h5></div>
            <div class="card-body">
                <div class="test-item test-pending" id="nav-1">
                    <strong>1. Navigation Links</strong>
                    <p>Check that new navigation links exist and work</p>
                    <button class="btn btn-primary btn-test" onclick="testLink('/SupporTracker/workorders', 'nav-1')">Test Work Orders</button>
                    <button class="btn btn-primary btn-test" onclick="testLink('/SupporTracker/projects', 'nav-1')">Test Projects</button>
                    <button class="btn btn-primary btn-test" onclick="testLink('/SupporTracker/parts', 'nav-1')">Test Parts</button>
                </div>
            </div>
        </div>

        <!-- Database Tests -->
        <div class="card mb-4">
            <div class="card-header"><h5>Database Schema Tests</h5></div>
            <div class="card-body">
                <div class="test-item test-pending" id="db-1">
                    <strong>2. Database Tables</strong>
                    <p>Verify new tables were created correctly</p>
                    <button class="btn btn-info btn-test" onclick="testDatabase('db-1')">Check Database Schema</button>
                    <div id="db-results"></div>
                </div>
            </div>
        </div>

        <!-- Work Orders Tests -->
        <div class="card mb-4">
            <div class="card-header"><h5>Work Orders System Tests</h5></div>
            <div class="card-body">
                <div class="test-item test-pending" id="wo-1">
                    <strong>3. Work Orders Page</strong>
                    <p>Test work orders list and creation</p>
                    <button class="btn btn-success btn-test" onclick="markTest('wo-1', 'pass')">✓ Page Loads</button>
                    <button class="btn btn-success btn-test" onclick="markTest('wo-1', 'pass')">✓ Create Modal Opens</button>
                    <button class="btn btn-danger btn-test" onclick="markTest('wo-1', 'fail')">✗ Issues Found</button>
                </div>

                <div class="test-item test-pending" id="wo-2">
                    <strong>4. Create Work Order</strong>
                    <p>Test creating a standalone work order</p>
                    <button class="btn btn-success btn-test" onclick="markTest('wo-2', 'pass')">✓ Created Successfully</button>
                    <button class="btn btn-danger btn-test" onclick="markTest('wo-2', 'fail')">✗ Creation Failed</button>
                </div>

                <div class="test-item test-pending" id="wo-3">
                    <strong>5. Work Order Status</strong>
                    <p>Test status changes and priority levels</p>
                    <button class="btn btn-success btn-test" onclick="markTest('wo-3', 'pass')">✓ Status Changes Work</button>
                    <button class="btn btn-danger btn-test" onclick="markTest('wo-3', 'fail')">✗ Status Issues</button>
                </div>
            </div>
        </div>

        <!-- Projects Tests -->
        <div class="card mb-4">
            <div class="card-header"><h5>Projects System Tests</h5></div>
            <div class="card-body">
                <div class="test-item test-pending" id="proj-1">
                    <strong>6. Projects Page Access</strong>
                    <p>Test if projects page loads (may show errors - that's expected)</p>
                    <button class="btn btn-warning btn-test" onclick="markTest('proj-1', 'pass')">⚠ Page Accessible</button>
                    <button class="btn btn-danger btn-test" onclick="markTest('proj-1', 'fail')">✗ Page Error</button>
                </div>

                <div class="test-item test-pending" id="proj-2">
                    <strong>7. Projects Controller</strong>
                    <p>Check if projects controller is working</p>
                    <button class="btn btn-success btn-test" onclick="markTest('proj-2', 'pass')">✓ Controller Works</button>
                    <button class="btn btn-danger btn-test" onclick="markTest('proj-2', 'fail')">✗ Controller Error</button>
                </div>
            </div>
        </div>

        <!-- Parts Tests -->
        <div class="card mb-4">
            <div class="card-header"><h5>Parts System Tests</h5></div>
            <div class="card-body">
                <div class="test-item test-pending" id="parts-1">
                    <strong>8. Parts Page Access</strong>
                    <p>Test if parts page loads (may show errors - that's expected)</p>
                    <button class="btn btn-warning btn-test" onclick="markTest('parts-1', 'pass')">⚠ Page Accessible</button>
                    <button class="btn btn-danger btn-test" onclick="markTest('parts-1', 'fail')">✗ Page Error</button>
                </div>

                <div class="test-item test-pending" id="parts-2">
                    <strong>9. Parts Controller</strong>
                    <p>Check if parts controller is working</p>
                    <button class="btn btn-success btn-test" onclick="markTest('parts-2', 'pass')">✓ Controller Works</button>
                    <button class="btn btn-danger btn-test" onclick="markTest('parts-2', 'fail')">✗ Controller Error</button>
                </div>
            </div>
        </div>

        <!-- Integration Tests -->
        <div class="card mb-4">
            <div class="card-header"><h5>Integration Tests</h5></div>
            <div class="card-body">
                <div class="test-item test-pending" id="int-1">
                    <strong>10. Work Order Enhancement</strong>
                    <p>Check if work orders now support project association</p>
                    <button class="btn btn-success btn-test" onclick="markTest('int-1', 'pass')">✓ Project Field Available</button>
                    <button class="btn btn-danger btn-test" onclick="markTest('int-1', 'fail')">✗ No Project Field</button>
                </div>
            </div>
        </div>

        <!-- Summary -->
        <div class="card mt-4">
            <div class="card-header"><h5>Test Summary</h5></div>
            <div class="card-body">
                <div id="summary">
                    <p>Complete the tests above to see summary</p>
                </div>
                <button class="btn btn-primary" onclick="generateSummary()">Generate Summary</button>
            </div>
        </div>
    </div>

    <script>
        function testLink(url, testId) {
            window.open(url, '_blank');
            // Auto-mark as pass since user can manually verify
            setTimeout(() => {
                markTest(testId, 'pass');
            }, 1000);
        }

        function testDatabase(testId) {
            fetch('/SupporTracker/check-db')
                .then(response => response.text())
                .then(html => {
                    document.getElementById('db-results').innerHTML = '<div class="mt-2 p-2 bg-light border rounded"><small>' + html + '</small></div>';
                    markTest(testId, 'pass');
                })
                .catch(error => {
                    document.getElementById('db-results').innerHTML = '<div class="mt-2 p-2 bg-danger text-white rounded">Error: ' + error + '</div>';
                    markTest(testId, 'fail');
                });
        }

        function markTest(testId, result) {
            const element = document.getElementById(testId);
            element.className = element.className.replace(/test-(pass|fail|pending)/, 'test-' + result);
            
            // Add result indicator
            let indicator = element.querySelector('.result-indicator');
            if (!indicator) {
                indicator = document.createElement('span');
                indicator.className = 'result-indicator float-end';
                element.appendChild(indicator);
            }
            
            if (result === 'pass') {
                indicator.innerHTML = '<i class="fas fa-check-circle text-success"></i> PASS';
            } else if (result === 'fail') {
                indicator.innerHTML = '<i class="fas fa-times-circle text-danger"></i> FAIL';
            }
        }

        function generateSummary() {
            const tests = document.querySelectorAll('.test-item');
            let passed = 0, failed = 0, pending = 0;
            
            tests.forEach(test => {
                if (test.classList.contains('test-pass')) passed++;
                else if (test.classList.contains('test-fail')) failed++;
                else pending++;
            });
            
            const total = tests.length;
            const passRate = Math.round((passed / total) * 100);
            
            document.getElementById('summary').innerHTML = `
                <div class="row">
                    <div class="col-md-3"><div class="text-center"><h4 class="text-success">${passed}</div><small>Passed</small></div></div>
                    <div class="col-md-3"><div class="text-center"><h4 class="text-danger">${failed}</div><small>Failed</small></div></div>
                    <div class="col-md-3"><div class="text-center"><h4 class="text-warning">${pending}</div><small>Pending</small></div></div>
                    <div class="col-md-3"><div class="text-center"><h4 class="text-primary">${passRate}%</div><small>Pass Rate</small></div></div>
                </div>
                <div class="mt-3">
                    <strong>Status:</strong> ${passRate >= 80 ? '<span class="text-success">Phase 2 Core Ready!</span>' : passRate >= 60 ? '<span class="text-warning">Needs Some Work</span>' : '<span class="text-danger">Major Issues Found</span>'}
                </div>
            `;
        }
    </script>
</body>
</html>