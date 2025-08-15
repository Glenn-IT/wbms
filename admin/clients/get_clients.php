<?php
require_once('../../initialize.php');

header('Content-Type: application/json');

try {
    $clients = $conn->query("SELECT *, CONCAT(lastname, ', ', firstname, ' ', COALESCE(middlename,'')) AS fullname FROM client_list WHERE delete_flag = 0 ORDER BY date_created DESC");
    
    $data = array();
    while($row = $clients->fetch_assoc()) {
        $data[] = $row;
    }
    
    echo json_encode(array("status" => "success", "data" => $data));
} catch (Exception $e) {
    echo json_encode(array("status" => "error", "data" => array(), "message" => $e->getMessage()));
}
?>
