<?php 
// Get dashboard statistics
$total_clients = $conn->query("SELECT COUNT(id) as count FROM `client_list` WHERE delete_flag = 0")->fetch_assoc()['count'];
$total_categories = $conn->query("SELECT COUNT(id) as count FROM `category_list` WHERE delete_flag = 0")->fetch_assoc()['count'];
$total_bills = $conn->query("SELECT COUNT(id) as count FROM `billing_list`")->fetch_assoc()['count'];
$paid_bills = $conn->query("SELECT COUNT(id) as count FROM `billing_list` WHERE status = 1")->fetch_assoc()['count'];
$pending_bills = $conn->query("SELECT COUNT(id) as count FROM `billing_list` WHERE status = 0")->fetch_assoc()['count'];
$total_revenue = $conn->query("SELECT SUM(total) as revenue FROM `billing_list` WHERE status = 1")->fetch_assoc()['revenue'] ?? 0;
$pending_amount = $conn->query("SELECT SUM(total) as amount FROM `billing_list` WHERE status = 0")->fetch_assoc()['amount'] ?? 0;

// Get recent activities
$recent_bills = $conn->query("SELECT b.*, CONCAT(c.firstname, ' ', c.lastname) as client_name, c.code as client_code 
                             FROM billing_list b 
                             INNER JOIN client_list c ON b.client_id = c.id 
                             ORDER BY b.date_created DESC 
                             LIMIT 5");
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="callout callout-info">
                <h5><i class="fas fa-tachometer-alt"></i> Water Billing Management Dashboard</h5>
                Welcome to the admin dashboard. Here's an overview of your system.
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3><?php echo number_format($total_clients) ?></h3>
                    <p>Total Clients</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="<?php echo base_url ?>admin/?page=clients" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3><?php echo number_format($paid_bills) ?></h3>
                    <p>Paid Bills</p>
                </div>
                <div class="icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <a href="<?php echo base_url ?>admin/?page=billings" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3><?php echo number_format($pending_bills) ?></h3>
                    <p>Pending Bills</p>
                </div>
                <div class="icon">
                    <i class="fas fa-clock"></i>
                </div>
                <a href="<?php echo base_url ?>admin/?page=billings" class="small-box-footer">
                    View Details <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-3 col-6">
            <div class="small-box bg-danger">
                <div class="inner">
                    <h3>₱<?php echo number_format($total_revenue, 2) ?></h3>
                    <p>Total Revenue</p>
                </div>
                <div class="icon">
                    <i class="fas fa-peso-sign"></i>
                </div>
                <a href="<?php echo base_url ?>admin/?page=reports" class="small-box-footer">
                    View Reports <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-chart-pie mr-1"></i>
                        Billing Summary
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-success"><i class="fas fa-money-bill"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Collected</span>
                                    <span class="info-box-number">₱<?php echo number_format($total_revenue, 2) ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="info-box">
                                <span class="info-box-icon bg-warning"><i class="fas fa-exclamation-triangle"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Outstanding</span>
                                    <span class="info-box-number">₱<?php echo number_format($pending_amount, 2) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-list mr-1"></i>
                        Quick Stats
                    </h3>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="info-box">
                                <span class="info-box-icon bg-primary"><i class="fas fa-file-invoice"></i></span>
                                <div class="info-box-content">
                                    <span class="info-box-text">Total Bills</span>
                                    <span class="info-box-number"><?php echo number_format($total_bills) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activities -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock mr-1"></i>
                        Recent Billing Activities
                    </h3>
                    <div class="card-tools">
                        <a href="<?php echo base_url ?>admin/?page=billings" class="btn btn-tool">
                            <i class="fas fa-eye"></i> View All
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if($recent_bills->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Client Code</th>
                                    <th>Client Name</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($row = $recent_bills->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['client_code'] ?></td>
                                    <td><?php echo $row['client_name'] ?></td>
                                    <td>₱<?php echo number_format($row['total'], 2) ?></td>
                                    <td>
                                        <?php if($row['status'] == 1): ?>
                                            <span class="badge badge-success">Paid</span>
                                        <?php else: ?>
                                            <span class="badge badge-warning">Pending</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($row['date_created'])) ?></td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No recent billing activities found.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
