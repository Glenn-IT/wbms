<?php
session_start();
require_once(__DIR__ . '/../../classes/Users_public.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Initialize attempt tracking
    if (!isset($_SESSION['security_attempts'])) {
        $_SESSION['security_attempts'] = 0;
        $_SESSION['lockout_time'] = null;
    }

    // Check if user is locked out
    if ($_SESSION['lockout_time'] !== null) {
        $currentTime = time();
        $lockoutEnd = $_SESSION['lockout_time'];
        
        if ($currentTime < $lockoutEnd) {
            $remainingTime = $lockoutEnd - $currentTime;
            echo json_encode([
                'status' => 'locked',
                'message' => 'Too many failed attempts. Please wait.',
                'remaining_seconds' => $remainingTime
            ]);
            exit;
        } else {
            // Lockout expired, reset
            $_SESSION['security_attempts'] = 0;
            $_SESSION['lockout_time'] = null;
        }
    }

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
        $_SESSION['security_attempts']++;
        
        if ($_SESSION['security_attempts'] >= 3) {
            $_SESSION['lockout_time'] = time() + 30; // 30 seconds lockout
            echo json_encode([
                'status' => 'locked',
                'message' => 'Too many failed attempts. Account locked for 30 seconds.',
                'remaining_seconds' => 30
            ]);
        } else {
            $remainingAttempts = 3 - $_SESSION['security_attempts'];
            echo json_encode([
                'status' => 'error',
                'message' => 'User not found.',
                'attempts_remaining' => $remainingAttempts
            ]);
        }
        exit;
    }

    $user = $res->fetch_assoc();

    if ($user['security_question'] === $question && $user['security_answer'] === $answer) {
        // Reset attempts on success
        $_SESSION['security_attempts'] = 0;
        $_SESSION['lockout_time'] = null;
        echo json_encode(['status' => 'success', 'message' => 'Security question verified.']);
    } else {
        $_SESSION['security_attempts']++;
        
        if ($_SESSION['security_attempts'] >= 3) {
            $_SESSION['lockout_time'] = time() + 30; // 30 seconds lockout
            echo json_encode([
                'status' => 'locked',
                'message' => 'Too many failed attempts. Account locked for 30 seconds.',
                'remaining_seconds' => 30
            ]);
        } else {
            $remainingAttempts = 3 - $_SESSION['security_attempts'];
            echo json_encode([
                'status' => 'error',
                'message' => 'Incorrect security question or answer.',
                'attempts_remaining' => $remainingAttempts
            ]);
        }
    }
    exit;
}
?>
