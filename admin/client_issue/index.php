<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline rounded-0 card-navy">
	<div class="card-header">
		<h3 class="card-title">Client Issue Management</h3>
		<div class="card-tools">
			<a href="javascript:void(0)" id="create_new" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span> Create New Issue</a>
		</div>
	</div>
	<div class="card-body">
        <div class="container-fluid">
			<table class="table table-hover table-striped table-bordered" id="list">
				<colgroup>
					<col width="8%">
					<col width="10%">
					<col width="20%">
					<col width="12%">
					<col width="12%">
					<col width="12%">
					<col width="26%">
				</colgroup>
				<thead>
					<tr>
						<th>Issue ID</th>
						<th>Meter ID</th>
						<th>Name</th>
						<th>Issue Status</th>
						<th>Date Created</th>
						<th>Date Resolved</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$qry = $conn->query("SELECT ci.*, cl.meter_code, concat(cl.lastname, ', ', cl.firstname, ' ', coalesce(cl.middlename,'')) as `name`, cl.address 
										FROM `client_issue_list` ci 
										INNER JOIN `client_list` cl ON ci.client_id = cl.id 
										WHERE cl.delete_flag = 0 
										ORDER BY ci.date_created DESC");
					while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center">
								<span class="badge badge-light bg-gradient-light text-dark font-weight-bold">
									ISS-<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?>
								</span>
							</td>
							<td><?php echo $row['meter_code'] ?></td>
							<td><?php echo $row['name'] ?></td>
							<td class="text-center">
								<?php
								switch($row['status']){
									case 0:
										echo '<span class="badge badge-warning bg-gradient-warning text-sm px-3 rounded-pill">Pending</span>';
										break;
									case 1:
										echo '<span class="badge badge-success bg-gradient-success text-sm px-3 rounded-pill">Resolved</span>';
										break;
								}
								?>
							</td>
							<td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td class="text-center">
								<?php 
								if($row['status'] == 1 && !empty($row['date_resolved'])){
									echo '<span class="text-success">' . date("Y-m-d H:i",strtotime($row['date_resolved'])) . '</span>';
								} else {
									echo '<span class="text-muted">—</span>';
								}
								?>
							</td>
							<td align="center">
								<button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
									Action
									<span class="sr-only">Toggle Dropdown</span>
								</button>
								<div class="dropdown-menu" role="menu">
									<a class="dropdown-item view_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-eye text-primary"></span> View</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item edit_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
									<div class="dropdown-divider"></div>
									<a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-trash text-danger"></span> Delete</a>
									<div class="dropdown-divider"></div>
									<?php if($row['status'] == 0): ?>
									<a class="dropdown-item resolve_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-check text-success"></span> Mark as Resolved</a>
									<?php else: ?>
									<a class="dropdown-item pending_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-clock text-warning"></span> Mark as Pending</a>
									<?php endif; ?>
								</div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<!-- View Modal -->
<div class="modal fade" id="view_modal" tabindex="-1" role="dialog" aria-labelledby="view_modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="view_modalLabel">Issue Details</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="view_content">
				<!-- Content will be loaded here -->
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-info" id="print_btn"><i class="fa fa-print"></i> Print</button>
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Back</button>
			</div>
		</div>
	</div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="edit_modal" tabindex="-1" role="dialog" aria-labelledby="edit_modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="edit_modalLabel">Edit Issue</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="edit_content">
				<!-- Content will be loaded here -->
			</div>
		</div>
	</div>
</div>

<!-- Create New Issue Modal -->
<div class="modal fade" id="create_modal" tabindex="-1" role="dialog" aria-labelledby="create_modalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="create_modalLabel">Create New Issue</h5>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body" id="create_content">
				<!-- Content will be loaded here -->
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		$('#list').dataTable({
			columnDefs: [
				{ orderable: false, targets: [6] }
			],
			order: [0, 'desc']
		});
		
		$('.view_data').click(function(){
			uni_modal("Issue Details","client_issue/view_issue.php?id="+$(this).attr('data-id'),"large");
		});
		
		$('.edit_data').click(function(){
			uni_modal("Edit Issue","client_issue/manage_issue.php?id="+$(this).attr('data-id'),"large");
		});
		
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this Issue permanently?","delete_issue",[$(this).attr('data-id')]);
		});
		
		$('.resolve_data').click(function(){
			_conf("Are you sure to mark this issue as resolved?","resolve_issue",[$(this).attr('data-id')]);
		});
		
		$('.pending_data').click(function(){
			_conf("Are you sure to mark this issue as pending?","pending_issue",[$(this).attr('data-id')]);
		});
		
		$('#create_new').click(function(){
			uni_modal("Create New Issue","client_issue/manage_issue.php","large");
		});
	});
	
	function delete_issue($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_issue",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err);
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
		});
	}
	
	function resolve_issue($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=update_issue_status",
			method:"POST",
			data:{id: $id, status: 1},
			dataType:"json",
			error:err=>{
				console.log(err);
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
		});
	}
	
	function pending_issue($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=update_issue_status",
			method:"POST",
			data:{id: $id, status: 0},
			dataType:"json",
			error:err=>{
				console.log(err);
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
		});
	}
</script>
