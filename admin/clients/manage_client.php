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
								<label for="address" class="control-label">Zone / Street (e.g., Zone 6, Mabini Street)</label>
								<textarea rows="3" class="form-control form-control-sm rounded-0" id="address" name="address" required="required"><?= isset($address) ? $address : '' ?></textarea>
							</div>
							<div class="form-group p-0 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
								<label for="meter_code" class="control-label">Meter ID</label>
								<input type="text" class="form-control form-control-sm rounded-0" id="meter_code" name="meter_code" value="<?= isset($meter_code) ? $meter_code : generate_meter_code($conn) ?>" readonly required="required">
							</div>
							<div class="form-group p-0 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
								<label for="first_reading" class="control-label">First Reading</label>
								<div class="input-group">
									<div class="input-group-prepend">
										<span class="input-group-text">₱</span>
									</div>
									<input type="number" class="form-control form-control-sm rounded-0" id="first_reading" name="first_reading" value="<?= isset($first_reading) ? $first_reading : '' ?>" step="0.01" min="0" required="required">
								</div>
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
                if(typeof resp == 'object' && resp.status == 'success'){
                    alert_toast("Client details saved successfully.",'success');
                    end_loader();
                    // Redirect to clients list page after successful save
                    setTimeout(function(){
                        location.href = './?page=clients';
                    }, 1500);
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
});
</script>
