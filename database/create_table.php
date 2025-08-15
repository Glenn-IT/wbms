<?php
// Simple script to create the client_issue_list table
require_once('../config.php');

// Read the SQL file
$sql_content = file_get_contents('client_issue_table.sql');

// Split by semicolons to get individual statements
$statements = explode(';', $sql_content);

echo "Creating client_issue_list table...\n";

foreach($statements as $statement) {
    $statement = trim($statement);
    if(!empty($statement)) {
        try {
            $conn->query($statement);
            echo "✓ Executed: " . substr($statement, 0, 50) . "...\n";
        } catch(Exception $e) {
            echo "✗ Error: " . $e->getMessage() . "\n";
            echo "Statement: " . $statement . "\n";
        }
    }
}

echo "\nChecking if table was created...\n";
$result = $conn->query("SHOW TABLES LIKE 'client_issue_list'");
if($result->num_rows > 0) {
    echo "✓ Table 'client_issue_list' created successfully!\n";
    
    // Now insert sample data
    echo "\nInserting sample data...\n";
    $sample_sql = file_get_contents('sample_client_issues.sql');
    $sample_statements = explode(';', $sample_sql);
    
    foreach($sample_statements as $statement) {
        $statement = trim($statement);
        if(!empty($statement) && !preg_match('/^--/', $statement)) {
            try {
                $conn->query($statement);
                echo "✓ Sample data inserted\n";
            } catch(Exception $e) {
                echo "✗ Error inserting sample data: " . $e->getMessage() . "\n";
            }
        }
    }
} else {
    echo "✗ Table was not created!\n";
}

$conn->close();
echo "\nDone!\n";
?>
