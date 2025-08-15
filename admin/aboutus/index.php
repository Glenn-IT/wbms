<?php 
// Include any necessary configurations if needed
?>

<div class="container-fluid">
    <!-- Page Header -->
    <div class="row">
        <div class="col-12">
            <div class="callout callout-info">
                <h5><i class="fas fa-info-circle"></i> About the System</h5>
                Learn more about the Water Billing Management System and its features.
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <!-- System Overview -->
        <div class="col-lg-8">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-tint"></i> Water Billing Management System (WBMS)</h3>
                </div>
                <div class="card-body">
                    <h5>System Overview</h5>
                    <p>
                        The Water Billing Management System (WBMS) is a comprehensive web-based application designed to streamline 
                        and automate the water billing process for water utility companies, cooperatives, and municipalities. 
                        This system provides an efficient solution for managing water consumption tracking, billing generation, 
                        payment processing, and customer management.
                    </p>

                    <h5 class="mt-4">Purpose & Mission</h5>
                    <p>
                        Our mission is to provide a reliable, user-friendly, and efficient billing management solution that helps 
                        water service providers deliver better service to their customers while maintaining accurate records and 
                        improving operational efficiency. The system aims to reduce manual work, minimize errors, and provide 
                        real-time insights into billing operations.
                    </p>

                    <h5 class="mt-4">Core Features</h5>
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> <strong>Client Management:</strong> Comprehensive customer database with detailed information</li>
                                <li><i class="fas fa-check text-success"></i> <strong>Billing System:</strong> Automated billing generation and management</li>
                                <li><i class="fas fa-check text-success"></i> <strong>Reading Management:</strong> Water meter reading tracking and history</li>
                                <li><i class="fas fa-check text-success"></i> <strong>Payment Processing:</strong> Receipt generation and payment tracking</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li><i class="fas fa-check text-success"></i> <strong>Reports & Analytics:</strong> Comprehensive reporting for business insights</li>
                                <li><i class="fas fa-check text-success"></i> <strong>User Management:</strong> Role-based access control and user administration</li>
                                <li><i class="fas fa-check text-success"></i> <strong>Category Management:</strong> Flexible billing categories and rate structures</li>
                                <li><i class="fas fa-check text-success"></i> <strong>Due List Management:</strong> Outstanding payment tracking and notifications</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Technical Information -->
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-cogs"></i> Technical Specifications</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Frontend Technologies:</h6>
                            <ul class="list-unstyled ml-3">
                                <li>• HTML5 & CSS3</li>
                                <li>• Bootstrap 4 Framework</li>
                                <li>• jQuery & JavaScript</li>
                                <li>• AdminLTE Dashboard Theme</li>
                                <li>• DataTables for data presentation</li>
                                <li>• Font Awesome Icons</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6>Backend Technologies:</h6>
                            <ul class="list-unstyled ml-3">
                                <li>• PHP (Server-side scripting)</li>
                                <li>• MySQL Database</li>
                                <li>• Object-Oriented Programming</li>
                                <li>• Session Management</li>
                                <li>• File Upload Handling</li>
                                <li>• PDF Generation</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- System Statistics & Quick Info -->
        <div class="col-lg-4">
            <!-- System Info Card -->
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-server"></i> System Information</h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td><strong>System Name:</strong></td>
                            <td><?php echo $_settings->info('name') ?? 'WBMS' ?></td>
                        </tr>
                        <tr>
                            <td><strong>Version:</strong></td>
                            <td>v1.1</td>
                        </tr>
                        <tr>
                            <td><strong>Release Date:</strong></td>
                            <td>After Final Defense</td>
                        </tr>
                        
                        <tr>
                            <td><strong>Platform:</strong></td>
                            <td>Web-based</td>
                        </tr>
                        <tr>
                            <td><strong>Database:</strong></td>
                            <td>MySQL</td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Key Benefits Card -->
            <div class="card card-warning card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-star"></i> Key Benefits</h3>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item border-0 p-0 mb-2">
                            <i class="fas fa-clock text-primary"></i> <strong>Time Efficiency</strong><br>
                            <small class="text-muted">Automated billing reduces manual processing time by up to 80%</small>
                        </div>
                        <div class="list-group-item border-0 p-0 mb-2">
                            <i class="fas fa-chart-line text-success"></i> <strong>Improved Accuracy</strong><br>
                            <small class="text-muted">Eliminates calculation errors and ensures consistent billing</small>
                        </div>
                        <div class="list-group-item border-0 p-0 mb-2">
                            <i class="fas fa-users text-info"></i> <strong>Better Customer Service</strong><br>
                            <small class="text-muted">Quick access to customer information and billing history</small>
                        </div>
                        <div class="list-group-item border-0 p-0 mb-2">
                            <i class="fas fa-shield-alt text-warning"></i> <strong>Data Security</strong><br>
                            <small class="text-muted">Secure user authentication and data protection</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Additional Information -->
    <div class="row">
        <div class="col-12">
            <div class="card card-dark card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-lightbulb"></i> Getting Started</h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-users fa-3x text-primary mb-3"></i>
                                <h5>1. Manage Clients</h5>
                                <p class="text-sm">Start by adding your water service customers to the system with their complete information and connection details.</p>
                                <a href="<?php echo base_url ?>admin/?page=clients" class="btn btn-primary btn-sm">Manage Clients</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-tachometer-alt fa-3x text-success mb-3"></i>
                                <h5>2. Record Readings</h5>
                                <p class="text-sm">Input water meter readings regularly to track consumption and generate accurate billing statements.</p>
                                <a href="<?php echo base_url ?>admin/?page=billings/input_reading" class="btn btn-success btn-sm">Input Readings</a>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-file-invoice fa-3x text-warning mb-3"></i>
                                <h5>3. Generate Bills</h5>
                                <p class="text-sm">Create and manage billing statements, track payments, and generate receipts for your customers.</p>
                                <a href="<?php echo base_url ?>admin/?page=billings" class="btn btn-warning btn-sm">Manage Billing</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Requirements Modal -->
