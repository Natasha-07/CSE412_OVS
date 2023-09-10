    <?php if ($check < 1): ?>
      <?php if ($passdefault): ?>
        <div class="col-sm-12">
          <div class="tengah">
            <div class="col-sm-5">
              <div class="box box-danger box-solid">
                <div class="box-header">
                  <h4 class="box-title"><i class="fa fa-lock"></i> Change Password</h4>
                </div>
                <div class="box-body">
                  <p class="text-center text-muted">Please change the password to continue</p>
                  <form id="formchangePasswd">
                    <div class="form-group">
                      <label for="PasswordLama">Old Password :</label>
                      <input type="password" name="passwdlama" class="form-control" placeholder="Enter Old Password">
                    </div>
                    <div class="form-group">
                      <label for="PasswordBaru">New Password :</label>
                      <input type="password" name="passwdbaru" class="form-control" placeholder="Enter New Password">
                    </div>
                    <button class="btn btn-danger btn-flat">Save</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      <?php else: ?>
        <div class="col-sm-12">
          <div class="box ">
            <div class="box-body">
              <h1 class="text-center no-margin"><?=$voting->election?></h1>
              <hr>
              <div class="tengah">
                <?php foreach ($candidate as $k): ?>
                <div class="col-sm-4">
                  <div class="box">
                    <div class="box-body box-profile">
                      <img src="<?=base_url('assets/img/candidate/'.$k->photo);?>" alt="" class="profile-user-img img-candidate img-responsive img-circle">
                      <h3 class="profile-username text-center"><?=$k->candidate_name?></h3>
                      <p class="text-center text-muted"><?=$k->explanation?></p>
                      <button class="btn btn-danger btn-flat btn-block check" data="<?=$k->candidate_id?>" data-voting="<?=$voting->id_voting?>" data-name="<?=$k->candidate_name?>">Vote</button>
                    </div>
                  </div>
                </div>
                <?php endforeach ?>
              </div>
              <h4 class="text-muted text-center">*Please choose a candidate to continue</h4>
            </div>
          </div>
        </div>
      <?php endif ?>
    <?php else: ?>
        <div class="col-sm-12">
          <div class="box box-solid">
            <div class="box-body">
              <h2 class="text-center">Thanks For Voting!</h2>
              <hr>
              <div class="tengah">
                <button class="btn btn-danger btn-flat logout text-center">LOGOUT NOW</button>
              </div>
            </div>
          </div>
        </div>
    <?php endif ?>

      <script>
        $('#formchangePasswd').on('submit', function(e){
          e.preventDefault();
          var id = <?=$this->session->id?>;
          var passLama = $('[name="passwdlama"]').val(), passBaru = $('[name="passwdbaru"]').val();
          if (passLama != '' || passBaru != '') {
            $.ajax({
              type: 'POST',
              url: '<?=base_url("user/set_pass_id/")?>'+id,
              data: {passwdLama: passLama, passwdBaru: passBaru},
              success: function(data){
                if (data == 1) {
                  $('#formchangePasswd').trigger('reset');
                  Swal.fire('Success', 'You will be redirected in 5 seconds', 'success');
                  setTimeout(function(){
                    window.location.reload()
                  }, 5000);
                }
                else{
                  $('#formchangePasswd').trigger('reset');
                  Swal.fire('Failed', 'Wrong old password!', 'error');
                }
              }
            })
          }
          else{
            Swal.fire('Failed', 'Form must be filled!', 'error');
            return false;
          }
        })
        //check
        $('.check').on('click', function(e){
          e.preventDefault();
          var id = $(this).attr('data');
          var voting = $(this).attr('data-voting');
          var name = $(this).attr('data-name');
          Swal.fire({
            type: 'question',
            title: 'Select '+name,
            text: 'Are you sure you will vote for the candidate ?',
            showCancelButton: true,
            confirmButtonText: 'Select'
          }).then((result) => {
            if (result.value) {
              //aksi
              $.ajax({
                type: 'POST',
                url: '<?=base_url("user/check/")?>'+voting+'/'+id,
                success: function(data){
                  if (data == 1) {
                    Swal.fire({
                      type: 'success',
                      title: 'Success',
                      text: 'You have chosen '+name,
                    }).then((result) => {
                      if (result.value) {
                        window.location.reload()
                      }
                    })
                  }
                }
              })
            }
          })
        })
        //logout
        $('.logout').on('click', function(e){
          e.preventDefault()
          window.location.assign('<?=base_url("user/logout")?>')
        })
      </script>

    <?php if ($check > 0): ?>
      <script>
        setTimeout(function(){
          window.location.assign('<?=base_url("user/logout")?>');
        }, 120000)
      </script>
    <?php endif ?>