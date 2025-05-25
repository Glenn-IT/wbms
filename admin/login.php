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
      <p class="login-box-msg">Please enter your credentials</p>
      <form id="login-frm" action="" method="post">
        <div class="input-group mb-3">
          <input type="text" class="form-control" name="username" autofocus placeholder="Username">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-user"></span>
            </div>
          </div>
        </div>
        <div class="input-group mb-3">
          <input type="password" class="form-control"  name="password" placeholder="Password">
          <div class="input-group-append">
            <div class="input-group-text">
              <span class="fas fa-lock"></span>
            </div>
          </div>
        </div>

<div id="login-error" class="text-danger text-sm mb-3" style="display: none;"></div>


        <div class="row">
          <div class="col-8">
            <!-- <a href="< ?php echo base_url ?>">Go to Website</a> -->
          </div>
          <!-- /.col -->
          <div class="col-4">
            <button type="submit" class="btn btn-primary btn-block">Sign In</button>
          </div>
          <!-- /.col -->
        </div>
      </form>
      <!-- /.social-auth-links -->

       <p class="mb-1">
        <a href="forgot_password/index.php">I forgot my password</a>
      </p> 
      
    </div>
    <!-- /.card-body -->
  </div>
  <!-- /.card -->
</div>
<!-- /.login-box -->

<!-- Stylish, Non-Dismissable Lockout Modal -->
<div class="modal fade" id="lockoutModal" tabindex="-1" role="dialog"
     aria-labelledby="lockoutModalLabel" aria-hidden="true"
     data-backdrop="static" data-keyboard="false">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content border-0 shadow-lg rounded-lg">
      <div class="modal-header bg-danger text-white rounded-top">
        <h5 class="modal-title w-100 text-center m-0" id="lockoutModalLabel">
          <i class="fas fa-exclamation-triangle mr-2"></i>Too Many Attempts
        </h5>
      </div>
      <div class="modal-body text-center">
        <p class="mb-2">You’ve entered incorrect credentials too many times.</p>
        <p class="mb-3">Please wait <span id="countdown" class="font-weight-bold text-danger">30</span> seconds before trying again.</p>
        <div class="spinner-border text-danger mt-3" role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div>
    </div>
  </div>
</div>




<!-- jQuery -->
<script src="<?= base_url ?>plugins/jquery/jquery.min.js"></script>
<!-- Bootstrap 4 -->
<script src="<?= base_url ?>plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
<!-- AdminLTE App -->
<script src="<?= base_url ?>dist/js/adminlte.min.js"></script>

<script>
  $(document).ready(function(){
    end_loader();
  })
</script>


<script>
  const LOCKOUT_DURATION = 30; // seconds

  let failedAttempts = parseInt(localStorage.getItem('failedAttempts')) || 0;
  let lockoutUntil = parseInt(localStorage.getItem('lockoutUntil')) || null;

  function showLockoutModal(secondsLeft) {
    $('#countdown').text(secondsLeft);
    $('#lockoutModal').modal('show');
  }

  function startLockout() {
    const now = Math.floor(Date.now() / 1000);
    const unlockTime = now + LOCKOUT_DURATION;
    localStorage.setItem('lockoutUntil', unlockTime);
    showLockoutModal(LOCKOUT_DURATION);

    const interval = setInterval(() => {
      const now = Math.floor(Date.now() / 1000);
      const remaining = unlockTime - now;
      $('#countdown').text(remaining);

      if (remaining <= 0) {
        clearInterval(interval);
        $('#lockoutModal').modal('hide');
        localStorage.removeItem('failedAttempts');
        localStorage.removeItem('lockoutUntil');
        failedAttempts = 0;
      }
    }, 1000);
  }

  function checkLockoutOnLoad() {
    const now = Math.floor(Date.now() / 1000);
    if (lockoutUntil && lockoutUntil > now) {
      const secondsLeft = lockoutUntil - now;
      showLockoutModal(secondsLeft);
      startLockout();
      return true;
    }
    return false;
  }

  $(document).ready(function () {
    end_loader();
    const isLocked = checkLockoutOnLoad();

    $('#login-frm').on('submit', function (e) {
      e.preventDefault();
      if (checkLockoutOnLoad()) return;

      const username = $('input[name="username"]').val();
      const password = $('input[name="password"]').val();

      if (username === 'admin' && password === 'admin123') {
        $('#login-error').hide();
        alert('Login successful!');
        localStorage.removeItem('failedAttempts');
        localStorage.removeItem('lockoutUntil');
      } else {
        failedAttempts++;
        localStorage.setItem('failedAttempts', failedAttempts);
        $('#login-error')
          .text('Invalid credentials. Attempt ' + failedAttempts + ' of 3')
          .fadeIn();

        if (failedAttempts >= 3) {
          startLockout();
        }
      }
    });
  });
</script>


</body>
</html>