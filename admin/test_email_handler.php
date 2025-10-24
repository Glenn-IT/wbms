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
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

if(!isset($_POST['action'])){
    echo json_encode(['status' => 'error', 'message' => 'No action specified']);
    exit;
}

/**
 * Send a test email with sample data
 */
function sendTestEmail($recipientEmail, $recipientName) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port       = SMTP_PORT;
        
        // Enable verbose debug output (optional - comment out in production)
        // $mail->SMTPDebug = 2;
        
        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($recipientEmail, $recipientName);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Test Email - Water Billing System';
        
        $mail->Body = getTestEmailTemplate($recipientName);
        $mail->AltBody = getTestEmailPlainText($recipientName);
        
        $mail->send();
        return [
            'status' => 'success', 
            'message' => 'Test email sent successfully! Please check your inbox.',
            'email' => $recipientEmail
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error', 
            'message' => 'Failed to send test email',
            'error' => $mail->ErrorInfo
        ];
    }
}

/**
 * Send a real bill notification to a test email
 */
function sendBillTestEmail($billingId, $testEmail) {
    global $conn;
    
    // Get billing and client details
    $qry = $conn->query("SELECT b.*, c.code, c.meter_code, c.email,
                       concat(c.firstname, ' ', coalesce(c.middlename,''), ' ', c.lastname) as `name`,
                       DATEDIFF(CURDATE(), b.due_date) as days_overdue
                       FROM `billing_list` b 
                       INNER JOIN client_list c ON b.client_id = c.id 
                       WHERE b.id = '$billingId' AND c.delete_flag = 0");
    
    if($qry->num_rows == 0) {
        return ['status' => 'error', 'message' => 'Billing record not found'];
    }
    
    $bill = $qry->fetch_assoc();
    
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USERNAME;
        $mail->Password   = SMTP_PASSWORD;
        $mail->SMTPSecure = SMTP_ENCRYPTION;
        $mail->Port       = SMTP_PORT;
        
        // Recipients
        $mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
        $mail->addAddress($testEmail, $bill['name']); // Send to test email but with real client name
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = '[TEST] Water Bill Payment Reminder - Barangay Catarauan';
        
        $consumption = $bill['reading'] - $bill['previous'];
        $mail->Body = getBillEmailTemplate($bill, $consumption);
        $mail->AltBody = getBillEmailPlainText($bill, $consumption);
        
        $mail->send();
        return [
            'status' => 'success',
            'message' => 'Test bill email sent successfully! Check your inbox.',
            'email' => $testEmail,
            'bill_id' => $billingId
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => 'Failed to send test bill email',
            'error' => $mail->ErrorInfo
        ];
    }
}

/**
 * Test Email HTML Template
 */
