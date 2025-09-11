<?php
require_once '../../config.php';
if (!isset($_GET['meter_code'])) {
    echo json_encode(['status' => 'error', 'msg' => 'No Meter ID provided']);
    exit;
}
$meter_code = trim($_GET['meter_code']);
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id > 0) {
    // Exclude current client if editing
    $qry = $conn->query("SELECT COUNT(*) as count FROM client_list WHERE meter_code = '$meter_code' AND id != $id");
} else {
    $qry = $conn->query("SELECT COUNT(*) as count FROM client_list WHERE meter_code = '$meter_code'");
}
$count = ($qry && $row = $qry->fetch_assoc()) ? $row['count'] : 0;
if ($count > 0) {
    echo json_encode(['status' => 'duplicate']);
} else {
    echo json_encode(['status' => 'unique']);
}
?>
