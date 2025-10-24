<?php
// Test PHPMailer installation
require_once(__DIR__ . '/libs/PHPMailer/src/Exception.php');
require_once(__DIR__ . '/libs/PHPMailer/src/PHPMailer.php');
require_once(__DIR__ . '/libs/PHPMailer/src/SMTP.php');

use PHPMailer\PHPMailer\PHPMailer;

echo "PHPMailer version: " . PHPMailer::VERSION . "\n";
echo "PHPMailer installed successfully! ✓\n";
echo "\nYou can now use the email notification feature.\n";
echo "Go to: Admin → Due List → Click 'Send Email Notifications'\n";
?>
