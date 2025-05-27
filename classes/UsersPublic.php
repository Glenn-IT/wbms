<?php
// Avoid loading config.php or initialize.php
require_once('DBConnection_public.php');

class UsersPublic extends DBConnection {

    public function __construct() {
        parent::__construct();
    }
   // error_log(print_r($_POST, true));

    public function reset_password($return_array = false) {
        $username = $_POST['username'] ?? '';
        $new_username = $_POST['new_username'] ?? '';
        $new_password = $_POST['new_password'] ?? '';
        $confirm_password = $_POST['confirm_password'] ?? '';
    
        $security_question1 = $_POST['security_question1'] ?? '';
        $security_answer1 = strtolower(trim($_POST['security_answer1'] ?? ''));
        $security_question2 = $_POST['security_question2'] ?? '';
        $security_answer2 = strtolower(trim($_POST['security_answer2'] ?? ''));
        $security_question3 = $_POST['security_question3'] ?? '';
        $security_answer3 = strtolower(trim($_POST['security_answer3'] ?? ''));
    
        // Validate all fields
        if (
            empty($username) || empty($new_username) ||
            empty($new_password) || empty($confirm_password) ||
            empty($security_question1) || empty($security_answer1) ||
            empty($security_question2) || empty($security_answer2) ||
            empty($security_question3) || empty($security_answer3)
        ) {
            return ['status' => 'error', 'message' => 'All fields are required.'];
        }
    
        // Validate password match
        if ($new_password !== $confirm_password) {
            return ['status' => 'error', 'message' => 'Passwords do not match.'];
        }
    
        // Check if user exists with matching username and security Q&A
        $stmt = $this->conn->prepare("
    SELECT * FROM users 
    WHERE username = ?
      AND security_question1 = ?
      AND LOWER(TRIM(security_answer1)) = LOWER(TRIM(?))
      AND security_question2 = ?
      AND LOWER(TRIM(security_answer2)) = LOWER(TRIM(?))
      AND security_question3 = ?
      AND LOWER(TRIM(security_answer3)) = LOWER(TRIM(?))
");

$stmt->bind_param(
    "sssssss",
    $username,
    $security_question1, $security_answer1,
    $security_question2, $security_answer2,
    $security_question3, $security_answer3
);

    
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows === 0) {
            return ['status' => 'error', 'message' => 'Invalid username or security answers.'];
        }
    
        $user = $result->fetch_assoc();
    
        // Store password as plain text (NOT RECOMMENDED in production)
        $plain_password = $new_password;
    
        // Update user record
        $update = $this->conn->prepare("UPDATE users SET username = ?, password = ? WHERE id = ?");
        $update->bind_param("ssi", $new_username, $plain_password, $user['id']);
    
        if ($update->execute()) {
            return ['status' => 'success', 'message' => 'Username and password updated successfully.'];
        } else {
            return ['status' => 'error', 'message' => 'Failed to update user.'];
        }
    }
    



}
