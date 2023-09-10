<?php
function generateRandomString($length = 6) {
  $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
  $charactersLength = strlen($characters);
  $randomString = '';
  for ($i = 0; $i < $length; $i++) {
      $randomString .= $characters[rand(0, $charactersLength - 1)];
  }
  return $randomString;
}
$rand = generateRandomString()
?>
        
        
        <div class="tengah">
          <div class="col-sm-5">
            <div class="box box-success box-solid">
              <div class="box-header">
                <h4 class="box-title"><i class="fa fa-lock"></i> Voter's Login Panel</h4>
              </div>
              <div class="box-body">
                <h4 class="text-center text-muted">Please login to continue</h4>
                <form action="<?=base_url('Auth/actlogin')?>" method="POST">
                  <div class="form-group">
                    <label for="Username">Username :</label>
                    <input type="text" name="username" class="form-control" placeholder="Enter Username">
                  </div>
                  <div class="form-group">
                    <label for="Password">Password :</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter Password">
                  </div>
                  <div>
                    <?php echo $rand?>
                    <input type='hidden' name='rand' value='<?php echo "$rand";?>'/> 
                  </div>
                  <div class="form-group">
                    <label for="Username">OTP :</label>
                    <input type="text" name="otp" class="form-control" placeholder="Enter the OTP" />
                  </div>
                  <button class="btn btn-success btn-flat btn-block"><i class="fa fa-sign-in"></i> Login</button>
                  <!-- <button class="btn btn-success btn-flat btn-block"><i class="fa fa-sign-in"></i> SEND OTP</button> -->
                </form>
              </div>
            </div>
          </div>
        </div>