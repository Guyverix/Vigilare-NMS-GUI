<?php
  /*
    Change user information or password.  This is AFTER login, not reset.
    Dont get sloppy on security here.
    Check your cookie, and we need to send JWT to change stuff.
  */
  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  checkCookie($_COOKIE);
  echo "<br><br><br>";

  if ( isset($_POST['updateUser'])) {
    $headers = array();
    $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
    $post = array();  // We are using post, so give it an empty array to post with
    $post = ['id' => $_POST['id']];
    $post += ['email' => $_POST['email']];
    $post += ['realName' => $_POST['fullName']];
    $post += ['username' => $_POST['username']];  // for updatePassword
    $post += ['userId' => $_POST['username']];    // for updateUser

    $rawCallChangeUser = callApiPost("/user/update", $post, $headers);
    $callChangeUser = json_decode($rawCallChangeUser['response'],1);
    if ( $callChangeUser['statusCode'] == 200 ) {
      successMessage("User values changed successfullly");
    }
    else {
      decideResponse($callChangeUser['statusCode'], json_encode($callChangeUser['error'],1));
    }
    // changing passwords is a different api call
    if ( ! empty($_POST['newPassword'])) {
      $post += ['password' => $_POST['newPassword']];
      $post += ['oldPassword' => $_POST['oldPassword']];
      $rawCallChangePassword = callApiPost("/user/updatePasswordUsers", $post, $headers);
      $callChangePassword = json_decode($rawCallChangePassword['response'],1);
      if ( $callChangePassword['statusCode'] == 200 ) {
        successMessage("Successful password change");
      }
      else {
        decideResponse($callChangePassword['statusCode'], json_encode($callChangePassword['error'],1));
      }
    }
    // After changing stuff, get outta here!

    echo '<script>
          window.setTimeout(function() {
          window.location = "/user/index.php";
          }, 4000);
          </script>';

    //debugger($callChangeUser);
    //debugger($callChangePassword);
    //debugger($post);
    //debugger($_POST);
  }

  // We have cookies set, so use the info provided here
  $email=$_COOKIE['email'];
  $userId=$_COOKIE['userId'];
  $id=$_COOKIE['id'];
  $fullName=$_COOKIE['realName'];

?>
  <!-- Tired of fighting with bootstrap to center -->
  <center>
  <div class="container">
    <div class="row justify-content-right">
      <div class="col-lg-5">
        <div class="card shadow-lg border-0 rounded-lg mt-5 bg-light">
          <div class="card-header"><h3 class="text-center font-weight-light my-4">Update <?php echo $userId . "'s"; ?> Settings</h3></div>
          <div class="card-body bg-light">
            <form action="" method="POST"> 
              <div>User Number:</div>
              <div class="form-floating mb-3 text-dark">
                <input class="form-control" id="id" name="id" type="text" value="<?php echo $id; ?>" text-dark readonly/>
              </div>
              <div>User Name(login name):</div>
              <div class="form-floating mb-3 text-dark">
                <input class="form-control" id="username" name="username" type="text" value="<?php echo $userId; ?>" readonly/>
              </div>
              <div>Full Real Name:</div>
              <div class="form-floating mb-3 text-dark">
                <input class="form-control" id="fullName" name="fullName" type="text" value="<?php echo $fullName ?>"/>
              </div>
              <div>Current Password:(only if changing to a new one)</div>
              <div class="form-floating mb-3 text-dark">
                <input class="form-control" id="oldPassword" name="oldPassword" type="password" placeholder="Current Password" />
                <label for="oldPassword">Current Password</label>
              </div>
              <div>Change Password:</div>
              <div class="form-floating mb-3 text-dark">
                <input class="form-control" id="newPassword" name="newPassword" type="password" placeholder="Change Password" />
                <label for="newPassword">New Password</label>
              </div>
              <div>Email Address:</div>
              <div class="form-floating mb-3 text-dark">
                <input class="form-control" id="email" name="email" type="email" value="<?php echo $email; ?>" />
              </div>
              <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                <button type="submit" name="updateUser" class="btn btn-primary">Update</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>



