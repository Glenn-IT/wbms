<h1>Welcome to <?php echo $_settings->info('name') ?> - Management Site</h1>
<hr>
<style>
  #site-cover {
    width:100%;
    height:40em;
    object-fit: cover;
    object-position:center center;
  }
</style>
<div class="row">
    
    <!-- /.col -->
    
    <!-- /.col -->
    
    <!-- /.col -->
</div>
<hr>
<center>
  <img src="<?= validate_image($_settings->info('cover')) ?>" alt="<?= validate_image($_settings->info('logo')) ?>" id="site-cover" class="img-fluid w-100">
</center>
