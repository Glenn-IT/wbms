<?php require_once('../config.php') ?>
<!DOCTYPE html>
<html lang="en" class="" style="height: auto;">
 <?php require_once('inc/header.php') ?>
<body class="hold-transition login-page">
  <script>
    start_loader()
  </script>
  <style>
    body{
      background-image: url("<?php echo validate_image($_settings->info('cover')) ?>");
      background-size:cover;
      background-repeat:no-repeat;
      backdrop-filter: contrast(1);
    }
    #page-title{
      text-shadow: 6px 4px 7px black;
      font-size: 3.5em;
      color: #fff4f4 !important;
      background: #8080801c;
    }
  </style>
  <h1 class="text-center text-white px-4 py-5" id="page-title"><b><?php echo $_settings->info('name') ?></b></h1>
  
  <div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-navy my-2">
      <div class="card-body">
        <p class="login-box-msg">Please essnter your credentials</p>
        
        <!-- Message display -->
        <div id="login-message" class="text-center mb-3 text-danger"></div>
        
        <form id="login-frm" action="" method="post">
          <div class="input-group mb-3">
            <input type="text" class="form-control" name="username" autofocus placeholder="Username" id="username-input">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
          <div class="input-group mb-3">
            <input type="password" class="form-control"  name="password" placeholder="Password" id="password-input">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-8">
              <!-- <a href="< ?php echo base_url ?>">Go to Website</a> -->
            </div>
            <div class="col-4">
              <button type="submit" class="btn btn-primary btn-block" id="login-button">Sign In</button>
            </div>
          </div>
        </form>
        <!-- /.social-auth-links -->

        <p class="mb-1">
          <a href="forgot_password/">I forgot my password</a>
        </p>
        
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.login-box -->

  <!-- jQuery -->
  <script src="<?= base_url ?>plugins/jquery/jquery.min.js"></script>
  <!-- Bootstrap 4 -->
  <script src="<?= base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
  <!-- AdminLTE App -->
  <script src="<?= base_url ?>dist/js/adminlte.min.js"></script>

  <script>
  $(document).ready(function(){
    end_loader();

    // Initialize login attempts count
    if(!sessionStorage.getItem('loginAttempts')) {
      sessionStorage.setItem('loginAttempts', 0);
    }

    // Initialize lock timeout timestamp
    if(!sessionStorage.getItem('lockTimeout')) {
      sessionStorage.setItem('lockTimeout', 0);
    }

    function unlockInputs(){
      $('#username-input, #password-input, #login-button').prop('disabled', false);
      $('#login-message').text('');
      sessionStorage.setItem('loginAttempts', 0);
      sessionStorage.setItem('lockTimeout', 0);
    }

    function lockInputsWithCountdown(seconds){
      let countdown = seconds;
      $('#username-input, #password-input, #login-button').prop('disabled', true);
      $('#login-message').text(`Too many failed attempts. Please wait ${countdown} seconds.`);

      // Save lock timeout timestamp
      const unlockTime = Date.now() + countdown * 1000;
      sessionStorage.setItem('lockTimeout', unlockTime);

      const interval = setInterval(() => {
        countdown--;
        if(countdown > 0){
          $('#login-message').text(`Too many failed attempts. Please wait ${countdown} seconds.`);
        } else {
          clearInterval(interval);
          unlockInputs();
        }
      }, 1000);
    }

    function checkLockStatus(){
      const lockTimeout = parseInt(sessionStorage.getItem('lockTimeout'));
      if(lockTimeout && lockTimeout > Date.now()){
        // Calculate remaining time in seconds
        let remaining = Math.ceil((lockTimeout - Date.now()) / 1000);
        lockInputsWithCountdown(remaining);
        return true;
      } else if(lockTimeout && lockTimeout <= Date.now()){
        unlockInputs();
        return false;
      }
      return false;
    }

    // Check on page load
    if(!checkLockStatus()){
      // If not locked, check attempts
      let attempts = parseInt(sessionStorage.getItem('loginAttempts'));
      if(attempts >= 3){
        lockInputsWithCountdown(30); // lock for 30 seconds
      }
    }

    $('#login-frm').submit(function(e){
      e.preventDefault();
      $('#login-message').text(''); // clear previous messages

      // Prevent submit if disabled
      if($('#login-button').prop('disabled')) return;

      $.ajax({
        url: '<?= base_url ?>classes/Login.php?f=login',
        method: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(resp){
          if(resp.status === 'success'){
            // Reset attempts on successful login
            sessionStorage.setItem('loginAttempts', 0);
            sessionStorage.setItem('lockTimeout', 0);
            window.location.href = '<?= base_url ?>admin/index.php'; // change to your post-login redirect
          }
          else if(resp.status === 'inactive'){
            $('#login-message').text('Your account is inactive. Please contact support.');
          }
          else if(resp.status === 'incorrect'){
            // Increment failed attempts
            let attempts = parseInt(sessionStorage.getItem('loginAttempts')) + 1;
            sessionStorage.setItem('loginAttempts', attempts);

            if(attempts >= 3){
              lockInputsWithCountdown(30); // lock for 30 seconds
            } else {
              $('#login-message').text('Incorrect username or password. Attempts left: ' + (3 - attempts));
            }
          }
          else{
            $('#login-message').text('Login failed. Please try again.');
          }
        },
        error: function(){
          $('#login-message').text('An error occurred. Please try again.');
        }
      });
    });
  });
</script>

</body>
</html>
