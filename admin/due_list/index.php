<?php if($_settings->chk_flashdata('success')): ?>
<script>
	alert_toast("<?php echo $_settings->flashdata('success') ?>",'success')
</script>
<?php endif;?>
<div class="card card-outline rounded-0 card-navy">
	<div class="card-header">
		<h3 class="card-title">List of Due Bills</h3>
		<div class="card-tools">
			<button id="printAllDue" class="btn btn-flat btn-success"><span class="fa fa-print"></span> Print All Due</button>
		</div>
	</div>
	<div class="card-body">
        <div class="container-fluid">
			<table class="table table-hover table-striped table-bordered" id="list">
				<colgroup>
					<col width="5%">
					<col width="12%">
					<col width="20%">
					<col width="12%">
					<col width="12%">
					<col width="12%">
					<col width="12%">
					<col width="15%">
				</colgroup>
				<thead>
					<tr>
						<th>#</th>
						<th>Meter ID</th>
						<th>Name</th>
						<th>Reading Date</th>
						<th>Due Date</th>
						<th>Amount</th>
						<th>Days Overdue</th>
						<th>Action</th>
					</tr>
				</thead>
				<tbody>
					<?php 
					$i = 1;
					$current_date = date('Y-m-d');
					
					// Query to show only overdue bills (unpaid bills past their due date)
					$qry = $conn->query("SELECT b.*, c.code, c.meter_code, 
										concat(c.lastname, ', ', c.firstname, ' ', coalesce(c.middlename,'')) as `name`,
										DATEDIFF('$current_date', b.due_date) as days_overdue
										FROM `billing_list` b 
										INNER JOIN client_list c ON b.client_id = c.id 
										WHERE b.status = 0 
										AND b.due_date < '$current_date'
										AND c.delete_flag = 0
										ORDER BY b.due_date ASC");
						while($row = $qry->fetch_assoc()):
					?>
						<tr>
							<td class="text-center"><?php echo $i++; ?></td>
							<td><?php echo $row['meter_code'] ?></td>
							<td><?php echo $row['code'] ." - ".$row['name'] ?></td>
							<td><?php echo date("Y-m-d",strtotime($row['reading_date'])) ?></td>
							<td><?php echo date("Y-m-d",strtotime($row['due_date'])) ?></td>
							<td class="text-right"><?php echo "₱" . number_format($row['total'], 2) ?></td>
							<td class="text-center">
								<span class="badge badge-danger bg-gradient-danger text-sm px-3 rounded-pill">
									<?php echo $row['days_overdue'] ?> day<?php echo $row['days_overdue'] > 1 ? 's' : '' ?>
								</span>
							</td>
							<td align="center">
								 <button type="button" class="btn btn-flat p-1 btn-default btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
				                  		Action
				                    <span class="sr-only">Toggle Dropdown</span>
				                  </button>
				                  <div class="dropdown-menu" role="menu">
				                    <a class="dropdown-item view_data" href="./?page=billings/view_billing&id=<?php echo $row['id'] ?>"><span class="fa fa-eye text-dark"></span> View</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item print_data" href="javascript:void(0)" data-id="<?php echo $row['id'] ?>"><span class="fa fa-print text-primary"></span> Print</a>
				                    <div class="dropdown-divider"></div>
				                    <a class="dropdown-item edit_data" href="./?page=billings/manage_billing&id=<?php echo $row['id'] ?>"><span class="fa fa-edit text-primary"></span> Edit</a>
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
				<h5 class="modal-title" id="printModalLabel">Print Bill</h5>
				<button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
				</button>
			</div>
			<div class="modal-body">
				<div id="printContent">
					<div class="text-center">
						<i class="fa fa-spinner fa-spin fa-2x"></i>
						<p>Loading bill details...</p>
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
			_conf("Are you sure to delete this billing permanently?","delete_billing",[$(this).attr('data-id')])
		})
		
		$('.print_data').click(function(){
			var billId = $(this).attr('data-id');
			
			$('#printModal').modal('show');
			
			// Load bill details for printing
			$.ajax({
				url: _base_url_+"classes/Master.php?f=get_bill_details",
				method: 'POST',
				data: {id: billId},
				dataType: 'json',
				beforeSend: function(){
					$('#printContent').html(`
						<div class="text-center">
							<i class="fa fa-spinner fa-spin fa-2x"></i>
							<p>Loading bill details...</p>
						</div>
					`);
				},
				success: function(resp){
					if(resp.status == 'success'){
						var bill = resp.data;
						var printHtml = `
							<div class="bill-print-content">
								<div class="text-center mb-4">
									<h4>Water Billing Statement</h4>
									<p class="mb-0">Barangay Catarauan, Piat, Cagayan</p>
								</div>
								
								<div class="row mb-3">
									<div class="col-md-6">
										<strong>Bill To:</strong><br>
										${bill.client_name}<br>
										${bill.address}<br>
										Contact: ${bill.contact}
									</div>
									<div class="col-md-6 text-right">
										<strong>Bill #:</strong> ${bill.id}<br>
										<strong>Meter ID:</strong> ${bill.meter_code}<br>
										<strong>Client Code:</strong> ${bill.code}
									</div>
								</div>
								
								<table class="table table-bordered">
									<tr>
										<td><strong>Reading Date:</strong></td>
										<td>${formatDate(bill.reading_date)}</td>
									</tr>
									<tr>
										<td><strong>Due Date:</strong></td>
										<td>${formatDate(bill.due_date)}</td>
									</tr>
									<tr>
										<td><strong>Current Reading:</strong></td>
										<td>${bill.reading} cubic meters</td>
									</tr>
									<tr>
										<td><strong>Previous Reading:</strong></td>
										<td>${bill.previous} cubic meters</td>
									</tr>
									<tr>
										<td><strong>Consumption:</strong></td>
										<td>${(bill.reading - bill.previous).toFixed(2)} cubic meters</td>
									</tr>
									<tr>
										<td><strong>Rate per cubic meter:</strong></td>
										<td>₱${parseFloat(bill.rate).toFixed(2)}</td>
									</tr>
									<tr class="table-active">
										<td><strong>Total Amount:</strong></td>
										<td><strong>₱${parseFloat(bill.total).toFixed(2)}</strong></td>
									</tr>
								</table>
								
								<div class="text-center mt-4">
									<p class="text-muted">Please pay on or before the due date to avoid penalties.</p>
								</div>
							</div>
						`;
						
						$('#printContent').html(printHtml);
					} else {
						$('#printContent').html(`
							<div class="text-center text-danger">
								<i class="fa fa-exclamation-triangle fa-2x"></i>
								<p>Error loading bill details. Please try again.</p>
							</div>
						`);
					}
				},
				error: function(err){
					console.log(err);
					$('#printContent').html(`
						<div class="text-center text-danger">
							<i class="fa fa-exclamation-triangle fa-2x"></i>
							<p>Error loading bill details. Please try again.</p>
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
		
		$('#printAllDue').click(function(){
			let printWindow = window.open('', '', 'height=800,width=1200');
			let doc = printWindow.document;
			let headerHtml = `<div style='text-align:center;margin-bottom:10px;'>
		<h2 style='margin:0;'>Barangay Catarauan, Piat, Cagayan</h2>
		<h4 style='margin:0;'>WATER BILLING SYSTEM</h4>
		<p style='margin:0;'>List of Due Bills</p>
	</div>`;
			let tableHtml = `<table border='1' cellspacing='0' cellpadding='5' style='width:100%;border-collapse:collapse;'>`;
			tableHtml += `<thead><tr>
				<th>#</th>
				<th>Meter ID</th>
				<th>Name</th>
				<th>Reading Date</th>
				<th>Due Date</th>
				<th>Amount</th>
				<th>Days Overdue</th>
			</tr></thead><tbody>`;
			$('#list tbody tr').each(function(){
				let tds = $(this).find('td');
				tableHtml += '<tr>';
				for(let i=0;i<7;i++){
					tableHtml += `<td>${tds.eq(i).html()}</td>`;
				}
				tableHtml += '</tr>';
			});
			tableHtml += '</tbody></table>';
			doc.write('<html><head><title>Print All Due Bills</title>');
			doc.write('<style>table{font-size:14px;} th,td{text-align:center;}</style>');
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
					{ orderable: false, targets: [7] }  // Disable sorting for Action column
			],
			order:[6,'desc']  // Order by days overdue (descending)
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
