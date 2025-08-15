<?php 
// Include any necessary configurations if needed
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="callout callout-primary">
                <h5><i class="fas fa-book"></i> User's Manual</h5>
                Complete guide to using the Water Billing Management System effectively.
            </div>
        </div>
    </div>

    <!-- Quick Navigation -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-compass"></i> Quick Navigation</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="#getting-started" class="btn btn-outline-primary btn-block btn-sm mb-2">
                                <i class="fas fa-play-circle"></i> Getting Started
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="#client-management" class="btn btn-outline-success btn-block btn-sm mb-2">
                                <i class="fas fa-users"></i> Client Management
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="#billing-system" class="btn btn-outline-warning btn-block btn-sm mb-2">
                                <i class="fas fa-file-invoice"></i> Billing System
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="#reports" class="btn btn-outline-info btn-block btn-sm mb-2">
                                <i class="fas fa-chart-bar"></i> Reports & Analytics
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Getting Started Section -->
            <div class="card card-primary card-outline" id="getting-started">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-play-circle"></i> Getting Started</h3>
                </div>
                <div class="card-body">
                    <h5>1. System Login</h5>
                    <p>Access the system by navigating to the login page and entering your credentials:</p>
                    <ul>
                        <li>Enter your <strong>username</strong> and <strong>password</strong></li>
                        <li>Click the "Login" button</li>
                        <li>You'll be redirected to the dashboard upon successful authentication</li>
                    </ul>
                    
                    <h5 class="mt-4">2. Dashboard Overview</h5>
                    <p>The dashboard provides a quick overview of your system:</p>
                    <div class="row">
                        <div class="col-md-6">
                            <ul>
                                <li><strong>Total Clients:</strong> Number of registered customers</li>
                                <li><strong>Paid Bills:</strong> Successfully processed payments</li>
                                <li><strong>Pending Bills:</strong> Outstanding invoices</li>
                                <li><strong>Total Revenue:</strong> Income from paid bills</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <div class="alert alert-info">
                                <i class="fas fa-lightbulb"></i> <strong>Tip:</strong> 
                                Use the dashboard statistics to quickly assess your billing status and identify areas that need attention.
                            </div>
                        </div>
                    </div>

                    <h5 class="mt-4">3. Navigation Menu</h5>
                    <p>The left sidebar contains all system modules:</p>
                    <ul>
                        <li><strong>Dashboard:</strong> System overview and statistics</li>
                        <li><strong>Clients:</strong> Customer management</li>
                        <li><strong>Categories:</strong> Billing rate categories</li>
                        <li><strong>Billings:</strong> Invoice and payment management</li>
                        <li><strong>Reports:</strong> Financial and operational reports</li>
                        <li><strong>Users:</strong> System user management</li>
                    </ul>
                </div>
            </div>

            <!-- Client Management Section -->
            <div class="card card-success card-outline mt-3" id="client-management">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-users"></i> Client Management</h3>
                </div>
                <div class="card-body">
                    <h5>Adding New Clients</h5>
                    <ol>
                        <li>Navigate to <strong>Clients</strong> in the sidebar menu</li>
                        <li>Click the <strong>"Create New"</strong> button</li>
                        <li>Fill in the required information:
                            <ul>
                                <li>Client Code (unique identifier)</li>
                                <li>First Name and Last Name</li>
                                <li>Contact Information (phone, email)</li>
                                <li>Complete Address</li>
                                <li>Connection Type and Category</li>
                            </ul>
                        </li>
                        <li>Click <strong>"Save"</strong> to add the client</li>
                    </ol>

                    <h5 class="mt-4">Managing Existing Clients</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>View Client Details:</h6>
                            <ul>
                                <li>Click the <strong>"View"</strong> button (eye icon)</li>
                                <li>Review complete client information</li>
                                <li>Check billing history and payment records</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Edit Client Information:</h6>
                            <ul>
                                <li>Click the <strong>"Edit"</strong> button (pencil icon)</li>
                                <li>Update any necessary information</li>
                                <li>Save changes</li>
                            </ul>
                        </div>
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> <strong>Important:</strong>
                        Always verify client information before saving. Incorrect data can affect billing accuracy.
                    </div>
                </div>
            </div>

            <!-- Billing System Section -->
            <div class="card card-warning card-outline mt-3" id="billing-system">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-file-invoice"></i> Billing System</h3>
                </div>
                <div class="card-body">
                    <h5>Water Meter Reading Process</h5>
                    <ol>
                        <li>Go to <strong>Billings → Input Reading</strong></li>
                        <li>Select the billing period (month/year)</li>
                        <li>Enter meter readings for each client:
                            <ul>
                                <li>Previous Reading (auto-filled from last billing)</li>
                                <li>Current Reading (new meter reading)</li>
                                <li>Consumption (automatically calculated)</li>
                            </ul>
                        </li>
                        <li>Save the readings</li>
                    </ol>

                    <h5 class="mt-4">Generating Bills</h5>
                    <ol>
                        <li>Navigate to <strong>Billings → Input Billing</strong></li>
                        <li>Select the billing period</li>
                        <li>Choose clients to generate bills for</li>
                        <li>Review calculated amounts based on:
                            <ul>
                                <li>Water consumption</li>
                                <li>Rate category</li>
                                <li>Additional fees (if any)</li>
                            </ul>
                        </li>
                        <li>Generate and save the bills</li>
                    </ol>

                    <h5 class="mt-4">Processing Payments</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Recording Payments:</h6>
                            <ol>
                                <li>Go to <strong>Billings → Manage Billing</strong></li>
                                <li>Find the client's bill</li>
                                <li>Click <strong>"Pay"</strong> button</li>
                                <li>Enter payment details</li>
                                <li>Generate receipt</li>
                            </ol>
                        </div>
                        <div class="col-md-6">
                            <h6>Payment Options:</h6>
                            <ul>
                                <li>Full payment</li>
                                <li>Partial payment</li>
                                <li>Payment with penalty (for overdue bills)</li>
                                <li>Cash or check payments</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Reports Section -->
            <div class="card card-info card-outline mt-3" id="reports">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-chart-bar"></i> Reports & Analytics</h3>
                </div>
                <div class="card-body">
                    <h5>Available Reports</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Financial Reports:</h6>
                            <ul>
                                <li><strong>Revenue Report:</strong> Total income by period</li>
                                <li><strong>Outstanding Bills:</strong> Unpaid invoices</li>
                                <li><strong>Payment History:</strong> Transaction records</li>
                                <li><strong>Collection Summary:</strong> Payment statistics</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Operational Reports:</h6>
                            <ul>
                                <li><strong>Client List:</strong> Customer database</li>
                                <li><strong>Consumption Report:</strong> Water usage analysis</li>
                                <li><strong>Billing Summary:</strong> Invoice overview</li>
                                <li><strong>Category Report:</strong> Rate structure analysis</li>
                            </ul>
                        </div>
                    </div>

                    <h5 class="mt-4">Generating Reports</h5>
                    <ol>
                        <li>Navigate to <strong>Reports</strong> in the main menu</li>
                        <li>Select the desired date range</li>
                        <li>Choose specific filters (if needed)</li>
                        <li>Click <strong>"Generate Report"</strong></li>
                        <li>Print or export the report as needed</li>
                    </ol>
                </div>
            </div>

            <!-- Troubleshooting Section -->
            <div class="card card-danger card-outline mt-3" id="troubleshooting">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tools"></i> Troubleshooting</h3>
                </div>
                <div class="card-body">
                    <h5>Common Issues & Solutions</h5>
                    
                    <div class="accordion" id="troubleshootingAccordion">
                        <div class="card">
                            <div class="card-header" id="issue1">
                                <h6 class="mb-0">
                                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse1">
                                        <i class="fas fa-question-circle"></i> Cannot login to the system
                                    </button>
                                </h6>
                            </div>
                            <div id="collapse1" class="collapse" data-parent="#troubleshootingAccordion">
                                <div class="card-body">
                                    <ul>
                                        <li>Verify your username and password are correct</li>
                                        <li>Check if Caps Lock is enabled</li>
                                        <li>Clear browser cache and cookies</li>
                                        <li>Contact system administrator if problem persists</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header" id="issue2">
                                <h6 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse2">
                                        <i class="fas fa-question-circle"></i> Bill calculation seems incorrect
                                    </button>
                                </h6>
                            </div>
                            <div id="collapse2" class="collapse" data-parent="#troubleshootingAccordion">
                                <div class="card-body">
                                    <ul>
                                        <li>Verify meter readings are entered correctly</li>
                                        <li>Check client's rate category</li>
                                        <li>Review any additional fees or penalties</li>
                                        <li>Recalculate if necessary</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="card">
                            <div class="card-header" id="issue3">
                                <h6 class="mb-0">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapse3">
                                        <i class="fas fa-question-circle"></i> Cannot generate reports
                                    </button>
                                </h6>
                            </div>
                            <div id="collapse3" class="collapse" data-parent="#troubleshootingAccordion">
                                <div class="card-body">
                                    <ul>
                                        <li>Ensure date range is properly selected</li>
                                        <li>Check if there's data for the selected period</li>
                                        <li>Verify browser supports PDF generation</li>
                                        <li>Try refreshing the page</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-3">
            <!-- Quick Tips -->
            <div class="card card-light">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-lightbulb"></i> Quick Tips</h3>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-0 p-0 mb-3">
                            <i class="fas fa-save text-primary"></i> <strong>Save Frequently</strong><br>
                            <small class="text-muted">Always save your work to prevent data loss</small>
                        </div>
                        <div class="list-group-item border-0 p-0 mb-3">
                            <i class="fas fa-eye text-success"></i> <strong>Double Check</strong><br>
                            <small class="text-muted">Review all entries before saving</small>
                        </div>
                        <div class="list-group-item border-0 p-0 mb-3">
                            <i class="fas fa-backup text-warning"></i> <strong>Regular Backups</strong><br>
                            <small class="text-muted">Export data regularly for safety</small>
                        </div>
                        <div class="list-group-item border-0 p-0 mb-3">
                            <i class="fas fa-users text-info"></i> <strong>User Training</strong><br>
                            <small class="text-muted">Ensure all users understand the system</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Requirements -->
            <div class="card card-info">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-desktop"></i> Browser Requirements</h3>
                </div>
                <div class="card-body">
                    <p class="text-sm mb-2">Recommended browsers:</p>
                    <ul class="list-unstyled text-sm">
                        <li><i class="fab fa-chrome text-warning"></i> Chrome 90+</li>
                        <li><i class="fab fa-firefox text-orange"></i> Firefox 88+</li>
                        <li><i class="fab fa-safari text-info"></i> Safari 14+</li>
                        <li><i class="fab fa-edge text-primary"></i> Edge 90+</li>
                    </ul>
                    <p class="text-sm text-muted mt-2">JavaScript must be enabled for full functionality.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Smooth scrolling for navigation links
$(document).ready(function() {
    $('a[href^="#"]').on('click', function(event) {
        var target = $(this.getAttribute('href'));
        if( target.length ) {
            event.preventDefault();
            $('html, body').stop().animate({
                scrollTop: target.offset().top - 100
            }, 1000);
        }
    });
});
</script>

<style>
/* Custom styles for the manual */
.card-header .card-title {
    font-weight: 600;
}

.list-group-item {
    background-color: transparent;
}

kbd {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 3px;
    color: #495057;
    font-size: 0.8em;
    padding: 2px 4px;
}

.accordion .card {
    border: 1px solid #dee2e6;
    margin-bottom: 5px;
}

.accordion .card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #dee2e6;
}

.accordion .btn-link {
    color: #495057;
    text-decoration: none;
    width: 100%;
    text-align: left;
}

.accordion .btn-link:hover {
    color: #007bff;
    text-decoration: none;
}

/* Navigation offset for smooth scrolling */
[id]:before {
    content: '';
    display: block;
    height: 100px;
    margin-top: -100px;
    visibility: hidden;
}
</style>
