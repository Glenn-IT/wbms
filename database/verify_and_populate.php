<?php
// Simple script to verify table and add sample data
require_once('../config.php');

echo "Checking table structure...\n";
$result = $conn->query("DESCRIBE client_issue_list");
if($result) {
    echo "✓ Table structure:\n";
    while($row = $result->fetch_assoc()) {
        echo "  - " . $row['Field'] . " (" . $row['Type'] . ")\n";
    }
} else {
    echo "✗ Could not describe table\n";
}

echo "\nInserting sample data...\n";

// Insert sample data
$sample_data = [
    [
        'client_id' => 14,
        'issue_title' => 'Water Meter Not Working',
        'remarks' => 'Customer reported that the water meter stopped functioning properly. Need to inspect and repair or replace.',
        'status' => 0
    ],
    [
        'client_id' => 15,
        'issue_title' => 'Billing Discrepancy',
        'remarks' => 'Customer disputes the current billing amount. Claims usage reading is incorrect.',
        'status' => 0
    ],
    [
        'client_id' => 16,
        'issue_title' => 'Leak in Connection',
        'remarks' => 'Water leak detected near the meter connection. Requires immediate attention to prevent water wastage.',
        'status' => 1
    ]
];

foreach($sample_data as $data) {
    $sql = "INSERT INTO client_issue_list (client_id, issue_title, remarks, status, date_created) VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    if($stmt) {
        $stmt->bind_param("issi", $data['client_id'], $data['issue_title'], $data['remarks'], $data['status']);
        if($stmt->execute()) {
            echo "✓ Inserted: " . $data['issue_title'] . "\n";
        } else {
            echo "✗ Error inserting: " . $data['issue_title'] . " - " . $stmt->error . "\n";
        }
        $stmt->close();
    }
}

echo "\nChecking inserted data...\n";
$result = $conn->query("SELECT COUNT(*) as count FROM client_issue_list");
if($result) {
    $row = $result->fetch_assoc();
    echo "✓ Total issues in database: " . $row['count'] . "\n";
}

echo "\nDone!\n";
?>
