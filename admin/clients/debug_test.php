<?php
// Debug test page
echo "<h1>Debug Test Page</h1>";
echo "<p>Testing PHP syntax...</p>";

// Test database connection
require_once('../../config.php');
if($conn){
    echo "<p style='color: green;'>✓ Database connection successful</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection failed</p>";
}

// Test client list query
try {
    $test_query = $conn->query("SELECT COUNT(*) as total FROM client_list WHERE delete_flag = 0");
    if($test_query){
        $result = $test_query->fetch_assoc();
        echo "<p style='color: green;'>✓ Client list query successful. Total clients: " . $result['total'] . "</p>";
    } else {
        echo "<p style='color: red;'>✗ Client list query failed</p>";
    }
} catch(Exception $e) {
    echo "<p style='color: red;'>✗ Exception: " . $e->getMessage() . "</p>";
}

echo "<p><a href='manage_client.php'>← Back to Client Management</a></p>";
?>
