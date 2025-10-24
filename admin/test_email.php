<?php
require_once('../config.php');
require_once('../classes/MailConfig.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once(__DIR__ . '/../libs/PHPMailer/src/Exception.php');
require_once(__DIR__ . '/../libs/PHPMailer/src/PHPMailer.php');
require_once(__DIR__ . '/../libs/PHPMailer/src/SMTP.php');

// Check if user is logged in
if(!isset($_SESSION['userdata'])){
    header("Location: ./login.php");
    exit;
}

$page_title = "Test Email Notification";
include_once('inc/header.php');
?>

<div class="card card-outline rounded-0 card-primary">
    <div class="card-header">
        <h3 class="card-title"><i class="fa fa-envelope"></i> Test Email Notification System</h3>
    </div>
    <div class="card-body">
        <div class="container-fluid">
            
            <!-- SMTP Configuration Check -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="card bg-light">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fa fa-cog"></i> Current SMTP Configuration</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm table-bordered">
                                <tr>
                                    <td width="30%"><strong>SMTP Host:</strong></td>
                                    <td><?php echo SMTP_HOST; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>SMTP Port:</strong></td>
                                    <td><?php echo SMTP_PORT; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>SMTP Username:</strong></td>
                                    <td><?php echo SMTP_USERNAME; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>SMTP Password:</strong></td>
                                    <td><?php echo str_repeat('*', strlen(SMTP_PASSWORD)); ?> (Hidden)</td>
                                </tr>
                                <tr>
                                    <td><strong>From Email:</strong></td>
                                    <td><?php echo SMTP_FROM_EMAIL; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>From Name:</strong></td>
                                    <td><?php echo SMTP_FROM_NAME; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Encryption:</strong></td>
                                    <td><?php echo strtoupper(SMTP_ENCRYPTION); ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Email Form -->
            <div class="row">
                <div class="col-md-6">
                    <div class="card card-primary">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fa fa-paper-plane"></i> Send Test Email</h5>
                        </div>
                        <div class="card-body">
                            <form id="testEmailForm">
                                <div class="form-group">
                                    <label for="testEmail">Recipient Email Address:</label>
                                    <input type="email" class="form-control" id="testEmail" name="testEmail" placeholder="Enter email address" required>
                                    <small class="form-text text-muted">Enter your email to receive a test notification</small>
                                </div>
                                <div class="form-group">
                                    <label for="recipientName">Recipient Name:</label>
                                    <input type="text" class="form-control" id="recipientName" name="recipientName" placeholder="Enter name" value="Test User">
                                </div>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fa fa-paper-plane"></i> Send Test Email
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card card-info">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fa fa-database"></i> Test with Real Bill Data</h5>
                        </div>
                        <div class="card-body">
                            <form id="testRealBillForm">
                                <div class="form-group">
                                    <label for="billingId">Select a Bill:</label>
                                    <select class="form-control select2" id="billingId" name="billingId" required>
                                        <option value="">-- Select a bill --</option>
                                        <?php
                                        $current_date = date('Y-m-d');
                                        $qry = $conn->query("SELECT b.id, b.reading_date, b.due_date, b.total,
                                                            c.code, c.meter_code, 
                                                            concat(c.lastname, ', ', c.firstname, ' ', coalesce(c.middlename,'')) as `name`
                                                            FROM `billing_list` b 
                                                            INNER JOIN client_list c ON b.client_id = c.id 
                                                            WHERE b.status = 0 
                                                            AND c.delete_flag = 0
                                                            ORDER BY b.due_date DESC LIMIT 20");
                                        while($row = $qry->fetch_assoc()):
                                        ?>
                                            <option value="<?php echo $row['id']; ?>">
                                                <?php echo $row['code'] . " - " . $row['name'] . " | Due: " . date('M d, Y', strtotime($row['due_date'])) . " | ₱" . number_format($row['total'], 2); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="testEmailReal">Send to Email:</label>
                                    <input type="email" class="form-control" id="testEmailReal" name="testEmailReal" placeholder="Enter email address" required>
                                    <small class="form-text text-muted">The email will be sent with real bill data but to your test email</small>
                                </div>
                                <button type="submit" class="btn btn-info btn-block">
                                    <i class="fa fa-file-invoice"></i> Send Test Bill Email
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Test Results -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card card-success" id="resultCard" style="display: none;">
                        <div class="card-header">
                            <h5 class="card-title mb-0"><i class="fa fa-check-circle"></i> Test Results</h5>
                        </div>
                        <div class="card-body">
                            <div id="testResults"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Instructions -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <h5><i class="icon fa fa-info-circle"></i> Instructions:</h5>
                        <ul class="mb-0">
                            <li><strong>Send Test Email:</strong> Sends a sample water bill notification to test the SMTP connection</li>
                            <li><strong>Test with Real Bill Data:</strong> Sends an actual bill notification to a test email (useful for checking template formatting)</li>
                            <li>Make sure your Gmail account has "App Passwords" enabled if using 2-factor authentication</li>
                            <li>Check your spam folder if you don't receive the email</li>
                            <li>The configuration is stored in <code>classes/MailConfig.php</code></li>
                        </ul>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
$(document).ready(function(){
    // Initialize Select2
    $('.select2').select2({
        width: '100%'
    });

    // Test Email Form
    $('#testEmailForm').submit(function(e){
        e.preventDefault();
        
        var email = $('#testEmail').val();
        var name = $('#recipientName').val();
        
        $('#resultCard').hide();
        
        Swal.fire({
            title: 'Sending Test Email...',
            html: 'Please wait while we send the test email to <strong>' + email + '</strong>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: 'test_email_handler.php',
            method: 'POST',
            data: {
                action: 'send_test',
                email: email,
                name: name
            },
            dataType: 'json',
            success: function(resp){
                Swal.close();
                
                if(resp.status == 'success'){
                    $('#testResults').html(`
                        <div class="alert alert-success">
                            <h5><i class="fa fa-check-circle"></i> Success!</h5>
                            <p>${resp.message}</p>
                            <p><strong>Sent to:</strong> ${email}</p>
                            <p><strong>Time:</strong> ${new Date().toLocaleString()}</p>
                        </div>
                    `);
                    $('#resultCard').removeClass('card-danger').addClass('card-success').show();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Email Sent!',
                        html: 'Test email has been sent successfully to <strong>' + email + '</strong><br><small>Please check your inbox (and spam folder)</small>',
                        confirmButtonColor: '#28a745'
                    });
                } else {
                    $('#testResults').html(`
                        <div class="alert alert-danger">
                            <h5><i class="fa fa-exclamation-triangle"></i> Failed!</h5>
                            <p>${resp.message}</p>
                            <p><strong>Error Details:</strong></p>
                            <pre>${resp.error || 'No additional details'}</pre>
                        </div>
                    `);
                    $('#resultCard').removeClass('card-success').addClass('card-danger').show();
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to Send Email',
                        html: resp.message,
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            error: function(err){
                Swal.close();
                console.log(err);
                
                $('#testResults').html(`
                    <div class="alert alert-danger">
                        <h5><i class="fa fa-exclamation-triangle"></i> Error!</h5>
                        <p>An unexpected error occurred while sending the email.</p>
                        <pre>${err.responseText || 'Unknown error'}</pre>
                    </div>
                `);
                $('#resultCard').removeClass('card-success').addClass('card-danger').show();
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An unexpected error occurred',
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    });

    // Test Real Bill Form
    $('#testRealBillForm').submit(function(e){
        e.preventDefault();
        
        var billingId = $('#billingId').val();
        var email = $('#testEmailReal').val();
        
        if(!billingId){
            Swal.fire({
                icon: 'warning',
                title: 'Please Select a Bill',
                text: 'You must select a bill from the dropdown',
                confirmButtonColor: '#ffc107'
            });
            return;
        }
        
        $('#resultCard').hide();
        
        Swal.fire({
            title: 'Sending Bill Email...',
            html: 'Please wait while we send the bill notification to <strong>' + email + '</strong>',
            allowOutsideClick: false,
            allowEscapeKey: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        $.ajax({
            url: 'test_email_handler.php',
            method: 'POST',
            data: {
                action: 'send_bill_test',
                billing_id: billingId,
                email: email
            },
            dataType: 'json',
            success: function(resp){
                Swal.close();
                
                if(resp.status == 'success'){
                    $('#testResults').html(`
                        <div class="alert alert-success">
                            <h5><i class="fa fa-check-circle"></i> Success!</h5>
                            <p>${resp.message}</p>
                            <p><strong>Bill ID:</strong> ${billingId}</p>
                            <p><strong>Sent to:</strong> ${email}</p>
                            <p><strong>Time:</strong> ${new Date().toLocaleString()}</p>
                        </div>
                    `);
                    $('#resultCard').removeClass('card-danger').addClass('card-success').show();
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Email Sent!',
                        html: 'Bill notification has been sent successfully to <strong>' + email + '</strong><br><small>Please check your inbox (and spam folder)</small>',
                        confirmButtonColor: '#28a745'
                    });
                } else {
                    $('#testResults').html(`
                        <div class="alert alert-danger">
                            <h5><i class="fa fa-exclamation-triangle"></i> Failed!</h5>
                            <p>${resp.message}</p>
                            <p><strong>Error Details:</strong></p>
                            <pre>${resp.error || 'No additional details'}</pre>
                        </div>
                    `);
                    $('#resultCard').removeClass('card-success').addClass('card-danger').show();
                    
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed to Send Email',
                        html: resp.message,
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            error: function(err){
                Swal.close();
                console.log(err);
                
                $('#testResults').html(`
                    <div class="alert alert-danger">
                        <h5><i class="fa fa-exclamation-triangle"></i> Error!</h5>
                        <p>An unexpected error occurred while sending the email.</p>
                        <pre>${err.responseText || 'Unknown error'}</pre>
                    </div>
                `);
                $('#resultCard').removeClass('card-success').addClass('card-danger').show();
                
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An unexpected error occurred',
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    });
});
</script>

<?php include_once('inc/footer.php'); ?>
