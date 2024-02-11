<?php
  /*
    Show deviceGroup information and allow for manipulation
  */
  echo "<br><br><br>";  // drop below ribbon

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
  $task = 'ignore';  // If we dont set stuff correctly, dont do stuff at all!

  // This is from an INTERNAL POST ONLY when we have changed something specific
  if ( isset($_POST['changeDeviceGroup'])) {
    $post = ['hostId' => $_POST['hostId']];
    $post += ['hostGroup' => $_POST['hostGroup']];
    $task = $_POST['task'];
    $changeDeviceGroup = callApiPost("/deviceGroup/" . $task, $post, $headers);
    $rawResponse = json_decode($changeDeviceGroup['response'], true);
    $responseCode = $rawResponse['statusCode'];
    $post = array();
    if ($responseCode !== 200 && $responseCode !== 403) {    // Anything other than a 200 OK is an issue
      decideResponse($responseCode, $responseString );
      $quitEarly = 1;
    }
    elseif ( $responseCode == 403) {
      load403Warn("Expired access credentials");
      $quitEarly = 1;
    }
    elseif ( $resonseCode ==200) {
      // After a successful update, wait and then reload the page
      successMessage('Device discovery is complete.');
      $quitEarly = 0;
    }
    else {
      $quitEarly = 1;
    }
  }

  // Full list of deviceGroups
  $rawDeviceGroupList = allApiPost("/monitors/findHostGroup", $post, $headers);

  // List of devices associated with a specific deviceGroup
  $rawDeviceNameList = allApiPost("/monitors/deviceGroupName", $post, $headers);

