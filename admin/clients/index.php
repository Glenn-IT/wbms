<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline rounded-0 card-navy">
	<div class="card-header">
		<h3 class="card-title">List of Clients</h3>
		<div class="card-tools">
			<button id="printAllClients" class="btn btn-flat btn-success"><span class="fa fa-print"></span> Print All</button>
			<a href="./?page=clients/manage_client" id="create_new" class="btn btn-flat btn-primary"><span class="fas fa-plus"></span>  Create New</a>
		</div>
	</div>
	<div class="card-body">
        <div class="container-fluid">
			<table class="table table-hover table-striped table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="15%">
					<col width="25%">
					<col width="15%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Date Created</th>
						<th>Name</th>
						<th>Meter ID</th>
						<th>Status</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
						$qry = $conn->query("SELECT *,concat(lastname, ', ', firstname, ' ', coalesce(middlename,'')) as `name` from `client_list` where delete_flag = 0 order by unix_timestamp(`date_created`) desc ");
						while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo date("Y-m-d H:i",strtotime($row['date_created'])) ?></td>
							<td><?php echo $row['name'] ?></td>
							<td><?php echo $row['meter_code'] ?></td>
							<td class="text-center">
								<?php
								switch($row['status']){
									case 1:
										echo '<span class="badge badge-primary bg-gradient-primary text-sm px-3 rounded-pill">Active</span>';
										break;
									case 2:
										echo '<span class="badge badge-danger bg-gradient-danger text-sm px-3 rounded-pill">Inactive</span>';
										break;
								}
								?>
                            </td>
							<td align="center">
								 <button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item view_data" href="./?page=clients/view_client&id=<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span> View</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item print_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-print text-primary"></span> Print</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item edit_data" href="./?page=clients/manage_client&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
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

