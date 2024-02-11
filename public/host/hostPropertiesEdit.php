<?php
  /*
    Editing known device Properties for a given host or device
  */


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


  // Initial call should have these 2 always set
  if ( isset($_POST['id'])) {
    $id = $_POST['id'];
  }

  if ( isset($_POST['hostname'])) {
    $hostname = $_POST['hostname'];
  }

  if ( isset($_POST['deviceProperties'])) {
    $properties = json_decode($_POST['deviceProperties'], true);
  }

  // If we want a full discovery, this will do it...
  if (isset($_POST['rediscover'])) {
    $post = [ 'id' => $id ];
    $rediscover = callApiPost("/discovery/discover" , $post, $header);
    // Show if we were able to actually rediscover stuff
    $rawRediscover = json_decode($rediscover['response'], 1);
    $rediscoverResponseCode = $rawRediscover['statusCode'];

    if ($rediscoverResponseCode !== 200 && $rediscoverResponseCode !== 403) {    // Anything other than a 200 OK is an issue
      echo "<br><br><br>";
      decideResponse($rediscoverResponseCode, $responseString );
      $quitEarly = 1;
    }
    elseif ( $rediscoverResponseCode == 403) {
      load403Warn("Expired access credentials");
      $quitEarly = 1;
    }
    else {
      // After a successful update show suceess
      echo '<br><br><br>';
      successMessage('Device discovery was successful.');
      $properties = $rawRediscover['data'];
      $quitEarly = 0;
    }
  }

  // This is going to be the oddball wheere we are adding and removing stuff
  if (isset($_POST['new_key']) || isset($_POST['remove_key'])) {
    //  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recombine the key-value pairs into a single array
    $newData = [];
    $id = $_POST['id'];
    $hostname = $_POST['hostname'];

    foreach ($_POST as $key => $value) {
      if ( $key == 'new_key') {
        $newKey = $value;
      }
      elseif ( $key == 'new_value') {
        $newValue = $value;
      }
      else {
        if ($key !== 'remove_key' && $key !== 'remove_value' && $key !== 'id' && $key !== 'hostname') {
          $newData[$key] = $value;
        }
      }
    }
    $removeKey = $_POST['remove_key'];
    if (!empty($removeKey)) {
      unset($newData[$removeKey]);
    }
    if (!empty($newKey) && !empty($newValue)) {
      $newData[$newKey] = $newValue;
    }

    $data = $newData;
    $data = json_encode($data,1);
    $post = ['id' => $id];
    $post += ['component' => 'properties'];
    $post += ['properties' => $data];

    // Change our device values now via API call
    $updateProperties = callApiPost("/device/update", $post, $headers);

    $rawResponse = json_decode($updateProperties['response'],1 );
    $responseCode = $rawResponse['statusCode'];
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
      // After a successful update show suceess
      echo '<br><br><br>';
      successMessage('Device Property changes have been saved.');
      $quitEarly = 0;
    }
  }


  // Sneaky buggers seem to get in there on page changes
  if ( !empty($properties['Id'])) { unset($properties['Id']); }
  if ( !empty($properties['id'])) { unset($properties['id']); }
  if ( !empty($properties['hostname'])) { unset($properties['hostname']); }

/*
debugger($post);
exit();
  echo "<br><br><br>";
  debugger($_POST);
  exit();
  debugger($properties);
  exit();
*/


  // In theory we should have what we need to display our information
  if ( $quitEarly == 0 ) {
?>
<br><br>
<div class="container">
  <div class=" text-center mt-5 ">
    <h1>Change <?php echo '<a href="/host/index.php?&page=deviceDetails.php&id=' . $id . '">' . $hostname . '</a>'; ?> Properties</h1><br>
  </div>
  <div class="row">
    <div class="col">
      <form id="rediscovery" role="form" action="" method="POST">
        <?php
          echo '<input type="hidden" name="id" value="' . $id . '">';
          echo '<input type="hidden" name="hostname" value="' . $hostname . '">';
      ?>
      </form>
      <form id="change-properties" role="form" action="" method="POST">
        <?php echo '<form id="form" action="/host/index.php?&page=hostPropertiesEdit.php&id="' . $id . '"  method="POST">';
          echo '<input type="hidden" name="id" value="' . $id . '">';
          echo '<input type="hidden" name="hostname" value="' . $hostname . '">';
        ?>
          <table class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;">
            <thead>
              <tr>
                <th><center>Delete</center></th>
                <th>Key</th>
                <th>Value</th>
                <th>
                  <button type="submit" class="btn btn-warning" name="rediscover" form="rediscovery"> Rediscover Everything </button>
                  <button type="submit" class="btn btn-primary" form="change-properties">Change Properties</button></th>
              </tr>
            </thead>
            <tbody>
            <?php
            if (isset($newData)) {
              $properties = $newData;
            }
            ?>
            <!-- Add a new property here -->
            <tr><td align=center>New:</td><td><input type="text" id="new_key" name="new_key" value=""></td><td><input type="text" id="new_value" name="new_value" value="" style="width: 500px;"></td></tr>
            <?php
              foreach ($properties as $key => $value) {
                echo '<tr><td align=center><button type="submit" name="remove_key" class="btn btn-danger btn-sm" value="' . $key . '">Remove</button></td>';
                echo '<td><label for="' . $key . '">' . ucfirst($key) . '</label></td>';
                echo '<td><input type="text" id="' . $key . '" name="' . $key . '" value="';
                if (is_array($value)) {
                  $value=json_encode($value,true);
                }
                echo  htmlspecialchars($value) ;
                echo '" style="width: 500px;"></td></tr>';
              }
            ?>
          </tbody>
        </table>
    </form>
  </div>
</div>

  <?php
  }
  else {
    // Something went very wrong with the API call, but keep the layout clean...
    loadUnknown("API calls failed in an unexpected way.  Please reload");
  }
?>
