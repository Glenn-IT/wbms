<?php

if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `billing_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<div class="mx-0 py-5 px-3 mx-ns-4 bg-gradient-warning">
	<h3><b><?= isset($id) ? "Input Billing - ".(isset($code) ? $code : '') : "Input Billing" ?></b></h3>
</div>
<style>
	img#cimg{
      max-height: 15em;
      width: 100%;
      object-fit: scale-down;
    }
</style>
<div class="row justify-content-center" style="margin-top:-2em;">
	<div class="col-lg-6 col-md-8 col-sm-11 col-xs-11">
		<div class="card rounded-0 shadow">
			<div class="card-body">
				<div class="container-fluid">
					<div class="container-fluid">
						<form action="" id="billing-form">
							<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
							
							<!-- Display Meter ID (readonly) -->
							<div class="form-group mb-3">
								<label for="meter_id_display" class="control-label">Meter ID</label>
								<?php 
								if(isset($client_id)){
									$client_qry = $conn->query("SELECT *, concat(lastname, ', ', firstname, ' ', coalesce(middlename)) as `name` FROM `client_list` where id = '{$client_id}'");
									$client_data = $client_qry->fetch_assoc();
								}
								?>
								<input type="text" class="form-control form-control-sm rounded-0" readonly value="<?= isset($client_data) ? $client_data['meter_code'] : '' ?>"/>
							</div>
							
							<!-- Display Client Info (readonly) -->
							<div class="form-group mb-3">
								<label for="client_display" class="control-label">Client</label>
								<input type="text" class="form-control form-control-sm rounded-0" readonly value="<?= isset($client_data) ? $client_data['code']." - ".$client_data['name'] : '' ?>"/>
								<input type="hidden" name="client_id" value="<?= isset($client_id) ? $client_id : '' ?>"/>
							</div>
							
							<!-- Display Zone (readonly) -->
							<div class="form-group mb-3">
								<label for="zone_display" class="control-label">Zone</label>
								<input type="text" class="form-control form-control-sm rounded-0" readonly value="<?= isset($client_data) ? $client_data['address'] : '' ?>"/>
							</div>
							
							<!-- Display Reading Date (readonly) -->
							<div class="form-group mb-3">
								<label for="reading_date_display" class="control-label">Reading Date</label>
								<input type="text" class="form-control form-control-sm rounded-0" readonly value="<?= isset($reading_date) ? date("Y-m-d", strtotime($reading_date)) : '' ?>"/>
								<input type="hidden" name="reading_date" value="<?= isset($reading_date) ? date("Y-m-d", strtotime($reading_date)) : '' ?>"/>
							</div>
							
							<!-- Display Previous Reading (readonly) 
							<div class="form-group mb-3">
								<label for="previous_display" class="control-label">Previous Reading</label>
								<input type="text" class="form-control form-control-sm rounded-0" readonly value="<?= isset($previous) ? $previous : '' ?>"/>
								<input type="hidden" name="previous" value="<?= isset($previous) ? $previous : '' ?>"/>
							</div>-->
							
							<!-- Display Current Reading (readonly) -->
							<div class="form-group mb-3">
								<label for="reading_display" class="control-label">Current Reading</label>
								<input type="text" class="form-control form-control-sm rounded-0" readonly value="<?= isset($reading) ? $reading : '' ?>"/>
								<input type="hidden" id="reading" name="reading" value="<?= isset($reading) ? $reading : '' ?>"/>
							</div>
							
							<!-- Editable Rate per Cubic Meter -->
							<div class="form-group mb-3">
								<label for="rate" class="control-label">Rate per Cubic Meter (m<sup>3</sup>)</label>
								<input type="text" class="form-control form-control-sm rounded-0" id="rate" name="rate" required value="<?= isset($rate) ? $rate : $_settings->info('rate') ?>"/>
							</div>
							
							<!-- Display Due Date (readonly) -->
							<div class="form-group mb-3">
								<label for="due_date_display" class="control-label">Due Date</label>
								<input type="text" class="form-control form-control-sm rounded-0" readonly value="<?= isset($due_date) ? date("Y-m-d", strtotime($due_date)) : '' ?>"/>
								<input type="hidden" name="due_date" value="<?= isset($due_date) ? date("Y-m-d", strtotime($due_date)) : '' ?>"/>
							</div>

							<!-- Display Penalty (readonly) -->
							<div class="form-group mb-3">
								<label for="penalty_display" class="control-label">Penalty</label>
								<?php 
								$penalty = 0;
								$penalty_rate = 5; // Default penalty rate percentage (5%)
								
								// Calculate penalty if due date has passed and bill is still pending
								if(isset($due_date) && isset($status) && $status == 0) {
									$current_date = date('Y-m-d');
									$due_date_formatted = date('Y-m-d', strtotime($due_date));
									
									// Calculate base amount first
									$base_amount = 0;
									if(isset($reading) && isset($previous) && isset($rate)) {
										$base_amount = ($reading - $previous) * $rate;
									}
									
									if($current_date > $due_date_formatted && $base_amount > 0) {
										$penalty = ($base_amount * $penalty_rate) / 100;
									}
								}
								?>
								<input type="text" class="form-control form-control-sm rounded-0" readonly value="<?= number_format($penalty, 2) ?>"/>
								<input type="hidden" name="penalty" value="<?= $penalty ?>"/>
								<small class="text-muted">
									<?php if($penalty > 0): ?>
										<?= $penalty_rate ?>% penalty applied for overdue payment
									<?php else: ?>
										<?= isset($status) && $status == 1 ? 'Paid - No penalty' : ($penalty == 0 ? 'No penalty (within due date)' : 'No penalty') ?>
									<?php endif; ?>
								</small>
							</div>
							
							<!-- Editable Total Bill -->
							<div class="form-group mb-3">
								<label for="total" class="control-label">Total Bill</label>
								<input type="number" step="any" class="form-control form-control-sm rounded-0 text-right" id="total" name="total" required value="<?= isset($total) ? $total : '' ?>"/>
							</div>
							
							
							
							<div class="form-group">
								<label for="status" class="control-label">Status</label>
								<select name="status" id="status" class="form-control form-control-sm rounded-0" required>
								<option value="0" <?php echo isset($status) && $status == 0 ? 'selected' : '' ?>>Pending</option>
								<option value="1" <?php echo isset($status) && $status == 1 ? 'selected' : '' ?>>Paid</option>
								</select>
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="card-footer py-1 text-center">
				<button class="btn btn-warning btn-sm bg-gradient-warning rounded-0" form="billing-form"><i class="fa fa-save"></i> Save Billing</button>
				<a class="btn btn-light btn-sm bg-gradient-light border rounded-0" href="./?page=billings"><i class="fa fa-angle-left"></i> Cancel</a>
			</div>
		</div>
	</div>
</div>
<script>
	function calc_total(){
		var current_reading = $('#reading').val()
		var previous = parseFloat('<?= isset($previous) ? $previous : 0 ?>')
		var rate = $('#rate').val()

		current_reading = current_reading > 0 ? current_reading : 0;
		rate = rate > 0 ? rate : 0;

		var base_total = (parseFloat(current_reading) - parseFloat(previous)) * parseFloat(rate);
		
		// Calculate penalty if applicable (for display only)
		var penalty = 0;
		var penalty_rate = 5; // 5% penalty rate
		var due_date = '<?= isset($due_date) ? date("Y-m-d", strtotime($due_date)) : "" ?>';
		var status = <?= isset($status) ? $status : 0 ?>;
		var current_date = new Date().toISOString().split('T')[0];
		
		if(due_date && status == 0 && current_date > due_date && base_total > 0) {
			penalty = (base_total * penalty_rate) / 100;
		}
		
		// Update penalty display (readonly field)
		$('input[readonly]').filter(function(){
			return $(this).closest('.form-group').find('label[for="penalty_display"]').length > 0;
		}).val(penalty.toFixed(2));
		
		// Update penalty hidden field for form submission
		$('input[name="penalty"]').val(penalty);
		
		// Update penalty description
		var penalty_text = '';
		if(penalty > 0) {
			penalty_text = penalty_rate + '% penalty applied for overdue payment';
		} else if(status == 1) {
			penalty_text = 'Paid - No penalty';
		} else {
			penalty_text = 'No penalty (within due date)';
		}
		$('.text-muted').text(penalty_text);

		// Total should include base amount + penalty
		$('#total').val(base_total + penalty)
	}
	
	$(document).ready(function(){
		// Calculate penalty and total on page load
		calc_total();
		
		$('#rate').on('input', function(){
			calc_total()
		})
		
		$('#status').on('change', function(){
			calc_total()
		})
		
		$('#billing-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_billing",
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
					if(typeof resp =='object' && resp.status == 'success'){
						alert_toast("Billing saved successfully",'success');
						setTimeout(function(){
							location.href = "./?page=billings"
						}, 2000);
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
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
		})

	})
</script>
