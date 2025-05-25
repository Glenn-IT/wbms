<?php
// Avoid loading config.php or initialize.php
require_once('DBConnection_public.php');

class UsersPublic extends DBConnection {

    public function __construct() {
        parent::__construct();
    }

    public function reset_password($return_array = false) {
        $username = $_POST['username'] ?? '';
        $security_question = $_POST['security_question'] ?? '';
        $security_answer = $_POST['security_answer'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';

        // Validation
        if (empty($username) || empty($security_question) || empty($security_answer) || empty($new_password) || empty($confirm_password)) {
            return ['status' => 'error', 'message' => 'All fields are required.'];
        }

        // Check if user exists
        $stmt = $this->conn->prepare("SELECT * FROM `users` WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ['status' => 'error', 'message' => 'User not found.'];
        }

        $user = $result->fetch_assoc();

        // Validate security question and answer
        if ($user['security_question'] !== $security_question || $user['security_answer'] !== $security_answer) {
            return ['status' => 'error', 'message' => 'Incorrect security question or answer.'];
        }

        // Validate password confirmation
        if ($new_password !== $confirm_password) {
            return ['status' => 'error', 'message' => 'Passwords do not match.'];
        }

        // Hash password — you should use password_hash in production
        $hashed_password = md5($new_password);

        $update = $this->conn->prepare("UPDATE users SET password = ? WHERE username = ?");
        $update->bind_param("ss", $hashed_password, $username);

        if ($update->execute()) {
            return ['status' => 'success', 'message' => 'Password reset successfully.'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to update password.'];
        }
    }
}
