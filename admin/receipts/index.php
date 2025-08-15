<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline rounded-0 card-navy">
	<div class="card-header">
		<h3 class="card-title">List of Receipts</h3>
	</div>
	<div class="card-body">
        <div class="container-fluid">
			<table class="table table-hover table-striped table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="35%">
					<col width="30%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Meter ID</th>
						<th>Name</th>
						<th>Zone</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
					// Query to get clients with paid bills only
					$qry = $conn->query("SELECT DISTINCT c.id, c.code, c.meter_code, 
										concat(c.lastname, ', ', c.firstname, ' ', coalesce(c.middlename,'')) as `name`,
										c.address as zone
										FROM `client_list` c 
										INNER JOIN billing_list b ON c.id = b.client_id 
										WHERE c.delete_flag = 0 AND b.status = 1
										ORDER BY c.code ASC");
						while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo $row['meter_code'] ?></td>
							<td><?php echo $row['code'] ." - ".$row['name'] ?></td>
							<td><?php echo $row['zone'] ?></td>
							<td align="center">
								 <button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item view_receipt" href="./?page=receipts/view_receipt&id=<?php echo $row['id'] ?>"><span class="fa fa-eye text-primary"></span> View</a>
				                  </div>
							</td>
						</tr>
					<?php endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		$('#list').dataTable({
			columnDefs: [
				{ orderable: false, targets: [4] }
			],
			order:[[0,'asc']]
		});
		$('#list').dataTable()

	})
</script>
