<?php
require_once('../../config.php');

$id = $_GET['id'] ?? 0;
$qry = $conn->query("SELECT ci.*, cl.meter_code, concat(cl.lastname, ', ', cl.firstname, ' ', coalesce(cl.middlename,'')) as `name`, cl.address, cl.contact
                    FROM `client_issue_list` ci 
                    INNER JOIN `client_list` cl ON ci.client_id = cl.id 
                    WHERE ci.id = '{$id}'");
$row = $qry->fetch_assoc();
?>

<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-12">
            <div class="alert alert-info">
                <h5 class="mb-0">
                    <i class="fas fa-ticket-alt"></i> 
                    Issue ID: <strong>ISS-<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></strong>
                    <span class="float-right">
                        <?php 
                        switch($row['status']){
                            case 0:
                                echo '<span class="badge badge-warning">Pending</span>';
                                break;
                            case 1:
                                echo '<span class="badge badge-success">Resolved</span>';
                                break;
                        }
                        ?>
                    </span>
                </h5>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="meter_id" class="control-label">Meter ID:</label>
                <div class="form-control-plaintext border-bottom"><?php echo $row['meter_code'] ?? 'N/A' ?></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="client_name" class="control-label">Name:</label>
                <div class="form-control-plaintext border-bottom"><?php echo $row['name'] ?? 'N/A' ?></div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="zone" class="control-label">Zone:</label>
                <div class="form-control-plaintext border-bottom"><?php echo $row['address'] ?? 'N/A' ?></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="contact" class="control-label">Contact:</label>
                <div class="form-control-plaintext border-bottom"><?php echo $row['contact'] ?? 'N/A' ?></div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label for="issue_title" class="control-label">Issue Title:</label>
                <div class="form-control-plaintext border-bottom"><?php echo $row['issue_title'] ?? 'N/A' ?></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label for="status" class="control-label">Status:</label>
                <div class="form-control-plaintext border-bottom">
                    <?php 
                    switch($row['status']){
                        case 0:
                            echo '<span class="badge badge-warning">Pending</span>';
                            break;
                        case 1:
                            echo '<span class="badge badge-success">Resolved</span>';
                            break;
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label for="remarks" class="control-label">Remarks:</label>
                <div class="form-control-plaintext border p-3" style="min-height: 100px; background-color: #f8f9fa;">
                    <?php echo nl2br($row['remarks'] ?? 'No remarks provided.') ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">Image:</label>
                <div class="border p-3" style="min-height: 200px; background-color: #f8f9fa;">
                    <?php if(!empty($row['image_path']) && file_exists('../../uploads/issues/'.$row['image_path'])): ?>
                        <img src="<?php echo base_url ?>uploads/issues/<?php echo $row['image_path'] ?>" 
                             alt="Issue Image" 
                             class="img-fluid" 
                             style="max-height: 300px; border-radius: 5px;">
                    <?php else: ?>
                        <div class="text-center text-muted py-5">
                            <i class="fas fa-image fa-3x mb-3"></i>
                            <p>No image uploaded</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-4">
            <div class="form-group">
                <label for="date_created" class="control-label">Date Created:</label>
                <div class="form-control-plaintext border-bottom"><?php echo date("F d, Y H:i", strtotime($row['date_created'])) ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="date_updated" class="control-label">Last Updated:</label>
                <div class="form-control-plaintext border-bottom"><?php echo date("F d, Y H:i", strtotime($row['date_updated'])) ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <label for="date_resolved" class="control-label">Date Resolved:</label>
                <div class="form-control-plaintext border-bottom">
                    <?php 
                    if($row['status'] == 1 && !empty($row['date_resolved'])){
                        echo '<span class="text-success">' . date("F d, Y H:i", strtotime($row['date_resolved'])) . '</span>';
                    } else {
                        echo '<span class="text-muted">Not resolved yet</span>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    $('#print_btn').click(function(){
        var printContents = $('#uni_modal .modal-body').html();
        var originalContents = document.body.innerHTML;
        
        var printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <html>
            <head>
                <title>Issue Details - ISS-<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></title>
                <link rel="stylesheet" href="<?php echo base_url ?>plugins/bootstrap/css/bootstrap.min.css">
                <style>
                    @media print {
                        .form-control-plaintext { border-bottom: 1px solid #000 !important; }
                        .border { border: 1px solid #000 !important; }
                        body { font-size: 12px; }
                    }
                    .form-group { margin-bottom: 15px; }
                    .control-label { font-weight: bold; }
                </style>
            </head>
            <body>
                <div class="container">
                    <h3 class="text-center mb-4">Client Issue Details</h3>
                    <h4 class="text-center mb-4">Issue ID: ISS-<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></h4>
                    ${printContents}
                </div>
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    });
});
</script>