<div class="modal fade" id="systemRequirementsModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info">
                <h5 class="modal-title text-white"><i class="fas fa-desktop"></i> System Requirements</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Server Requirements:</h6>
                        <ul>
                            <li>PHP 7.4 or higher</li>
                            <li>MySQL 5.7 or higher</li>
                            <li>Apache or Nginx web server</li>
                            <li>Minimum 1GB RAM</li>
                            <li>100MB disk space</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Client Requirements:</h6>
                        <ul>
                            <li>Modern web browser (Chrome, Firefox, Safari, Edge)</li>
                            <li>JavaScript enabled</li>
                            <li>Internet connection</li>
                            <li>Screen resolution: 1024x768 minimum</li>
                            <li>PDF reader for viewing reports</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Change Log Modal -->
<div class="modal fade" id="changeLogModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success">
                <h5 class="modal-title text-white"><i class="fas fa-history"></i> Version History</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="timeline">
                    <div class="time-label">
                        <span class="bg-primary">v1.1</span>
                    </div>
                    <div>
                        <i class="fas fa-star bg-success"></i>
                        <div class="timeline-item">
                            <span class="time"><i class="fas fa-clock"></i> August 2025</span>
                            <h3 class="timeline-header">Latest Release</h3>
                            <div class="timeline-body">
                                <ul>
                                    <li>Enhanced client issue tracking system</li>
                                    <li>Improved billing report generation</li>
                                    <li>Better user interface and navigation</li>
                                    <li>Added receipt management features</li>
                                    <li>Performance optimizations</li>
                                    <li>Bug fixes and security improvements</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="time-label">
                        <span class="bg-secondary">v1.0</span>
                    </div>
                    <div>
                        <i class="fas fa-rocket bg-primary"></i>
                        <div class="timeline-item">
                            <h3 class="timeline-header">Initial Release</h3>
                            <div class="timeline-body">
                                Core functionality including client management, billing system, and basic reporting features.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script>
function showSystemRequirements() {
    $('#systemRequirementsModal').modal('show');
}

function showChangeLog() {
    $('#changeLogModal').modal('show');
}
</script>

<style>
.timeline {
    position: relative;
    margin: 0 0 30px 0;
    padding: 0;
    list-style: none;
}

.timeline:before {
    content: '';
    position: absolute;
    top: 0;
    left: 25px;
    height: 100%;
    width: 4px;
    background: #ddd;
}

.timeline > .time-label > span {
    font-weight: 600;
    color: #fff;
    font-size: 16px;
    padding: 5px 10px;
    display: inline-block;
    border-radius: 4px;
    margin-left: 15px;
}

.timeline > div {
    margin: 10px 0;
}

.timeline > div > .fa,
.timeline > div > .fas,
.timeline > div > .far,
.timeline > div > .fab,
.timeline > div > .fal,
.timeline > div > .fad {
    width: 30px;
    height: 30px;
    font-size: 15px;
    line-height: 30px;
    position: absolute;
    color: #666;
    background: #ddd;
    border-radius: 50%;
    text-align: center;
    left: 10px;
    top: 0;
}

.timeline > div > .timeline-item {
    background: #fff;
    border-radius: 3px;
    width: calc(100% - 50px);
    margin-left: 50px;
    padding: 10px;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.timeline-header {
    margin: 0;
    color: #555;
    border-bottom: 1px solid #f4f4f4;
    padding: 5px 0;
    font-size: 16px;
    line-height: 1.1;
}

.timeline-body {
    padding: 10px 0;
}

.time {
    color: #999;
    float: right;
    font-size: 12px;
}
</style>
