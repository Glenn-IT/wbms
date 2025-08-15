<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>

<div class="card card-outline rounded-0 card-navy">
	<div class="card-header">
		<h3 class="card-title">Billing Reports</h3>
		<div class="card-tools">
			<button type="button" id="print_report" class="btn btn-flat btn-success">
				<span class="fas fa-print"></span> Print Report
			</button>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid" id="report_content">
			<!-- Report Header -->
			<div class="row mb-3">
				<div class="col-12 text-center">
					<h4><strong><?php echo $_settings->info('name') ?></strong></h4>
					<h5>Billing Summary Report</h5>
					<p>Generated on: <?php echo date('F d, Y') ?></p>
				</div>
			</div>

			<!-- Summary Cards -->
			<div class="row mb-4">
				<?php 
				// Total Clients
				$total_clients = $conn->query("SELECT COUNT(*) as count FROM client_list WHERE delete_flag = 0")->fetch_assoc()['count'];
				
				// Total Bills This Month
				$current_month = date('Y-m');
				$bills_this_month = $conn->query("SELECT COUNT(*) as count FROM billing_list WHERE DATE_FORMAT(reading_date, '%Y-%m') = '$current_month'")->fetch_assoc()['count'];
				
				// Total Revenue This Month
				$revenue_this_month = $conn->query("SELECT SUM(total) as revenue FROM billing_list WHERE DATE_FORMAT(reading_date, '%Y-%m') = '$current_month' AND status = 1")->fetch_assoc()['revenue'] ?? 0;
				
				// Pending Payments
				$pending_payments = $conn->query("SELECT COUNT(*) as count FROM billing_list WHERE status = 0")->fetch_assoc()['count'];
				?>
				
				<div class="col-lg-3 col-md-6">
					<div class="small-box bg-info">
						<div class="inner">
							<h3><?php echo number_format($total_clients) ?></h3>
							<p>Total Clients</p>
						</div>
						<div class="icon">
							<i class="fas fa-users"></i>
						</div>
					</div>
				</div>
				
				<div class="col-lg-3 col-md-6">
					<div class="small-box bg-success">
						<div class="inner">
							<h3><?php echo number_format($bills_this_month) ?></h3>
							<p>Bills This Month</p>
						</div>
						<div class="icon">
							<i class="fas fa-file-invoice"></i>
						</div>
					</div>
				</div>
				
				<div class="col-lg-3 col-md-6">
					<div class="small-box bg-warning">
						<div class="inner">
							<h3>₱<?php echo number_format($revenue_this_month, 2) ?></h3>
							<p>Revenue This Month</p>
						</div>
						<div class="icon">
							<i class="fas fa-dollar-sign"></i>
						</div>
					</div>
				</div>
				
				<div class="col-lg-3 col-md-6">
					<div class="small-box bg-danger">
						<div class="inner">
							<h3><?php echo number_format($pending_payments) ?></h3>
							<p>Pending Payments</p>
						</div>
						<div class="icon">
							<i class="fas fa-exclamation-triangle"></i>
						</div>
					</div>
				</div>
			</div>

			<!-- Recent Billing Table -->
			<div class="row">
				<div class="col-12">
					<h5>Recent Billing Records</h5>
					<table class="table table-bordered table-striped">
						<thead>
							<tr>
								<th>Date</th>
								<th>Client</th>
								<th>Reading</th>
								<th>Amount</th>
								<th>Status</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							$recent_bills = $conn->query("SELECT b.*, concat(c.lastname, ', ', c.firstname, ' ', coalesce(c.middlename,'')) as client_name 
														 FROM billing_list b 
														 INNER JOIN client_list c ON b.client_id = c.id 
														 ORDER BY b.date_created DESC 
														 LIMIT 10");
							
							while($row = $recent_bills->fetch_assoc()):
							?>
							<tr>
								<td><?php echo date('M d, Y', strtotime($row['reading_date'])) ?></td>
								<td><?php echo $row['client_name'] ?></td>
								<td><?php echo number_format($row['reading'], 2) ?> m³</td>
								<td>₱<?php echo number_format($row['total'], 2) ?></td>
								<td>
									<?php if($row['status'] == 1): ?>
										<span class="badge badge-success">Paid</span>
									<?php else: ?>
										<span class="badge badge-warning">Pending</span>
									<?php endif; ?>
								</td>
							</tr>
							<?php endwhile; ?>
						</tbody>
					</table>
				</div>
			</div>

			<!-- Monthly Revenue Chart Data -->
			<div class="row mt-4">
				<div class="col-12">
					<h5>Monthly Revenue Summary</h5>
					<table class="table table-bordered">
						<thead>
							<tr>
								<th>Month</th>
								<th>Total Bills</th>
								<th>Paid Bills</th>
								<th>Revenue</th>
							</tr>
						</thead>
						<tbody>
							<?php 
							for($i = 5; $i >= 0; $i--):
								$month = date('Y-m', strtotime("-$i months"));
								$month_name = date('F Y', strtotime("-$i months"));
								
								$monthly_stats = $conn->query("SELECT 
													COUNT(*) as total_bills,
													SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as paid_bills,
													SUM(CASE WHEN status = 1 THEN total ELSE 0 END) as revenue
													FROM billing_list 
													WHERE DATE_FORMAT(reading_date, '%Y-%m') = '$month'")->fetch_assoc();
							?>
							<tr>
								<td><?php echo $month_name ?></td>
								<td><?php echo number_format($monthly_stats['total_bills']) ?></td>
								<td><?php echo number_format($monthly_stats['paid_bills']) ?></td>
								<td>₱<?php echo number_format($monthly_stats['revenue'], 2) ?></td>
							</tr>
							<?php endfor; ?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<style>
@media print {
	.card-tools, .main-sidebar, .main-header, .content-wrapper .content-header {
		display: none !important;
	}
	.content-wrapper {
		margin-left: 0 !important;
	}
	.card {
		border: none !important;
		box-shadow: none !important;
	}
	.small-box {
		border: 1px solid #ddd !important;
		margin-bottom: 10px !important;
	}
	.small-box .inner h3 {
		color: #000 !important;
	}
	.badge {
		border: 1px solid #000 !important;
	}
	body {
		font-size: 12px !important;
	}
}
</style>

<script>
$(document).ready(function(){
	$('#print_report').click(function(){
		var originalTitle = document.title;
		document.title = 'Billing Report - ' + '<?php echo date("M-d-Y") ?>';
		
		window.print();
		
		// Restore original title after print dialog
		setTimeout(function(){
			document.title = originalTitle;
		}, 1000);
	});
});
</script>
