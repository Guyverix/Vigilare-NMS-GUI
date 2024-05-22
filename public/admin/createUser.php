<?php
/*
  Create User
  Admin page!

  This is going to have 2 options.  Either we are going to
  create with email and a temp password, or we are going to
  set the user password.

  I think the temp password is the better idea, but donno everyone
  elses use case.  Might not be the best solution for someone else.
*/


// Only needed for debugging and bypassing security, etc
// require_once(__DIR__ . '/../../functions/generalFunctions.php');
// checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

// Load local vars for use (urls, ports, etc)
// require_once __DIR__ . "/../../config/api.php";

// Grab our POSSIBLE values so users can choose what they change
$headers = array();
$headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
$post = array();  // We are using post, so give it an empty array to post with
$quitEarly = 0;
echo "<br><br><br>";
// This is from an INTERNAL POST ONLY when we have changed a monitor
if (isset($_POST['password'])) {
  $post = $_POST;
  $changeMyUser = callApiPost("/admin/create", $post, $headers);
  $rawResponse = json_decode($changeMyUser['response'], true);
  if ( $changeMyUser['code'] == 418 ) {
    $responseCode = 418;
  }
  else {
    $responseCode = $rawResponse['statusCode'];
  }
  $post = array();
  if ($responseCode !== 200 && $responseCode !== 403 && $responseCode !== 418) {    // Anything other than a 200 OK is an issue
    echo "<br><br><br>";
    $responseString = $rawResponse['error']['description'];
    decideResponse($responseCode, $responseString);
    $quitEarly = 1;
  } elseif ($responseCode == 418) {
    load418("Additional access required.  Please contact admin.");
    $quitEarly = 2;
  } elseif ($responseCode == 403) {
    load403Warn("Expired access credentials");
    $quitEarly = 1;
  } else {
    // After a successful create, load the page with default values
    echo "<br><br><br>";
    successMessage('User creation with password is successful.');
  }
} elseif (isset($_POST['noPassword'])) {
  $post = $_POST;
  $changeMyUser = callApiPost("/admin/adminRegister", $post, $headers);
  $rawResponse = json_decode($changeMyUser['response'], true);
  if ( $changeMyUser['code'] == 418 ) {
    $responseCode = 418;
  }
  else {
    $responseCode = $rawResponse['statusCode'];
  }
  $post = array();
  if ($responseCode !== 200 && $responseCode !== 403 && $responseCode !== 418) {    // Anything other than a 200 OK is an issue
    echo "<br><br><br>";
    $responseString = $rawResponse['error']['description'];
    decideResponse($responseCode, $responseString);
    $quitEarly = 1;
  } elseif ($responseCode == 418) {
    load418("Additional access required.  Please contact admin.");
    $quitEarly = 2;
  } elseif ($responseCode == 403) {
    load403Warn("Expired access credentials");
    $quitEarly = 1;
  } else {
    // After a successful create, load the page with default values
    echo "<br><br><br>";
    successMessage('User creation was successful.  Email sent to user with temporary link.');
  }
}


// Success or failure, continue loading the page
$id = 'na';
$realName = 'John Doe';
$email = 'fakeUser@noyb.com';
$userId = 'fakeUser';
$timer = 8;
$accessList = 'none';
$enable = 0;
$password = 'changeMe';

if ($quitEarly == 0) {
  // debugger($_SERVER);
  // exit();
  ?>
  <br><br>
  <center>
    <div class="container">
      <div class=" text-center mt-5 ">
        <h1>Create New User</h1><br>
      </div>
      <div class="row">
        <div class="col">
          <form id="create-user-form-password" role="form" action="" method="POST">
            <form id="create-user-form-no-password" role="form" action="" method="POST">
              <div class="controls">
                <div class="row">
                  <center>
                    <div class="col-3">
                      <div class="form-group">
                        <label for="form_realName">Real Name *</label>
                        <?php echo '<input id="form_realName" type="text" name="realName" class="form-control" value="' . $realName . '" required="required" data-error="The users real name is required.">'; ?>
                      </div> <!-- form-group -->
                    </div> <!-- col -->
                </div> <!-- row -->
                <div class="row">
                  <center>
                    <div class="col-3">
                      <div class="form-group">
                        <label for="form_email">Email Address *</label>
                        <?php echo '<input id="form_email" type="text" name="email" class="form-control" value="' . $email . '" required="required" data-error="A valid email address is required.">'; ?>
                      </div> <!-- form-group -->
                    </div> <!-- col -->
                </div> <!-- row -->
                <div class="row">
                  <center>
                    <div class="col-3">
                      <div class="form-group">
                        <label for="form_userId">User Login ID *</label>
                        <?php echo '<input id="form_userId" type="text" name="userId" class="form-control" value="' . $userId . '" required="required" data-error="A userid value is required.">'; ?>
                      </div> <!-- form-group -->
                    </div> <!-- col -->
                </div> <!-- row -->
                <div class="row">
                  <center>
                    <div class="col-3">
                      <div class="form-group">
                        <label for="form_timer">Max login hours *</label>
                        <?php echo '<input id="form_timer" type="text" name="timer" class="form-control" value="' . $timer . '" required="required" data-error="Number of hours a login cookie is good for.">'; ?>
                      </div> <!-- form-group -->
                    </div> <!-- col -->
                </div> <!-- row -->
                <div class="row">
                  <center>
                    <div class="col-3">
                      <div class="form-group">
                        <label for="form_accessList">accessList *</label>
                        <?php echo '<input id="form_accessList" type="text" name="accessList" class="form-control" value="' . $accessList . '" required="required" data-error="CSV of access names, or a single integer with an absolute access value.">'; ?>
                      </div> <!-- form-group -->
                    </div> <!-- col -->
                </div> <!-- row -->
                <div class="row">
                  <center>
                    <div class="col-3">
                      <div class="form-group">
                        <label for="form_enable">Enable Login *</label>
                        <select id="form_enable" name="enable" class="form-control" required="required"
                          data-error="Please specify if account is enabled or not.">
                          <?php
                          if ($enable == 1) {
                            echo '<option value="0">account disabled</option>';
                            echo '<option value="' . $enable . '" selected >account enabled</option>';
                          } else {
                            echo "<option value=\"" . $enable . "\" selected >account disabled</option>";
                            echo '<option value="1">account enabled</option>';
                          }
                          ?>
                        </select>
                      </div>
                    </div>
                    <div class="row">
                      <center>
                        <div class="col-12"><br>
                          <input type="submit" class="btn btn-success btn-send  pt-2 btn-block" name="noPassword"
                            value="Email Temporary Password">
                        </div>
                    </div> <!-- row for form -->
                </div> <!-- controls -->
                <div class="row">
                  <center>
                    <div class="col-3">
                      <div class="form-group">
                        <label for="form_timer">Password *</label>
                        <?php echo '<input id="form_timer" type="text" name="userPass" class="form-control" value="' . $password . '" required="required" data-error="Initial user password.">'; ?>
                      </div> <!-- form-group -->
                    </div> <!-- col -->
                </div> <!-- row -->
                <div class="row">
                  <center>
                    <div class="col-12"><br>
                      <input type="submit" class="btn btn-success btn-send  pt-2 btn-block" name="password"
                        value="Set Password">
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
} elseif ( $quitEarly == 2) {}
else {
  // Our API did not give us usable information.  May be transient, or API server is borked.
  loadUnknown("API calls failed in an unexpected way.  Please reload");
}

?>
