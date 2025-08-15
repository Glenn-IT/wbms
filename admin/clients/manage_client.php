<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `client_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}


function generate_meter_code($conn) {
    $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $numbers = '0123456789';
    $random = '';

    // Generate 2 random letters
    for ($i = 0; $i < 2; $i++) {
        $random .= $characters[rand(0, strlen($characters) - 1)];
    }

    // Generate 3 random numbers
    for ($i = 0; $i < 3; $i++) {
        $random .= $numbers[rand(0, strlen($numbers) - 1)];
    }

    // Optional: ensure uniqueness (basic check against existing meter_codes)
    $check = $conn->query("SELECT COUNT(*) as count FROM client_list WHERE meter_code = '$random'");
    if ($check && $check->fetch_assoc()['count'] > 0) {
        return generate_meter_code($conn); // regenerate if duplicate
    }

    return $random;
}

?>
<div class="mx-0 py-5 px-3 mx-ns-4 bg-gradient-primary">
	<h3><b><?= isset($id) ? "Update Client Details - ".(isset($code) ? $code : '') : "Create New Client" ?></b></h3>
</div>
<style>
	img#cimg{
      max-height: 15em;
      width: 100%;
      object-fit: scale-down;
    }
</style>
<div class="row justify-content-center" style="margin-top:-2em;">
	<div class="col-lg-10 col-md-11 col-sm-11 col-xs-11">
		<div class="card rounded-0 shadow">
			<div class="card-body">
				<div class="container-fluid">
					<div class="container-fluid">
						<form action="" id="client-form">
							<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
							<?php 
							// Fetch the Residential category ID
							$residential_qry = $conn->query("SELECT id FROM `category_list` WHERE name = 'Residential' AND delete_flag = 0 AND status = 1 LIMIT 1");
							$residential_id = ($residential_qry && $residential_qry->num_rows > 0) ? $residential_qry->fetch_assoc()['id'] : '';
							if (!isset($category_id)) $category_id = $residential_id;
							?>
							<div class="form-group mb-3" style="display: none;">
								<label for="category_id" class="control-label">Category</label>
								<select name="category_id" id="category_id" class="form-control form-control-sm rounded-0" required="required">
									<option value="" disabled <?= empty($category_id) ? 'selected' : '' ?>></option>
									<?php 
									$category_qry = $conn->query("SELECT * FROM `category_list` WHERE delete_flag = 0 AND `status` = 1");
									while($row = $category_qry->fetch_assoc()):
									?>
									<option value="<?= $row['id'] ?>" <?= $category_id == $row['id'] ? 'selected' : '' ?>><?= $row['name'] ?></option>
									<?php endwhile; ?>
								</select>
							</div>

							<div class="form-group mb-3">
								<label for="firstname" class="control-label">First Name</label>
								<input type="text" class="form-control form-control-sm rounded-0" id="firstname" name="firstname" required="required" value="<?= isset($firstname) ? $firstname : '' ?>"/>
							</div>
							<div class="form-group mb-3">
								<label for="middlename" class="control-label">Middle Name</label>
								<input type="text" class="form-control form-control-sm rounded-0" id="middlename" name="middlename" placeholder="optional" value="<?= isset($middlename) ? $middlename : '' ?>"/>
							</div>
							<div class="form-group mb-3">
								<label for="lastname" class="control-label">Last Name</label>
								<input type="text" class="form-control form-control-sm rounded-0" id="lastname" name="lastname" required value="<?= isset($lastname) ? $lastname : '' ?>"/>
							</div>
							<div class="form-group mb-3">
							    <label for="contact" class="control-label">Contact #</label>
							    <input type="text" 
							           class="form-control form-control-sm rounded-0" 
							           id="contact" 
							           name="contact" 
							           required 
							           maxlength="11"
							           inputmode="numeric" 
							           pattern="\d{11}" 
							           title="Please enter exactly 11 digits"
							           value="<?= isset($contact) ? $contact : '' ?>"/>
							</div>
							<script>
							document.getElementById('contact').addEventListener('input', function () {
							    this.value = this.value.replace(/\D/g, '').slice(0, 11);
							});
							</script>

							<div class="form-group mb-3">
								<label for="address" class="control-label">Zone</label>
								<select name="address" id="address" class="form-control form-control-sm rounded-0" required="required">
									<option value="" disabled <?= empty($address) ? 'selected' : '' ?>>Select Zone</option>
									<option value="Zone 1" <?= isset($address) && $address == 'Zone 1' ? 'selected' : '' ?>>Zone 1</option>
									<option value="Zone 2" <?= isset($address) && $address == 'Zone 2' ? 'selected' : '' ?>>Zone 2</option>
									<option value="Zone 3" <?= isset($address) && $address == 'Zone 3' ? 'selected' : '' ?>>Zone 3</option>
									<option value="Zone 4" <?= isset($address) && $address == 'Zone 4' ? 'selected' : '' ?>>Zone 4</option>
									<option value="Zone 5" <?= isset($address) && $address == 'Zone 5' ? 'selected' : '' ?>>Zone 5</option>
									<option value="Zone 6" <?= isset($address) && $address == 'Zone 6' ? 'selected' : '' ?>>Zone 6</option>
								</select>
							</div>
							<div class="form-group p-0 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
								<label for="meter_code" class="control-label">Meter ID</label>
								<input type="text" class="form-control form-control-sm rounded-0" id="meter_code" name="meter_code" value="<?= isset($meter_code) ? $meter_code : generate_meter_code($conn) ?>" readonly required="required">
							</div>
							<div class="form-group p-0 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3" style="display: none;">
								<label for="first_reading" class="control-label">First Reading</label>
								<input type="number" class="form-control form-control-sm rounded-0" id="first_reading" name="first_reading" value="<?= isset($first_reading) ? $first_reading : '0' ?>" step="0.01" min="0" required="required">
							</div>
							<div class="form-group">
								<label for="status" class="control-label">Status</label>
								<select name="status" id="status" class="form-control form-control-sm rounded-0" required>
									<option value="1" <?= isset($status) && $status == 1 ? 'selected' : '' ?>>Active</option>
									<option value="2" <?= isset($status) && $status == 2 ? 'selected' : '' ?>>Inactive</option>
								</select>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="card-footer py-1 text-center">
				<button class="btn btn-primary btn-sm bg-gradient-primary rounded-0" form="client-form"><i class="fa fa-save"></i> Save</button>
				<a class="btn btn-light btn-sm bg-gradient-light border rounded-0" href="./?page=clients"><i class="fa fa-angle-left"></i> Cancel</a>
			</div>
		</div>

        <!-- CLIENT LIST TABLE -->
        <div class="card mt-3 rounded-0 shadow">
            <div class="card-header bg-gradient-secondary text-white">
                <h5 class="mb-0"><b>Client List</b></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-sm" id="client-table">
                        <thead class="bg-light">
                            <tr>
                                <th>#</th>
                                <th>Code</th>
                                <th>Client Name</th>
                                <th>Contact</th>
                                <th>Address</th>
                                <th>Meter ID</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $clients = $conn->query("SELECT *, CONCAT(lastname, ', ', firstname, ' ', COALESCE(middlename,'')) AS fullname FROM client_list WHERE delete_flag = 0 ORDER BY date_created DESC");
                            $i=1;
                            while($row = $clients->fetch_assoc()):
                            ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= $row['code'] ?></td>
                                <td><?= $row['fullname'] ?></td>
                                <td><?= $row['contact'] ?></td>
                                <td><?= $row['address'] ?></td>
                                <td><?= $row['meter_code'] ?></td>
                                <td>
                                    <?php if($row['status'] == 1): ?>
                                        <span class="badge badge-primary bg-gradient-primary">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger bg-gradient-danger">Inactive</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
	</div>
</div>

<!-- DataTables Scripts -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js"></script>

<script>
$(document).ready(function(){
    $('#category_id').select2({
        placeholder:"Please Select Here",
        containerCssClass:'form-control form-control-sm rounded-0'
    });

    $('#client-form').submit(function(e){
        e.preventDefault();
        var _this = $(this)
        $('.err-msg').remove();
        start_loader();
        $.ajax({
            url:_base_url_+"classes/Master.php?f=save_client",
            data: new FormData($(this)[0]),
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            error:err=>{
                console.log(err)
                alert_toast("An error occured",'error');
                end_loader();
            },
            success:function(resp){
                console.log('Save response:', resp);
                if(typeof resp == 'object' && resp.status == 'success'){
                    alert_toast("Client details saved successfully.",'success');
                    end_loader();
                    
                    // Add the new client to the table if it's a new client and we have client data
                    if(!$('#id').val() && resp.client_data) {
                        var table = $('#client-table').DataTable();
                        var statusBadge = resp.client_data.status == 1 ? 
                            '<span class="badge badge-primary bg-gradient-primary">Active</span>' :
                            '<span class="badge badge-danger bg-gradient-danger">Inactive</span>';
                        
                        // Add new row to the beginning of the table
                        table.row.add([
                            table.rows().count() + 1,
                            resp.client_data.code,
                            resp.client_data.fullname,
                            resp.client_data.contact,
                            resp.client_data.address,
                            resp.client_data.meter_code,
                            statusBadge
                        ]).draw(false);
                        
                        // Reorder table to show newest first (since we order by date_created DESC)
                        table.order([0, 'desc']).draw();
                    } else {
                        // For updates or if no client data, refresh the table
                        updateClientTable();
                    }
                    
                    // Clear the form for new entries
                    if(!$('#id').val()) {
                        $('#client-form')[0].reset();
                        // Generate new meter code for next client
                        generateNewMeterCode();
                        // Reset category to Residential
                        $('#category_id').val('<?= $residential_id ?>').trigger('change');
                    }
                    
                }
                else if(resp.status == 'failed' && !!resp.msg){
                    var el = $('<div>').addClass("alert alert-danger err-msg").text(resp.msg)
                    _this.prepend(el)
                    el.show('slow')
                    $("html, body, .modal").scrollTop(0)
                    end_loader()
                }else{
                    alert_toast("An error occured",'error');
                    end_loader();
                    console.log(resp)
                }
            }
        })
    });

    // Initialize DataTable
    $('#client-table').DataTable({
        "pageLength": 5,
        "lengthChange": false,
        "ordering": true,
        "info": false
    });
    
    // Function to update client table without page reload
    function updateClientTable() {
        $.ajax({
            url: "./get_clients.php",
            method: 'GET',
            dataType: 'json',
            success: function(resp) {
                console.log('Client update response:', resp);
                if(resp.status == 'success') {
                    var table = $('#client-table').DataTable();
                    table.clear();
                    
                    $.each(resp.data, function(index, client) {
                        var statusBadge = client.status == 1 ? 
                            '<span class="badge badge-primary bg-gradient-primary">Active</span>' :
                            '<span class="badge badge-danger bg-gradient-danger">Inactive</span>';
                            
                        table.row.add([
                            index + 1,
                            client.code,
                            client.fullname,
                            client.contact,
                            client.address,
                            client.meter_code,
                            statusBadge
                        ]);
                    });
                    
                    table.draw();
                } else {
                    console.log('Failed to update client table:', resp);
                }
            },
            error: function(err) {
                console.log('Error updating client table:', err);
                // Fallback: reload the page if AJAX fails
                setTimeout(function(){
                    location.reload();
                }, 1000);
            }
        });
    }
    
    // Function to generate new meter code
    function generateNewMeterCode() {
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        var numbers = '0123456789';
        var random = '';

        // Generate 2 random letters
        for (var i = 0; i < 2; i++) {
            random += characters.charAt(Math.floor(Math.random() * characters.length));
        }

        // Generate 3 random numbers
        for (var i = 0; i < 3; i++) {
            random += numbers.charAt(Math.floor(Math.random() * numbers.length));
        }

        $('#meter_code').val(random);
    }
});
</script>
