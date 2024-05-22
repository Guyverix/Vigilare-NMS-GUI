<?php
  /*
    Change a users login information
    Admin page!
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

  // This is from an INTERNAL POST ONLY when we have changed a user value
  if ( isset($_POST['changeMyUser'])) {
    $post = $_POST;
    $changeMyUser = callApiPost("/admin/update", $post, $headers);
    $rawResponse = json_decode($changeMyUser['response'], true);
    $responseCode = $rawResponse['statusCode'];
    $post = array();
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
      successMessage('User update is successful.');
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
    <h1>Change Existing User</h1><br>
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
              <?php echo '<input id="form_realName" type="text" name="realName" class="form-control" value="' . $realName . '" required="required" data-error="The users real name is required.">';  ?>
            </div> <!-- form-group -->
          </div>  <!-- col -->
        </div>  <!-- row -->
        <div class="row"><center>
          <div class="col-3">
            <div class="form-group">
              <label for="form_email">Email Address *</label>
              <?php echo '<input id="form_email" type="text" name="email" class="form-control" value="' . $email . '" required="required" data-error="A valid email address is required.">';  ?>
            </div> <!-- form-group -->
          </div>  <!-- col -->
        </div>  <!-- row -->
        <div class="row"><center>
          <div class="col-3">
            <div class="form-group">
              <label for="form_userId">User Login ID *</label>
              <?php echo '<input id="form_userId" type="text" name="userId" class="form-control" value="' . $userId . '" required="required" data-error="A userid value is required.">';  ?>
            </div> <!-- form-group -->
          </div>  <!-- col -->
        </div>  <!-- row -->
        <div class="row"><center>
          <div class="col-3">
            <div class="form-group">
              <label for="form_timer">Max login hours *</label>
              <?php echo '<input id="form_timer" type="text" name="timer" class="form-control" value="' . $timer . '" required="required" data-error="Number of hours a login cookie is good for.">';  ?>
            </div> <!-- form-group -->
          </div>  <!-- col -->
        </div>  <!-- row -->
        <div class="row"><center>
          <div class="col-3">
            <div class="form-group">
              <label for="form_accessList">accessList *</label>
              <?php echo '<input id="form_accessList" type="text" name="accessList" class="form-control" value="' . $accessList . '" required="required" data-error="CSV of access names, or a single integer with an absolute access value.">';  ?>
            </div> <!-- form-group -->
          </div>  <!-- col -->
        </div>  <!-- row -->
        <div class="row"><center>
          <div class="col-3">
            <div class="form-group">
              <label for="form_enable">Enable Login *</label>
              <select id="form_enable" name="enable" class="form-control" required="required" data-error="Please specify if account is enabled or not.">
              <?php
              if ( $enable == 1 ) {
                echo '<option value="0">account disabled</option>';
                echo '<option value="' . $enable . '" selected >account enabled</option>';
              }
              else {
                echo "<option value=\"" . $enable . "\" selected >account disabled</option>";
                echo '<option value="1">account enabled</option>';
              }
              ?>
              </select>
            </div>
          </div>
        <div class="row"><center>
          <div class="col-12"><br>
            <input type="hidden" name="changeMyUser" value="true">
            <?php echo '<input type="hidden" name="id" value="' . $id . '">'; ?>
            <input type="submit" class="btn btn-success btn-send  pt-2 btn-block" value="Change User Values" >
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