function getTestEmailTemplate($recipientName) {
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #007bff; color: white; padding: 20px; text-align: center; }
            .content { background-color: #f9f9f9; padding: 30px; }
            .success-box { background-color: #d4edda; border: 2px solid #28a745; padding: 20px; margin: 20px 0; border-radius: 5px; }
            .info-box { background-color: #d1ecf1; border: 1px solid #0c5460; padding: 15px; margin: 15px 0; border-radius: 5px; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
            .check-icon { color: #28a745; font-size: 48px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>✓ Email Test Successful</h2>
                <p>Water Billing System - Barangay Catarauan</p>
            </div>
            
            <div class='content'>
                <div class='success-box' style='text-align: center;'>
                    <h1 style='color: #28a745; margin: 0;'>✓</h1>
                    <h3 style='margin: 10px 0;'>Email System is Working!</h3>
                </div>
                
                <p>Dear <strong>{$recipientName}</strong>,</p>
                
                <p>Congratulations! If you're reading this email, it means your Water Billing System email notification is configured correctly and working perfectly.</p>
                
                <div class='info-box'>
                    <h4 style='margin-top: 0;'>✓ What's Working:</h4>
                    <ul>
                        <li>SMTP Connection: Successfully connected to Gmail</li>
                        <li>Authentication: Login credentials verified</li>
                        <li>Email Delivery: Message delivered to inbox</li>
                        <li>HTML Formatting: Email template rendering correctly</li>
                    </ul>
                </div>
                
                <h4>Next Steps:</h4>
                <ol>
                    <li>You can now use the email notification feature in the Due List page</li>
                    <li>Test sending a real bill notification using the second test form</li>
                    <li>When ready, use the 'Send Email Notifications' button to notify clients</li>
                </ol>
                
                <div style='background-color: #fff3cd; border: 1px solid #ffc107; padding: 15px; margin: 20px 0; border-radius: 5px;'>
                    <p style='margin: 0;'><strong>⚠️ Note:</strong> This is a test email. In production, this system will send payment reminders to clients with overdue water bills.</p>
                </div>
                
                <p style='margin-top: 30px;'>Test completed successfully at <strong>" . date('F d, Y h:i:s A') . "</strong></p>
            </div>
            
            <div class='footer'>
                <p>This is an automated test message from the Water Billing System.</p>
                <p>&copy; " . date('Y') . " Water Billing System - Barangay Catarauan</p>
            </div>
        </div>
    </body>
    </html>
    ";
}

/**
 * Test Email Plain Text
 */
function getTestEmailPlainText($recipientName) {
    return "
EMAIL TEST SUCCESSFUL
Water Billing System - Barangay Catarauan

Dear {$recipientName},

Congratulations! If you're reading this email, it means your Water Billing System email notification is configured correctly and working perfectly.

WHAT'S WORKING:
✓ SMTP Connection: Successfully connected to Gmail
✓ Authentication: Login credentials verified
✓ Email Delivery: Message delivered to inbox
✓ HTML Formatting: Email template rendering correctly

NEXT STEPS:
1. You can now use the email notification feature in the Due List page
2. Test sending a real bill notification using the second test form
3. When ready, use the 'Send Email Notifications' button to notify clients

NOTE: This is a test email. In production, this system will send payment reminders to clients with overdue water bills.

Test completed successfully at " . date('F d, Y h:i:s A') . "

---
This is an automated test message from the Water Billing System.
© " . date('Y') . " Water Billing System - Barangay Catarauan
    ";
}

/**
 * Real Bill Email HTML Template
 */
function getBillEmailTemplate($bill, $consumption) {
    $daysOverdue = $bill['days_overdue'];
    $statusMessage = $daysOverdue > 0 ? 
        "<p style='color: red; font-weight: bold;'>⚠️ Your bill is <strong>{$daysOverdue} day(s) overdue!</strong></p>" :
        "<p style='color: orange;'>Your bill is due soon. Please pay on or before the due date.</p>";
    
    return "
    <!DOCTYPE html>
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #007bff; color: white; padding: 20px; text-align: center; }
            .test-badge { background-color: #ffc107; color: #000; padding: 10px; text-align: center; font-weight: bold; }
            .content { background-color: #f9f9f9; padding: 20px; }
            .bill-details { background-color: white; padding: 15px; margin: 20px 0; border: 1px solid #ddd; }
            .bill-details table { width: 100%; }
            .bill-details td { padding: 8px; border-bottom: 1px solid #eee; }
            .bill-details td:first-child { font-weight: bold; width: 40%; }
            .total { background-color: #007bff; color: white; font-size: 18px; font-weight: bold; padding: 15px; text-align: center; }
            .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='test-badge'>
                ⚠️ THIS IS A TEST EMAIL - For Testing Purposes Only
            </div>
            
            <div class='header'>
                <h2>Water Billing System</h2>
                <p>Barangay Catarauan, Piat, Cagayan</p>
            </div>
            
            <div class='content'>
                <h3>Dear {$bill['name']},</h3>
                {$statusMessage}
                <p>This is a friendly reminder about your water billing statement.</p>
                
                <div class='bill-details'>
                    <table>
                        <tr>
                            <td>Client Code:</td>
                            <td>{$bill['code']}</td>
                        </tr>
                        <tr>
                            <td>Meter ID:</td>
                            <td>{$bill['meter_code']}</td>
                        </tr>
                        <tr>
                            <td>Reading Date:</td>
                            <td>" . date('F d, Y', strtotime($bill['reading_date'])) . "</td>
                        </tr>
                        <tr>
                            <td>Due Date:</td>
                            <td>" . date('F d, Y', strtotime($bill['due_date'])) . "</td>
                        </tr>
                        <tr>
                            <td>Current Reading:</td>
                            <td>{$bill['reading']} m³</td>
                        </tr>
                        <tr>
                            <td>Previous Reading:</td>
                            <td>{$bill['previous']} m³</td>
                        </tr>
                        <tr>
                            <td>Consumption:</td>
                            <td>" . number_format($consumption, 2) . " m³</td>
                        </tr>
                        <tr>
                            <td>Rate per m³:</td>
                            <td>₱" . number_format($bill['rate'], 2) . "</td>
                        </tr>
                    </table>
                </div>
                
                <div class='total'>
                    TOTAL AMOUNT DUE: ₱" . number_format($bill['total'], 2) . "
                </div>
                
                <p><strong>Payment Instructions:</strong></p>
                <ul>
                    <li>Please pay at the Barangay Hall during office hours (8:00 AM - 5:00 PM)</li>
                    <li>Present this email or mention your Client Code: {$bill['code']}</li>
                    <li>For inquiries, please contact the barangay office</li>
                </ul>
                
                <p style='margin-top: 30px;'>Thank you for your prompt payment!</p>
            </div>
            
            <div class='footer'>
                <p>This is an automated TEST message. Please do not reply to this email.</p>
                <p>&copy; " . date('Y') . " Water Billing System - Barangay Catarauan</p>
            </div>
        </div>
    </body>
    </html>
    ";
}

/**
 * Real Bill Plain Text Email
 */
function getBillEmailPlainText($bill, $consumption) {
    $daysOverdue = $bill['days_overdue'];
    $statusMessage = $daysOverdue > 0 ? 
        "WARNING: Your bill is {$daysOverdue} day(s) overdue!\n\n" :
        "Your bill is due soon. Please pay on or before the due date.\n\n";
    
    return "
*** THIS IS A TEST EMAIL - For Testing Purposes Only ***

WATER BILLING SYSTEM
Barangay Catarauan, Piat, Cagayan

Dear {$bill['name']},

{$statusMessage}

BILLING STATEMENT
-----------------
Client Code: {$bill['code']}
Meter ID: {$bill['meter_code']}
Reading Date: " . date('F d, Y', strtotime($bill['reading_date'])) . "
Due Date: " . date('F d, Y', strtotime($bill['due_date'])) . "
Current Reading: {$bill['reading']} m³
Previous Reading: {$bill['previous']} m³
Consumption: " . number_format($consumption, 2) . " m³
Rate per m³: ₱" . number_format($bill['rate'], 2) . "

TOTAL AMOUNT DUE: ₱" . number_format($bill['total'], 2) . "

PAYMENT INSTRUCTIONS:
- Please pay at the Barangay Hall during office hours (8:00 AM - 5:00 PM)
- Present this email or mention your Client Code: {$bill['code']}
- For inquiries, please contact the barangay office

Thank you for your prompt payment!

---
This is an automated TEST message. Please do not reply to this email.
© " . date('Y') . " Water Billing System - Barangay Catarauan
    ";
}

// Handle Actions
if($_POST['action'] == 'send_test') {
    $email = $_POST['email'];
    $name = $_POST['name'];
    
    $result = sendTestEmail($email, $name);
    echo json_encode($result);
}

if($_POST['action'] == 'send_bill_test') {
    $billingId = $_POST['billing_id'];
    $email = $_POST['email'];
    
    $result = sendBillTestEmail($billingId, $email);
    echo json_encode($result);
}
?>
