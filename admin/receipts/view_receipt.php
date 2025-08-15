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
?>
<div class="card card-outline rounded-0 card-primary">
	<div class="card-header">
		<h3 class="card-title">Receipt Details</h3>
		<div class="card-tools">
			<a class="btn btn-flat btn-primary" href="./?page=receipts"><span class="fas fa-angle-left"></span> Back to List</a>
		</div>
	</div>
	<div class="card-body">
		<div class="container-fluid" id="printout">
            <?php if(isset($id) && isset($billing)): ?>
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header bg-gradient-primary">
                            <h5 class="card-title text-white"><i class="fas fa-receipt"></i> Receipt Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered">
                                <tr>
                                    <th width="30%">Meter ID:</th>
                                    <td><?php echo isset($meter_code) ? $meter_code : '' ?></td>
                                </tr>
                                <tr>
                                    <th>Name:</th>
                                    <td><?php echo isset($name) ? $name : '' ?></td>
                                </tr>
                                <tr>
                                    <th>Zone:</th>
                                    <td><?php echo isset($address) ? $address : '' ?></td>
                                </tr>
                                <tr>
                                    <th>Date of Reading:</th>
                                    <td><?php echo isset($billing['reading_date']) ? date("M d, Y", strtotime($billing['reading_date'])) : '' ?></td>
                                </tr>
                                <tr>
                                    <th>Date of Payment:</th>
                                    <td><?php echo isset($billing['date_updated']) ? date("M d, Y", strtotime($billing['date_updated'])) : '' ?></td>
                                </tr>
                                <tr>
                                    <th>Due Date:</th>
                                    <td><?php echo isset($billing['due_date']) ? date("M d, Y", strtotime($billing['due_date'])) : '' ?></td>
                                </tr>
                                <tr>
                                    <th>Current Reading:</th>
                                    <td><?php echo isset($billing['reading']) ? number_format($billing['reading'], 2) : '0.00' ?></td>
                                </tr>
                                <tr>
                                    <th>Consumption:</th>
                                    <td><?php echo isset($billing['reading']) && isset($billing['previous']) ? number_format($billing['reading'] - $billing['previous'], 2) : '0.00' ?> cubic meters</td>
                                </tr>
                                <tr>
                                    <th>Rate per cubic meter:</th>
                                    <td>₱<?php echo isset($billing['rate']) ? number_format($billing['rate'], 2) : '0.00' ?></td>
                                </tr>
                                <tr>
                                    <th>Penalty:</th>
                                    <td>₱<?php echo isset($billing['penalty']) ? number_format($billing['penalty'], 2) : '0.00' ?></td>
                                </tr>
                                <tr class="bg-light">
                                    <th>Total Bills:</th>
                                    <td><strong>₱<?php echo isset($billing['total']) ? number_format($billing['total'], 2) : '0.00' ?></strong></td>
                                </tr>
                                <tr class="bg-success text-white">
                                    <th>Status:</th>
                                    <td><strong>PAID</strong></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header bg-gradient-success">
                            <h5 class="card-title text-white"><i class="fas fa-print"></i> Actions</h5>
                        </div>
                        <div class="card-body text-center">
                            <button class="btn btn-success btn-lg btn-block" type="button" id="print">
                                <i class="fas fa-print"></i> Print Receipt
                            </button>
                            <br>
                            <a href="./?page=receipts" class="btn btn-secondary btn-block">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-danger">
                <h5><i class="icon fas fa-ban"></i> Error!</h5>
                No receipt found or client has no paid bills.
            </div>
            <?php endif; ?>
		</div>
	</div>
</div>

<noscript id="print-header">
    <div style="text-align: center; margin-bottom: 20px;">
        <h2><?php echo $_settings->info('name') ?></h2>
        <h3>WATER BILL RECEIPT</h3>
        <p>Date of Printing: <?php echo date('F d, Y') ?></p>
        <hr>
    </div>
</noscript>

<script>
    <?php 
    // Function to convert number to words for JavaScript
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

    $(document).ready(function(){
        $('#print').click(function(){
            var rep = $('#printout').clone();
            var ph = $($('noscript#print-header').html()).clone();
            
            // Add print-specific styling and content
            rep.find('.card').removeClass('card').css({
                'border': '2px solid #000',
                'padding': '20px',
                'max-width': '600px',
                'margin': '0 auto',
                'background-color': '#fff'
            });
            
            rep.find('.row').css({
                'display': 'flex',
                'justify-content': 'space-between',
                'margin': '8px 0',
                'padding': '5px 0',
                'border-bottom': '1px solid #ccc'
            });
            
            rep.find('th').css({
                'font-weight': 'bold',
                'width': '40%'
            });
            
            rep.find('td').css({
                'width': '55%',
                'text-align': 'left'
            });
            
            rep.find('.card-header').remove();
            rep.find('.card-tools').remove();
            rep.find('.col-md-4').remove(); // Remove action buttons column
            rep.find('.col-md-8').removeClass('col-md-8').css('width', '100%');
            
            // Add amount in words
            <?php if(isset($billing['total'])): ?>
            var amountInWords = '<?php echo convertCurrencyToWords($billing['total']); ?>';
            rep.find('table').append('<tr class="bg-light"><th>Total Amount in Words:</th><td style="font-style: italic;">' + amountInWords + '</td></tr>');
            <?php endif; ?>
            
            // Add signature section
            var signatureSection = '<div style="margin-top: 40px; display: flex; justify-content: space-between;">' +
                                   '<div style="width: 45%; text-align: center;">' +
                                   '<div style="border-top: 1px solid #000; width: 200px; margin: 0 auto;"></div>' +
                                   '<div style="margin-top: 10px; font-size: 16px; font-weight: bold;"><?php echo isset($name) ? $name : 'N/A' ?></div>' +
                                   '</div>' +
                                   '<div style="width: 45%; text-align: center;">' +
                                   '<div style="border-top: 1px solid #000; width: 200px; margin: 0 auto;"></div>' +
                                   '<div style="margin-top: 10px; font-size: 16px; font-weight: bold;"><?php echo $_settings->userdata('firstname') . ' ' . $_settings->userdata('lastname') ?></div>' +
                                   '</div>' +
                                   '</div>';
            
            rep.append(signatureSection);
            
            // Add footer
            var footer = '<div style="text-align: center; margin-top: 30px; font-size: 12px;">' +
                        '<p>Thank you for your payment!</p>' +
                        '<p>This is an official receipt. Please keep for your records.</p>' +
                        '<p style="font-size: 10px;">Generated on <?php echo date('F d, Y g:i A') ?></p>' +
                        '</div>';
            
            rep.append(footer);
            
            var nw = window.open("", "_blank", "width=800,height=600");
            nw.document.write('<html><head><title>Receipt - <?php echo isset($name) ? $name : 'N/A' ?></title>');
            nw.document.write('<style>');
            nw.document.write('body { font-family: Arial, sans-serif; margin: 20px; }');
            nw.document.write('table { width: 100%; border-collapse: collapse; }');
            nw.document.write('th, td { padding: 8px; text-align: left; }');
            nw.document.write('.bg-light { background-color: #f8f9fa; }');
            nw.document.write('.bg-success { background-color: #28a745; color: white; }');
            nw.document.write('@media print { body { margin: 10px; } }');
            nw.document.write('</style>');
            nw.document.write('</head><body>');
            nw.document.write(ph.prop('outerHTML'));
            nw.document.write(rep.html());
            nw.document.write('</body></html>');
            nw.document.close();
            nw.print();
            setTimeout(function(){ nw.close(); }, 500);
        });
    });
</script>
