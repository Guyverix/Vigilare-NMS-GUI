<?php
  /*
    Change a users login password
    Admin page!  Admin only!  Users have their own path
  */


  // Only needed for debugging and bypassing security, etc
  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  // checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";

  // Grab our POSSIBLE values so users can choose what they change
  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
  $post = array();  // We are using post, so give it an empty array to post with
  $quitEarly = 0;


  // debugger($_POST);

  // This is from an INTERNAL POST ONLY when we have changed a monitor
  if ( isset($_POST['changePassword'])) {
    $post = array();
    $post  = ['username' => $_POST['userId']];
    $post += ['password' => $_POST['userPassword']];
    $changeMyUser = callApiPost("/admin/updatePassword", $post, $headers);
    // debugger($changeMyUser);
    $rawResponse = json_decode($changeMyUser['response'], true);
    $responseCode = $rawResponse['statusCode'];
    $responseString = $rawResponse['error']['description'];
    if ($responseCode !== 200 && $responseCode !== 403) {    // Anything other than a 200 OK is an issue
      echo "<br><br><br>";
      decideResponse($responseCode, $responseString );
      $quitEarly = 1;
    }
    elseif ( $responseCode == 403) {
      load403Warn("Expired access credentials");
      $quitEarly = 1;
    }
    else {
      // After a successful update, wait and then reload the page
      echo "<br><br><br>";
      successMessage('User password change is successful.');
    }
  }
  // Success or failure, continue loading the page


  // Set the values we know right now passed from parent page or a self post
  $id = $_POST['id'];
  $userId = $_POST['userId'];
  $email = $_POST['email'];
  $realName = $_POST['realName'];
  $timer = $_POST['timer'];
  $accessList = $_POST['accessList'];
  $enable = $_POST['enable'];

  if ( $quitEarly == 0 ) {
    // debugger($_SERVER);
    // exit();
?>
<br><br><center>
<div class="container">
  <div class=" text-center mt-5 ">
    <h1>Change User Password</h1><br>
  </div>
  <div class="row">
    <div class="col">
      <form id="update-user-form" role="form" action="" method="POST">
      <div class="controls">
      <div class="row"><center>
          <div class="col-3">
            <div class="form-group">
              <label for="form_realName">User ID number *</label>
              <?php echo '<input id="form_id" type="text" name="id" class="form-control" value="' . $id . '" required="required" data-error="The ID number must be defined for changes to happen." readonly>';  ?>
            </div> <!-- form-group -->
          </div>  <!-- col -->
        </div>  <!-- row -->        <div class="row"><center>
          <div class="col-3">
            <div class="form-group">
              <label for="form_realName">Real Name *</label>
              <?php echo '<input id="form_realName" type="text" name="realName" class="form-control" value="' . $realName . '" required="required" data-error="The users real name is required." readonly>';  ?>
            </div> <!-- form-group -->
          </div>  <!-- col -->
        </div>  <!-- row -->
        <div class="row"><center>
          <div class="col-3">
            <div class="form-group">
              <label for="form_email">Email Address *</label>
              <?php echo '<input id="form_email" type="text" name="email" class="form-control" value="' . $email . '" required="required" data-error="A valid email address is required." readonly>';  ?>
            </div> <!-- form-group -->
          </div>  <!-- col -->
        </div>  <!-- row -->
        <div class="row"><center>
          <div class="col-3">
            <div class="form-group">
              <label for="form_userId">User Login ID *</label>
              <?php echo '<input id="form_userId" type="text" name="userId" class="form-control" value="' . $userId . '" required="required" data-error="A userid value is required." readonly>';  ?>
            </div> <!-- form-group -->
          </div>  <!-- col -->
        </div>  <!-- row -->
        <div class="row"><center>
          <div class="col-3">
            <div class="form-group">
              <label for="form_timer">Max login hours *</label>
              <?php echo '<input id="form_timer" type="text" name="timer" class="form-control" value="' . $timer . '" required="required" data-error="Number of hours a login cookie is good for." readonly>';  ?>
            </div> <!-- form-group -->
          </div>  <!-- col -->
        </div>  <!-- row -->
        <div class="row"><center>
          <div class="col-3">
            <div class="form-group">
              <label for="form_accessList">accessList *</label>
              <?php echo '<input id="form_accessList" type="text" name="accessList" class="form-control" value="' . $accessList . '" required="required" data-error="CSV of access names, or a single integer with an absolute access value." readonly>';  ?>
            </div> <!-- form-group -->
          </div>  <!-- col -->
        </div>  <!-- row -->
        <div class="row"><center>
          <div class="col-3">
            <div><b>As an administrator you CAN give simpler passwords, but it is not recommended.  You must still give more than 6 characters.</b></div>
            <div class="form-group">
              <label for="form_enable">New Password *</label>
              <?php echo '<input id="form_accessList" type="text" name="userPassword" class="form-control" value="PASSWORD" required="required" data-error="Give a new password here.">';  ?>
            </div>
          </div>
        <div class="row"><center>
          <div class="col-12"><br>
            <input type="hidden" name="changePassword" value="true">
            <?php echo '<input type="hidden" name="id" value="' . $id . '">'; ?>
            <input type="submit" class="btn btn-success btn-send  pt-2 btn-block" value="Change Password" >
          </div>
        </div> <!-- row for form -->
        </div> <!-- controls -->
        </form>
      </div> <!-- column end -->
      </div>
    </div>
   </div> <!-- full row end -->
  </div> <!-- end container -->



<?php

  }
  else {
    // Our API did not give us usable information.  May be transient, or API server is borked.
    loadUnknown("API calls failed in an unexpected way.  Please reload");
  }
?>
