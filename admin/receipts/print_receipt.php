<?php
if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT c.*, concat(c.lastname, ', ', c.firstname, ' ', coalesce(c.middlename,'')) as `name` FROM `client_list` c WHERE c.id = '{$_GET['id']}' AND c.delete_flag = 0");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k = $v;
        }
        
        // Get latest paid billing
        $billing_qry = $conn->query("SELECT * FROM `billing_list` WHERE client_id = '{$_GET['id']}' AND status = 1 ORDER BY id DESC LIMIT 1");
        if($billing_qry->num_rows > 0){
            $billing = $billing_qry->fetch_assoc();
        }
    }
}

// Function to convert number to words
function numberToWords($number) {
    $ones = array(
        0 => '', 1 => 'One', 2 => 'Two', 3 => 'Three', 4 => 'Four', 5 => 'Five',
        6 => 'Six', 7 => 'Seven', 8 => 'Eight', 9 => 'Nine', 10 => 'Ten',
        11 => 'Eleven', 12 => 'Twelve', 13 => 'Thirteen', 14 => 'Fourteen', 15 => 'Fifteen',
        16 => 'Sixteen', 17 => 'Seventeen', 18 => 'Eighteen', 19 => 'Nineteen'
    );
    
    $tens = array(
        0 => '', 2 => 'Twenty', 3 => 'Thirty', 4 => 'Forty', 5 => 'Fifty',
        6 => 'Sixty', 7 => 'Seventy', 8 => 'Eighty', 9 => 'Ninety'
    );
    
    if ($number < 20) {
        return $ones[$number];
    } elseif ($number < 100) {
        return $tens[floor($number / 10)] . ' ' . $ones[$number % 10];
    } elseif ($number < 1000) {
        return $ones[floor($number / 100)] . ' Hundred ' . numberToWords($number % 100);
    } elseif ($number < 1000000) {
        return numberToWords(floor($number / 1000)) . ' Thousand ' . numberToWords($number % 1000);
    }
    
    return number_format($number);
}

function convertCurrencyToWords($amount) {
    $amount = number_format($amount, 2, '.', '');
    $parts = explode('.', $amount);
    $pesos = (int)$parts[0];
    $centavos = (int)$parts[1];
    
    $result = numberToWords($pesos) . ' Pesos';
    if ($centavos > 0) {
        $result .= ' and ' . numberToWords($centavos) . ' Centavos';
    }
    
    return trim($result);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - <?php echo isset($name) ? $name : 'N/A' ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #fff;
        }
        .receipt-container {
            max-width: 600px;
            margin: 0 auto;
            border: 2px solid #000;
            padding: 20px;
            background-color: #fff;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .system-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .receipt-title {
            font-size: 24px;
            font-weight: bold;
            margin: 10px 0;
            text-decoration: underline;
        }
        .print-date {
            font-size: 12px;
            margin-top: 10px;
        }
        .content {
            margin: 20px 0;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            padding: 5px 0;
        }
        .row.bordered {
            border-bottom: 1px solid #ccc;
        }
        .label {
            font-weight: bold;
            width: 40%;
        }
        .value {
            width: 55%;
            text-align: left;
        }
        .amount {
            font-size: 18px;
            font-weight: bold;
        }
        .total-section {
            border-top: 2px solid #000;
            border-bottom: 2px solid #000;
            padding: 10px 0;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
        }
        .signature-section {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }
        .signature-box {
            width: 45%;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-top: 30px;
            padding-top: 5px;
        }
        @media print {
            body {
                margin: 0;
                padding: 10px;
            }
            .receipt-container {
                border: 2px solid #000;
                max-width: none;
            }
        }
    </style>
</head>
<body>
    <?php if(isset($id) && isset($billing)): ?>
    <div class="receipt-container">
        <div class="header">
            <div class="system-name"><?php echo $_settings->info('name') ?></div>
            <div class="receipt-title">WATER BILL RECEIPT</div>
            <div class="print-date">Date of Printing: <?php echo date('F d, Y') ?></div>
        </div>
        
        <div class="content">
            <div class="row bordered">
                <div class="label">Meter ID:</div>
                <div class="value"><?php echo isset($meter_code) ? $meter_code : 'N/A' ?></div>
            </div>
            
            <div class="row bordered">
                <div class="label">Name:</div>
                <div class="value"><?php echo isset($name) ? $name : 'N/A' ?></div>
            </div>
            
            <div class="row bordered">
                <div class="label">Zone:</div>
                <div class="value"><?php echo isset($address) ? $address : 'N/A' ?></div>
            </div>
            
            <div class="row bordered">
                <div class="label">Date of Reading:</div>
                <div class="value"><?php echo isset($billing['reading_date']) ? date("F d, Y", strtotime($billing['reading_date'])) : 'N/A' ?></div>
            </div>
            
            <div class="row bordered">
                <div class="label">Date of Payment:</div>
                <div class="value"><?php echo isset($billing['date_updated']) ? date("F d, Y", strtotime($billing['date_updated'])) : 'N/A' ?></div>
            </div>
            
            <div class="row bordered">
                <div class="label">Due Date:</div>
                <div class="value"><?php echo isset($billing['due_date']) ? date("F d, Y", strtotime($billing['due_date'])) : 'N/A' ?></div>
            </div>
            
            <?php 
            $due_amount = 0;
            $current_date = date('Y-m-d');
            $due_date = isset($billing['due_date']) ? date('Y-m-d', strtotime($billing['due_date'])) : '';
            $payment_date = isset($billing['date_updated']) ? date('Y-m-d', strtotime($billing['date_updated'])) : '';
            
            // Check if payment was made after due date
            if($payment_date > $due_date && isset($billing['penalty']) && $billing['penalty'] > 0) {
                $due_amount = $billing['penalty'];
            }
            ?>
            
            <?php if($due_amount > 0): ?>
            <div class="row bordered">
                <div class="label">Due Date Amount:</div>
                <div class="value amount">₱<?php echo number_format($due_amount, 2) ?></div>
            </div>
            <?php endif; ?>
            
            <div class="total-section">
                <div class="row">
                    <div class="label">Total Amount:</div>
                    <div class="value amount">₱<?php echo isset($billing['total']) ? number_format($billing['total'], 2) : '0.00' ?></div>
                </div>
                
                <div class="row">
                    <div class="label">Total Amount in Words:</div>
                    <div class="value" style="font-style: italic;">
                        <?php echo isset($billing['total']) ? convertCurrencyToWords($billing['total']) : 'Zero Pesos' ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="label">Payment Status:</div>
                <div class="value" style="color: green; font-weight: bold;">PAID IN FULL</div>
            </div>
        </div>
        
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line">
                    Customer Signature
                </div>
            </div>
            <div class="signature-box">
                <div class="signature-line">
                    Authorized Signature
                </div>
            </div>
        </div>
        
        <div class="footer">
            <p>Thank you for your payment!</p>
            <p>This is an official receipt. Please keep for your records.</p>
            <p style="font-size: 10px;">Generated on <?php echo date('F d, Y g:i A') ?></p>
        </div>
    </div>
    
    <script>
        window.onload = function() {
            window.print();
        }
    </script>
    
    <?php else: ?>
    <div class="receipt-container">
        <div class="header">
            <div class="receipt-title">ERROR</div>
        </div>
        <div class="content">
            <p style="text-align: center; color: red;">No receipt found or client has no paid bills.</p>
        </div>
    </div>
    <?php endif; ?>
</body>
</html>
