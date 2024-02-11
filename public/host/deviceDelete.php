<?php
  /*
    Wanna nuke something?  Are you sure?
  */

  echo '<br><br><br>'; // only needed if we have a horozontal bar

  // Only needed for debugging and bypassing security, etc
  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  // checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";

  // Hosts and Devices have A LOT of variables in play.  We need functions specific to this group
  require_once __DIR__ . "/functions/hostFunctions.php";

  // Grab our POSSIBLE values so users can choose what they change
  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
  $post = array();  // We are using post, so give it an empty array to post with
  $quitEarly = 0;

  // debugger($_POST);
  if (isset($_POST['deviceDelete'])) {
    $post = [ 'id' => $_POST['id'] ];

    $rawDeleteDevice = callApiPost("/device/delete", $post, $headers);
    $deleteDevice = json_decode($rawDeleteDevice['response'],true);
    $responseCode = $deleteDevice['statusCode'];
    if ($responseCode !== 200 && $responseCode !== 403) {    // Anything other than a 200 OK is an issue
      decideResponse($responseCode, $responseString );
      $quitEarly = 1;
    }
    elseif ( $responseCode == 403) {
      load403Warn("Expired access credentials");
      $quitEarly = 1;
    }
    else {
      // After a successful update, wait and then reload the page
      successMessage('Device deletion is complete.');
      $quitEarly = 2;
      echo '<script>
              window.setTimeout(function() {
                window.location = "/host/index.php";
              }, 2000);
            </script>';
    }
  }

  if ( $quitEarly == 0 ) {
  ?>
  <div class="container">
    <div class=" text-center mt-5 ">
      <h1>Deletion of <?php echo '<a href="/host/index.php?&page=deviceDetails.php&id=' . $_POST['id'] . '">' . $_POST['hostname'] . '</a>'; ?></h1><br>
    </div>
    <table class="table table-striped bg-dark table-dark">
      <tbody>
        <tr><td>Device ID</td><td><?php echo $_POST['id'] ; ?></td></tr>
        <tr><td>Hostname</td><td><?php echo $_POST['hostname'] ; ?></td></tr>
        <tr><td>Address</td><td><?php echo $_POST['address'] ; ?></td></tr>
        <tr><td>First Seen</td><td><?php echo $_POST['firstSeen'] ; ?></td></tr>
        <tr><td>Production State</td><td><?php echo $_POST['productionState'] ; ?></td></tr>
        <tr><td>Is Alive</td><td><?php echo $_POST['isAlive'] ; ?></td></tr>
      </tbody>
    </table>
  <?php
    echo '<form id="deviceDelete" method="POST" action="/host/index.php?&page=deviceDelete.php">';
    echo '<input type="hidden" name="id" value="' . $_POST['id'] . '">';
    echo '<button form="deviceDelete" name="deviceDelete" type="submit" class="btn btn-danger">Delete Device</button> ';
  }


  elseif ($quitEarly = 2) {
   // do nothing
  }
  else {
    // Something went very wrong with the API call, but keep the layout clean...
    loadUnknown("Page load failed in an unusual way.  Please go back one page and try again.");
  }
?>








