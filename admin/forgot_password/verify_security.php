<?php
session_start();
require_once(__DIR__ . '/../../classes/Users_public.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $question = $_POST['security_question'] ?? '';
    $answer = $_POST['security_answer'] ?? '';

    if (empty($username) || empty($question) || empty($answer)) {
        echo json_encode(['status' => 'error', 'message' => 'All fields are required.']);
        exit;
    }

    $db = new UsersPublic();
    $stmt = $db->conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'User not found.']);
        exit;
    }

    $user = $res->fetch_assoc();

    if ($user['security_question'] === $question && $user['security_answer'] === $answer) {
        echo json_encode(['status' => 'success', 'message' => 'Security question verified.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Incorrect security question or answer.']);
    }
    exit;
}
?>
