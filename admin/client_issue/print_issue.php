<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Print Issue Details</title>
    <link rel="stylesheet" href="<?php echo base_url ?>plugins/bootstrap/css/bootstrap.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            .form-control-plaintext { border-bottom: 1px solid #000 !important; }
            .border { border: 1px solid #000 !important; }
            body { font-size: 12px; }
        }
        .form-group { margin-bottom: 15px; }
        .control-label { font-weight: bold; }
        .company-header { 
            text-align: center; 
            margin-bottom: 30px; 
            border-bottom: 2px solid #000; 
            padding-bottom: 15px; 
        }
        .issue-header {
            background-color: #f8f9fa;
            padding: 15px;
            border: 1px solid #000;
            margin-bottom: 20px;
        }
        .print-btn {
            position: fixed;
            top: 10px;
            right: 10px;
            z-index: 1000;
        }
    </style>
</head>
<body>
    <button onclick="window.print()" class="btn btn-primary print-btn no-print">
        <i class="fas fa-print"></i> Print
    </button>

    <?php
    require_once('../../config.php');

    $id = $_GET['id'] ?? 0;
    $qry = $conn->query("SELECT ci.*, cl.meter_code, concat(cl.lastname, ', ', cl.firstname, ' ', coalesce(cl.middlename,'')) as `name`, cl.address, cl.contact
                        FROM `client_issue_list` ci 
                        INNER JOIN `client_list` cl ON ci.client_id = cl.id 
                        WHERE ci.id = '{$id}'");
    $row = $qry->fetch_assoc();

    if(!$row) {
        echo "<div class='alert alert-danger'>Issue not found!</div>";
        exit;
    }
    ?>

    <div class="container-fluid">
        <div class="company-header">
            <h2>Water Billing Management System</h2>
            <h3>Client Issue Report</h3>
            <p>Generated on: <?php echo date('F d, Y H:i:s') ?></p>
        </div>

        <div class="issue-header">
            <div class="row">
                <div class="col-md-6">
                    <h4>Issue ID: ISS-<?php echo str_pad($row['id'], 4, '0', STR_PAD_LEFT) ?></h4>
                    <p><strong>Issue Title:</strong> <?php echo htmlspecialchars($row['issue_title']) ?></p>
                </div>
                <div class="col-md-6 text-right">
                    <?php 
                    switch($row['status']){
                        case 0:
                            echo '<span class="badge badge-warning" style="font-size: 1.2em;">PENDING</span>';
                            break;
                        case 1:
                            echo '<span class="badge badge-success" style="font-size: 1.2em;">RESOLVED</span>';
                            break;
                    }
                    ?>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Meter ID:</label>
                    <div class="form-control-plaintext border-bottom"><?php echo $row['meter_code'] ?? 'N/A' ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Client Name:</label>
                    <div class="form-control-plaintext border-bottom"><?php echo $row['name'] ?? 'N/A' ?></div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Zone/Address:</label>
                    <div class="form-control-plaintext border-bottom"><?php echo $row['address'] ?? 'N/A' ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label">Contact:</label>
                    <div class="form-control-plaintext border-bottom"><?php echo $row['contact'] ?? 'N/A' ?></div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label">Remarks:</label>
                    <div class="form-control-plaintext border p-3" style="min-height: 100px; background-color: #f8f9fa;">
                        <?php echo nl2br(htmlspecialchars($row['remarks'] ?? 'No remarks provided.')) ?>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if(!empty($row['image_path']) && file_exists('../../uploads/issues/'.$row['image_path'])): ?>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="control-label">Attached Image:</label>
                    <div class="border p-3 text-center" style="background-color: #f8f9fa;">
                        <img src="<?php echo base_url ?>uploads/issues/<?php echo $row['image_path'] ?>" 
                             alt="Issue Image" 
                             class="img-fluid" 
                             style="max-height: 400px; border-radius: 5px;">
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Date Created:</label>
                    <div class="form-control-plaintext border-bottom"><?php echo date("F d, Y H:i", strtotime($row['date_created'])) ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Last Updated:</label>
                    <div class="form-control-plaintext border-bottom"><?php echo date("F d, Y H:i", strtotime($row['date_updated'])) ?></div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label class="control-label">Date Resolved:</label>
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

        <div class="row mt-4 no-print">
            <div class="col-md-12 text-center">
                <button onclick="window.print()" class="btn btn-primary btn-lg">
                    <i class="fas fa-print"></i> Print This Report
                </button>
                <button onclick="window.close()" class="btn btn-secondary btn-lg ml-2">
                    <i class="fas fa-times"></i> Close
                </button>
            </div>
        </div>
    </div>

    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
        
        // Auto close after printing
        window.onafterprint = function() {
            // window.close(); // Uncomment if you want auto-close after print
        }
    </script>
</body>
</html>
