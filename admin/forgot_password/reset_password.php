<?php
session_start();

// Include the UsersPublic class
$path = __DIR__ . '/../../classes/Users_public.php';
if (!file_exists($path)) {
    $_SESSION['message'] = "Internal error. Please contact support.";
    $_SESSION['type'] = "danger";
    header("Location: index.php");
    exit;
}
require_once($path);

// Check request method
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $api = new UsersPublic();

    // Call reset method and get result
    $result = $api->reset_password(true);

    // Set flash message based on result
    $_SESSION['message'] = $result['message'];
    $_SESSION['type'] = $result['status'] === 'success' ? 'success' : 'danger';

    header("Location: index.php");
    exit;
}
?>
