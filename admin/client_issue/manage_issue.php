<?php
require_once('../../config.php');

$id = $_GET['id'] ?? '';
$is_edit = !empty($id);

if($is_edit){
    $qry = $conn->query("SELECT ci.*, cl.meter_code, concat(cl.lastname, ', ', cl.firstname, ' ', coalesce(cl.middlename,'')) as `name`
                        FROM `client_issue_list` ci 
                        INNER JOIN `client_list` cl ON ci.client_id = cl.id 
                        WHERE ci.id = '{$id}'");
    $row = $qry->fetch_assoc();
}
?>

<div class="container-fluid">
    <?php if($is_edit): ?>
    <div class="alert alert-info mb-3">
        <h6 class="mb-0">
            <i class="fas fa-edit"></i> 
            Editing Issue ID: <strong>ISS-<?php echo str_pad($id, 4, '0', STR_PAD_LEFT) ?></strong>
        </h6>
    </div>
    <?php endif; ?>
    
    <form action="" id="issue-form" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?php echo $id ?>">
        
        <?php if(!$is_edit): ?>
        <div class="form-group">
            <label for="client_id" class="control-label">Select Client <span class="text-danger">*</span></label>
            <select name="client_id" id="client_id" class="form-control select2" required>
                <option value="">Select Client</option>
                <?php 
                $client_qry = $conn->query("SELECT id, meter_code, concat(lastname, ', ', firstname, ' ', coalesce(middlename,'')) as `name`, address 
                                          FROM `client_list` 
                                          WHERE delete_flag = 0 AND status = 1 
                                          ORDER BY lastname, firstname");
                while($client = $client_qry->fetch_assoc()):
                ?>
                <option value="<?php echo $client['id'] ?>"><?php echo $client['meter_code'] ?> - <?php echo $client['name'] ?> (<?php echo $client['address'] ?>)</option>
                <?php endwhile; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="issue_title" class="control-label">Issue Title <span class="text-danger">*</span></label>
            <input type="text" name="issue_title" id="issue_title" class="form-control" required placeholder="Enter issue title">
        </div>
        <?php else: ?>
        <div class="form-group">
            <label class="control-label">Client Information</label>
            <div class="form-control-plaintext border-bottom">
                <strong><?php echo $row['meter_code'] ?></strong> - <?php echo $row['name'] ?>
            </div>
        </div>
        
        <div class="form-group">
            <label class="control-label">Issue Title</label>
            <div class="form-control-plaintext border-bottom"><?php echo $row['issue_title'] ?></div>
        </div>
        <?php endif; ?>
        
        <div class="form-group">
            <label for="remarks" class="control-label">Remarks</label>
            <textarea name="remarks" id="remarks" cols="30" rows="4" class="form-control" placeholder="Enter remarks or additional details about the issue"><?php echo $is_edit ? $row['remarks'] : '' ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="image" class="control-label">Upload Image</label>
            <input type="file" name="image" id="image" class="form-control-file" accept="image/*">
            <?php if($is_edit && !empty($row['image_path'])): ?>
            <div class="mt-2">
                <small class="text-muted">Current image:</small><br>
                <img src="<?php echo base_url ?>uploads/issues/<?php echo $row['image_path'] ?>" 
                     alt="Current Image" 
                     class="img-thumbnail" 
                     style="max-height: 150px;">
                <div class="custom-control custom-checkbox mt-2">
                    <input type="checkbox" class="custom-control-input" id="remove_image" name="remove_image" value="1">
                    <label class="custom-control-label" for="remove_image">Remove current image</label>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if(!$is_edit): ?>
        <div class="form-group">
            <label for="status" class="control-label">Initial Status</label>
            <select name="status" id="status" class="form-control">
                <option value="0">Pending</option>
                <option value="1">Resolved</option>
            </select>
        </div>
        <?php endif; ?>
    </form>
</div>

<script>
$(document).ready(function(){
    $('.select2').select2({
        placeholder:"Please select here",
        width:"relative"
    });
    
    $('#issue-form').submit(function(e){
        e.preventDefault();
        var _this = $(this);
        $('.err-msg').remove();
        start_loader();
        
        var formData = new FormData(this);
        
        $.ajax({
            url:_base_url_+"classes/Master.php?f=save_issue",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            method: 'POST',
            type: 'POST',
            dataType: 'json',
            error:err=>{
                console.log(err);
                alert_toast("An error occurred",'error');
                end_loader();
            },
            success:function(resp){
                if(typeof resp== 'object' && resp.status == 'success'){
                    location.reload();
                }else if(resp.status == 'failed' && !!resp.msg){
                    var el = $('<div>');
                    el.addClass("alert alert-danger err-msg").text(resp.msg);
                    _this.prepend(el);
                    el.show('slow');
                    $("html, body").scrollTop(0);
                    end_loader();
                }else{
                    alert_toast("An error occurred",'error');
                    end_loader();
                    console.log(resp);
                }
            }
        });
    });
});
</script>