<!-- Print Modal -->
<div class="modal fade" id="printModal" tabindex="-1" role="dialog" aria-labelledby="printModalLabel" aria-hidden="true">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			<div class="modal-header bg-gradient-primary text-white">
				<h5 class="modal-title" id="printModalLabel">Print Client Details</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="printContent">
					<div class="text-center">
						<i class="fa fa-spinner fa-spin fa-2x"></i>
						<p>Loading client details...</p>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				<button type="button" class="btn btn-primary" id="printBtn"><i class="fa fa-print"></i> Print</button>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready(function(){
		$('.delete_data').click(function(){
			_conf("Are you sure to delete this client permanently?","delete_client",[$(this).attr('data-id')])
		})
		
		$('.print_data').click(function(){
			var clientId = $(this).attr('data-id');
			
			$('#printModal').modal('show');
			
			// Load client details for printing
			$.ajax({
				url: _base_url_+"classes/Master.php?f=get_client_details",
				method: 'POST',
				data: {id: clientId},
				dataType: 'json',
				beforeSend: function(){
					$('#printContent').html(`
						<div class="text-center">
							<i class="fa fa-spinner fa-spin fa-2x"></i>
							<p>Loading client details...</p>
						</div>
					`);
				},
				success: function(resp){
					if(resp.status == 'success'){
						var client = resp.data;
						var statusBadge = '';
						if(client.status == 1){
							statusBadge = '<span class="badge badge-primary">Active</span>';
						} else if(client.status == 2){
							statusBadge = '<span class="badge badge-danger">Inactive</span>';
						}
						
						var printHtml = `
							<div class="client-print-content">
								<div class="text-center mb-4">
									<h4>Client Information</h4>
									<p class="mb-0">Barangay Catarauan, Piat, Cagayan</p>
									<p class="mb-0">Water Billing Management System</p>
								</div>
								
								<table class="table table-bordered">
									<tr>
										<td width="30%"><strong>Client Code:</strong></td>
										<td>${client.code}</td>
									</tr>
									<tr>
										<td><strong>Client Name:</strong></td>
										<td>${client.name}</td>
									</tr>
									<tr>
										<td><strong>Contact Number:</strong></td>
										<td>${client.contact || 'N/A'}</td>
									</tr>
									<tr>
										<td><strong>Email Address:</strong></td>
										<td>${client.email || 'N/A'}</td>
									</tr>
									<tr>
										<td><strong>Address:</strong></td>
										<td>${client.address || 'N/A'}</td>
									</tr>
									<tr>
										<td><strong>Meter Code:</strong></td>
										<td>${client.meter_code}</td>
									</tr>
									<tr>
										<td><strong>Meter First Reading:</strong></td>
										<td>${client.first_reading} cubic meters</td>
									</tr>
									<tr>
										<td><strong>Date Created:</strong></td>
										<td>${formatDate(client.date_created)}</td>
									</tr>
									<tr>
										<td><strong>Status:</strong></td>
										<td>${statusBadge}</td>
									</tr>
								</table>
								
								<div class="text-center mt-4">
									<p class="text-muted">Printed on: ${new Date().toLocaleDateString('en-CA')} ${new Date().toLocaleTimeString()}</p>
								</div>
							</div>
						`;
						
						$('#printContent').html(printHtml);
					} else {
						$('#printContent').html(`
							<div class="text-center text-danger">
								<i class="fa fa-exclamation-triangle fa-2x"></i>
								<p>Error loading client details. Please try again.</p>
							</div>
						`);
					}
				},
				error: function(err){
					console.log(err);
					$('#printContent').html(`
						<div class="text-center text-danger">
							<i class="fa fa-exclamation-triangle fa-2x"></i>
							<p>Error loading client details. Please try again.</p>
						</div>
					`);
				}
			});
		});
		
		$('#printBtn').click(function(){
			var printContents = document.getElementById('printContent').innerHTML;
			var originalContents = document.body.innerHTML;
			
			document.body.innerHTML = printContents;
			window.print();
			document.body.innerHTML = originalContents;
			location.reload();
		});
		
		$('#printAllClients').click(function(){
			let printWindow = window.open('', '', 'height=800,width=1200');
			let doc = printWindow.document;
			let headerHtml = `<div style='text-align:center;margin-bottom:10px;'>
		<h2 style='margin:0;'>Barangay Catarauan, Piat, Cagayan</h2>
		<h4 style='margin:0;'>WATER BILLING MANAGEMENT SYSTEM</h4>
		<p style='margin:0;'>List of Clients</p>
		<p style='margin:0;font-size:12px;'>Printed on: ${new Date().toLocaleDateString('en-CA')} ${new Date().toLocaleTimeString()}</p>
	</div>`;
			let tableHtml = `<table border='1' cellspacing='0' cellpadding='5' style='width:100%;border-collapse:collapse;'>`;
			tableHtml += `<thead><tr>
				<th>#</th>
				<th>Date Created</th>
				<th>Name</th>
				<th>Meter ID</th>
				<th>Status</th>
			</tr></thead><tbody>`;
			$('#list tbody tr').each(function(){
				let tds = $(this).find('td');
				tableHtml += '<tr>';
				for(let i=0;i<5;i++){
					tableHtml += `<td>${tds.eq(i).html()}</td>`;
				}
				tableHtml += '</tr>';
			});
			tableHtml += '</tbody></table>';
			doc.write('<html><head><title>Print All Clients</title>');
			doc.write('<style>table{font-size:14px;} th,td{text-align:center;} @media print { body { margin: 20px; } }</style>');
			doc.write('</head><body>');
			doc.write(headerHtml);
			doc.write(tableHtml);
			doc.write('</body></html>');
			doc.close();
			printWindow.focus();
			setTimeout(function(){ printWindow.print(); printWindow.close(); }, 500);
		});
		
		$('.table').dataTable({
			columnDefs: [
					{ orderable: false, targets: [5] }
			],
			order:[0,'asc']
		});
		$('.dataTable td,.dataTable th').addClass('py-1 px-2 align-middle')
	})
	
	function formatDate(dateString) {
		var date = new Date(dateString);
		var options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
		return date.toLocaleDateString('en-US', options);
	}
	
	function delete_client($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_client",
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