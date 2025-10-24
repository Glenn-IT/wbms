<?php
require_once('../config.php');
require_once('MailConfig.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include PHPMailer autoload (make sure PHPMailer is installed via Composer or manually)
require_once(__DIR__ . '/../libs/PHPMailer/src/Exception.php');
require_once(__DIR__ . '/../libs/PHPMailer/src/PHPMailer.php');
require_once(__DIR__ . '/../libs/PHPMailer/src/SMTP.php');

class MailNotification {
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    /**
     * Send due date reminder to a single client
     */
    public function sendDueDateReminder($billing_id) {
        // Get billing and client details
        $qry = $this->conn->query("SELECT b.*, c.code, c.meter_code, c.email,
                                   concat(c.firstname, ' ', coalesce(c.middlename,''), ' ', c.lastname) as `name`,
                                   DATEDIFF(CURDATE(), b.due_date) as days_overdue
                                   FROM `billing_list` b 
                                   INNER JOIN client_list c ON b.client_id = c.id 
                                   WHERE b.id = '$billing_id' AND c.delete_flag = 0");
        
        if($qry->num_rows == 0) {
            return ['status' => 'error', 'message' => 'Billing record not found'];
        }
        
        $bill = $qry->fetch_assoc();
        
        if(empty($bill['email'])) {
            return ['status' => 'error', 'message' => 'Client email not found'];
        }
        
        return $this->sendEmail($bill);
    }
    
    /**
     * Send due date reminders to all overdue clients
     */
    public function sendAllDueReminders() {
        $current_date = date('Y-m-d');
        $qry = $this->conn->query("SELECT b.*, c.code, c.meter_code, c.email,
                                   concat(c.firstname, ' ', coalesce(c.middlename,''), ' ', c.lastname) as `name`,
                                   DATEDIFF('$current_date', b.due_date) as days_overdue
                                   FROM `billing_list` b 
                                   INNER JOIN client_list c ON b.client_id = c.id 
                                   WHERE b.status = 0 
                                   AND b.due_date < '$current_date'
                                   AND c.delete_flag = 0
                                   AND c.email IS NOT NULL
                                   AND c.email != ''
                                   ORDER BY b.due_date ASC");
        
        $sent = 0;
        $failed = 0;
        $errors = [];
        
        while($bill = $qry->fetch_assoc()) {
            $result = $this->sendEmail($bill);
            if($result['status'] == 'success') {
                $sent++;
            } else {
                $failed++;
                $errors[] = $bill['name'] . ': ' . $result['message'];
            }
        }
        
        return [
            'status' => 'success',
            'sent' => $sent,
            'failed' => $failed,
            'errors' => $errors
        ];
    }
    
    /**
     * Send email using PHPMailer
     */
    private function sendEmail($bill) {
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
            $mail->addAddress($bill['email'], $bill['name']);
            
            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Water Bill Payment Reminder - Barangay Catarauan';
            
            $consumption = $bill['reading'] - $bill['previous'];
            $mail->Body = $this->getEmailTemplate($bill, $consumption);
            $mail->AltBody = $this->getEmailPlainText($bill, $consumption);
            
            $mail->send();
            return ['status' => 'success', 'message' => 'Email sent successfully'];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => "Email could not be sent. Mailer Error: {$mail->ErrorInfo}"];
        }
    }
    
    /**
     * HTML Email Template
     */
    private function getEmailTemplate($bill, $consumption) {
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
                    <p>This is an automated message. Please do not reply to this email.</p>
                    <p>&copy; " . date('Y') . " Water Billing System - Barangay Catarauan</p>
                </div>
            </div>
        </body>
        </html>
        ";
    }
    
    /**
     * Plain Text Email (for email clients that don't support HTML)
     */
    private function getEmailPlainText($bill, $consumption) {
        $daysOverdue = $bill['days_overdue'];
        $statusMessage = $daysOverdue > 0 ? 
            "WARNING: Your bill is {$daysOverdue} day(s) overdue!\n\n" :
            "Your bill is due soon. Please pay on or before the due date.\n\n";
        
        return "
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
This is an automated message. Please do not reply to this email.
© " . date('Y') . " Water Billing System - Barangay Catarauan
        ";
    }
}

// Handle AJAX requests
if(isset($_POST['action'])) {
    $mail = new MailNotification();
    
    if($_POST['action'] == 'send_single') {
        $result = $mail->sendDueDateReminder($_POST['billing_id']);
        echo json_encode($result);
    }
    
    if($_POST['action'] == 'send_all') {
        $result = $mail->sendAllDueReminders();
        echo json_encode($result);
    }
}
?>
