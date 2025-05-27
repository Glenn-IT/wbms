<?php
session_start();
$modalToShow = null;
$messageToShow = null;

if (isset($_SESSION['type'], $_SESSION['message'])) {
    if ($_SESSION['type'] === 'success') {
        $modalToShow = 'successModal';
    } else {
        $modalToShow = 'errorModal';
    }
    // Save message before unsetting
    $messageToShow = $_SESSION['message'];
    unset($_SESSION['message'], $_SESSION['type']);
}
?>

<!-- Include Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

<?php if ($modalToShow): ?>
<script>
    window.addEventListener('DOMContentLoaded', () => {
        const modal = new bootstrap.Modal(document.getElementById('<?php echo $modalToShow; ?>'));
        modal.show();
    });
</script>
<?php endif; ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Forgot Password</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" />
    <style>
        /* Your existing styles */
        body {
            background-color: #f4f7fa;
            font-family: Arial, sans-serif;
        }
        .forgot-password-container {
            max-width: 400px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
        .forgot-password-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .forgot-password-container .btn {
            background-color: #007bff;
            color: #fff;
            border-radius: 4px;
            font-size: 16px;
        }
        .forgot-password-container .btn:hover {
            background-color: #0056b3;
        }
        .forgot-password-container .form-group label {
            font-size: 14px;
            color: #333;
        }
        .forgot-password-container a {
            color: #007bff;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 15px;
        }
        .forgot-password-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="forgot-password-container">
        <h2>Forgot Password</h2>
        <form action="reset_password.php" method="POST" id="resetPasswordForm">
            <!-- Security Fields -->
<div class="input-group mb-3">
    <input type="text" class="form-control" name="username" id="username" placeholder="Username" required />
</div>

<div class="form-group">
    <label for="security_question">Security Question</label>
    <select name="security_question" class="form-control" id="security_question" required>
        <option value="">Select a question</option>
        <option value="pet">What is the name of your first pet?</option>
        <option value="school">What was the name of your elementary school?</option>
        <option value="mother_maiden">What is your mother's maiden name?</option>
        <option value="birth_city">In what city were you born?</option>
        <option value="nickname">What was your childhood nickname?</option>
    </select>
</div>
<br />
<div class="input-group mb-3">
    <input type="text" class="form-control" name="security_answer" id="security_answer" placeholder="Answer" required />
</div>

<!-- Verify Button -->
<div class="text-center mb-3">
    <button type="button" class="btn btn-info" id="verifySecurity">Verify</button>
</div>

<!-- Password Fields (Initially Hidden) -->
<div id="passwordFields" style="display:none;">
    <div class="input-group mb-3">
        <input type="password" class="form-control" name="new_password" id="new_password"
               placeholder="New Password" pattern="^[a-zA-Z0-9]{8,20}$"
               title="Password must be 8-20 characters and alphanumeric only" required />
        <span class="input-group-text">
            <i class="bi bi-eye-slash toggle-password" data-target="new_password" style="cursor:pointer;"></i>
        </span>
    </div>

    <div class="input-group mb-3">
        <input type="password" class="form-control" name="confirm_password" id="confirm_password"
               placeholder="Confirm Password" pattern="^[a-zA-Z0-9]{8,20}$"
               title="Password must be 8-20 characters and alphanumeric only" required />
        <span class="input-group-text">
            <i class="bi bi-eye-slash toggle-password" data-target="confirm_password" style="cursor:pointer;"></i>
        </span>
        
    </div>
    <div class="row">
                <div class="col-12" align="center">
                    <button type="submit" class="btn btn-primary btn-block">Reset Password</button>
                </div>
            </div>
</div>



           

            <div class="row mt-3">
                <a href="../login.php">Back to Login</a>
            </div>
        </form>
    </div>
</body>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="successModalLabel">Password Reset Successful</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Your password has been successfully reset.
      </div>
      <div class="modal-footer">
        <a href="../login.php" class="btn btn-success">Go to Login</a>
      </div>
    </div>
  </div>
</div>

<!-- Error Modal -->
<div class="modal fade" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="errorModalLabel">Reset Failed</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <?php echo htmlspecialchars($messageToShow ?? 'Something went wrong.'); ?>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

</html>

<script>
    document.querySelectorAll('.toggle-password').forEach(icon => {
        icon.addEventListener('click', () => {
            const targetId = icon.getAttribute('data-target');
            const input = document.getElementById(targetId);

            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            }
        });
    });
</script>
<script>
document.getElementById('verifySecurity').addEventListener('click', function () {
    const username = document.getElementById('username').value;
    const question = document.getElementById('security_question').value;
    const answer = document.getElementById('security_answer').value;

    fetch('verify_security.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `username=${encodeURIComponent(username)}&security_question=${encodeURIComponent(question)}&security_answer=${encodeURIComponent(answer)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('passwordFields').style.display = 'block';
        } else {
            alert(data.message); // Show error message
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Failed to verify. Please try again.');
    });
});
</script>


