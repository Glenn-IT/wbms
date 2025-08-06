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
							
							<!-- Display Client Info (readonly) -->
							<div class="form-group mb-3">
								<label for="client_display" class="control-label">Client</label>
								<?php 
								if(isset($client_id)){
									$client_qry = $conn->query("SELECT *, concat(lastname, ', ', firstname, ' ', coalesce(middlename)) as `name` FROM `client_list` where id = '{$client_id}'");
									$client_data = $client_qry->fetch_assoc();
								}
								?>
								<input type="text" class="form-control form-control-sm rounded-0" readonly value="<?= isset($client_data) ? $client_data['code']." - ".$client_data['name'] : '' ?>"/>
								<input type="hidden" name="client_id" value="<?= isset($client_id) ? $client_id : '' ?>"/>
							</div>
							
							<!-- Display Reading Date (readonly) -->
							<div class="form-group mb-3">
								<label for="reading_date_display" class="control-label">Reading Date</label>
								<input type="text" class="form-control form-control-sm rounded-0" readonly value="<?= isset($reading_date) ? date("Y-m-d", strtotime($reading_date)) : '' ?>"/>
								<input type="hidden" name="reading_date" value="<?= isset($reading_date) ? date("Y-m-d", strtotime($reading_date)) : '' ?>"/>
							</div>
							
							<!-- Display Previous Reading (readonly) -->
							<div class="form-group mb-3">
								<label for="previous_display" class="control-label">Previous Reading</label>
								<input type="text" class="form-control form-control-sm rounded-0" readonly value="<?= isset($previous) ? $previous : '' ?>"/>
								<input type="hidden" name="previous" value="<?= isset($previous) ? $previous : '' ?>"/>
							</div>
							
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
							
							<!-- Editable Total Bill -->
							<div class="form-group mb-3">
								<label for="total" class="control-label">Total Bill</label>
								<input type="number" step="any" class="form-control form-control-sm rounded-0 text-right" id="total" name="total" required value="<?= isset($total) ? $total : '' ?>"/>
							</div>
							
							<!-- Display Due Date (readonly) -->
							<div class="form-group mb-3">
								<label for="due_date_display" class="control-label">Due Date</label>
								<input type="text" class="form-control form-control-sm rounded-0" readonly value="<?= isset($due_date) ? date("Y-m-d", strtotime($due_date)) : '' ?>"/>
								<input type="hidden" name="due_date" value="<?= isset($due_date) ? date("Y-m-d", strtotime($due_date)) : '' ?>"/>
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

		$('#total').val((parseFloat(current_reading) - parseFloat(previous)) * parseFloat(rate))
	}
	
	$(document).ready(function(){
		$('#rate').on('input', function(){
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
