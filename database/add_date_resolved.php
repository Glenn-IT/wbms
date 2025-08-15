<?php
// Script to add date_resolved column to client_issue_list table
require_once('../config.php');

echo "Adding date_resolved column to client_issue_list table...\n";

// Add the new column
$sql = "ALTER TABLE `client_issue_list` ADD COLUMN `date_resolved` DATETIME NULL DEFAULT NULL AFTER `status`";

try {
    $conn->query($sql);
    echo "✓ Successfully added date_resolved column\n";
} catch(Exception $e) {
    echo "✗ Error adding column: " . $e->getMessage() . "\n";
}

// Update existing resolved issues to set their resolve date
echo "\nUpdating existing resolved issues...\n";
$update_sql = "UPDATE `client_issue_list` SET `date_resolved` = `date_updated` WHERE `status` = 1 AND `date_resolved` IS NULL";

try {
    $result = $conn->query($update_sql);
    $affected = $conn->affected_rows;
    echo "✓ Updated {$affected} existing resolved issues\n";
} catch(Exception $e) {
    echo "✗ Error updating existing records: " . $e->getMessage() . "\n";
}

echo "\nVerifying table structure...\n";
$result = $conn->query("DESCRIBE client_issue_list");
if($result) {
    echo "✓ Current table structure:\n";
    while($row = $result->fetch_assoc()) {
        echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
}

echo "\nDone!\n";
?>
