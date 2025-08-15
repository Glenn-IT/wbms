
<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline rounded-0 card-navy">
	<div class="card-header">
		<h3 class="card-title">List of Bill</h3>
		<div class="card-tools">
			<a href="./?page=billings/manage_billing" id="create_new" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
        <div class="container-fluid">
			<table class="table table-hover table-striped table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="10%">
					<col width="20%">
					<col width="12%">
					<col width="15%">
					<col width="10%">
					<col width="13%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Reading Date</th>
						<th>Client</th>
						<th>Amount</th>
						<th>Due Date</th>
						<th>Status</th>
						<th>Due Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
					// Query to show only the most recent billing per client
					$qry = $conn->query("SELECT b.*, c.code, concat(c.lastname, ', ', c.firstname, ' ', coalesce(c.middlename,'')) as `name` 
										FROM `billing_list` b 
										INNER JOIN client_list c ON b.client_id = c.id 
										WHERE b.id IN (
											SELECT MAX(id) 
											FROM billing_list 
											GROUP BY client_id
										)
										ORDER BY b.id DESC");
						while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d",strtotime($row['reading_date'])) ?></td>
							<td><?php echo $row['code'] ." - ".$row['name'] ?></td>
							<td><?php echo format_num($row['total']) ?></td>
							<td><?php echo date("Y-m-d",strtotime($row['due_date'])) ?></td>
							<td class="text-center">
								<?php
								switch($row['status']){
									case 0:
										echo '<span class="badge badge-secondary  bg-gradient-secondary  text-sm px-3 rounded-pill">Pending</span>';
										break;
									case 1:
										echo '<span class="badge badge-success bg-gradient-success text-sm px-3 rounded-pill">Paid</span>';
										break;
								}
								?>
                            </td>
							<td class="text-center">
								<?php
								$current_date = date('Y-m-d');
								$due_date = date('Y-m-d', strtotime($row['due_date']));
								
								if($row['status'] == 1) {
									// Bill is paid
									echo '<span class="badge badge-success bg-gradient-success text-sm px-3 rounded-pill">Paid</span>';
								} elseif($current_date > $due_date) {
									// Bill is overdue
									$days_overdue = (strtotime($current_date) - strtotime($due_date)) / (60 * 60 * 24);
									echo '<span class="badge badge-danger bg-gradient-danger text-sm px-3 rounded-pill">Overdue (' . floor($days_overdue) . ' days)</span>';
								} else {
									// Bill is current/not due yet
									$days_remaining = (strtotime($due_date) - strtotime($current_date)) / (60 * 60 * 24);
									if($days_remaining <= 3) {
										echo '<span class="badge badge-warning bg-gradient-warning text-sm px-3 rounded-pill">Due Soon (' . floor($days_remaining) . ' days)</span>';
									} else {
										echo '<span class="badge badge-info bg-gradient-info text-sm px-3 rounded-pill">Current</span>';
									}
								}
								?>
                            </td>
							<td align="center">
								 <button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item view_data" href="./?page=billings/view_billing&id=<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span> View</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item view_history" href="javascript:void(0)" data-client-id="<?php echo $row['client_id'] ?>" data-client-name="<?php echo $row['code'] .' - '.$row['name'] ?>"><span class="fa fa-history text-success"></span> View History</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item input_reading" href="./?page=billings/input_reading&id=<?php echo $row['id'] ?>"><span class="fa fa-tachometer-alt text-info"></span> Input Reading</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item input_billing" href="./?page=billings/input_billing&id=<?php echo $row['id'] ?>"><span class="fa fa-calculator text-warning"></span> Input Billing</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
				                  </div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- Billing History Modal -->
<div class="modal fade" id="historyModal" tabindex="-1" role="dialog" aria-labelledby="historyModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-xl" role="document">
		<div class="modal-content">
			<div class="modal-header bg-gradient-primary text-white">
				<h5 class="modal-title" id="historyModalLabel">Billing History</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="historyContent">
					<div class="text-center">
						<i class="fa fa-spinner fa-spin fa-2x"></i>
						<p>Loading billing history...</p>
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
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this billing permanently?","delete_billing",[$(this).attr('data-id')])
		})
		
		$('.view_history').click(function(){
			var clientId = $(this).attr('data-client-id');
			var clientName = $(this).attr('data-client-name');
			
			$('#historyModalLabel').text('Billing History - ' + clientName);
			$('#historyModal').modal('show');
			
			// Load billing history
			$.ajax({
				url: _base_url_+"classes/Master.php?f=get_billing_history",
				method: 'POST',
				data: {client_id: clientId},
				dataType: 'json',
				beforeSend: function(){
					$('#historyContent').html(`
						<div class="text-center">
							<i class="fa fa-spinner fa-spin fa-2x"></i>
							<p>Loading billing history...</p>
						</div>
					`);
				},
				success: function(resp){
					if(resp.status == 'success' && resp.data.length > 0){
						var historyHtml = `
							<div class="table-responsive">
								<table class="table table-bordered table-striped table-sm">
									<thead class="bg-light">
										<tr>
											<th>#</th>
											<th>Reading Date</th>
											<th>Reading</th>
											<th>Rate</th>
											<th>Amount</th>
											<th>Due Date</th>
											<th>Status</th>
										</tr>
									</thead>
									<tbody>
						`;
						
						$.each(resp.data, function(index, bill){
							var statusBadge = bill.status == 1 ? 
								'<span class="badge badge-success">Paid</span>' :
								'<span class="badge badge-secondary">Pending</span>';
							
							historyHtml += `
								<tr>
									<td>${index + 1}</td>
									<td>${formatDate(bill.reading_date)}</td>
									<td>${bill.reading}</td>
									<td>₱${parseFloat(bill.rate).toFixed(2)}</td>
									<td>₱${parseFloat(bill.total).toFixed(2)}</td>
									<td>${formatDate(bill.due_date)}</td>
									<td>${statusBadge}</td>
								</tr>
							`;
						});
						
						historyHtml += `
									</tbody>
								</table>
							</div>
						`;
						
						$('#historyContent').html(historyHtml);
					} else {
						$('#historyContent').html(`
							<div class="text-center">
								<i class="fa fa-info-circle fa-2x text-info"></i>
								<p>No billing history found for this client.</p>
							</div>
						`);
					}
				},
				error: function(err){
					console.log(err);
					$('#historyContent').html(`
						<div class="text-center text-danger">
							<i class="fa fa-exclamation-triangle fa-2x"></i>
							<p>Error loading billing history. Please try again.</p>
						</div>
					`);
				}
			});
		});
		
		$('.table').dataTable({
			columnDefs: [
					{ orderable: false, targets: [7] }  // Updated to target the Action column (now index 7)
			],
			order:[0,'asc']
		});
		$('.dataTable td,.dataTable th').addClass('py-1 px-2 align-middle')
	})
	
	function formatDate(dateString) {
		var date = new Date(dateString);
		return date.toLocaleDateString('en-CA'); // Returns YYYY-MM-DD format
	}
	function delete_billing($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_billing",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.reload();
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>