<?php
  /*
    This is the landing page when deleting a monitor.
    After the message, we are going to redirect back to the searchMonitor page
  */

  /*
    We need to make some API calls, so load defaults to do so
  */
  echo "<br><br><br>";
  // Add our functions in for calling the api
  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  checkCookie($_COOKIE);  // disable check here to test 401 and 403 responses

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";

  if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $headers = array();
    $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
    $postData = $_POST;
    // debugger($postData);

    // API call to remove monitor id.
    $callApi = callApiPost('/monitors/deleteMonitor', $postData, $headers);  // returns an array
    $parentDecode = json_decode($callApi['response'], 1);
    $responseCode = $parentDecode['statusCode'];
    $childDecode = json_decode($parentDecode['result'], 1);
    $responseString = json_encode($childDecode, 1);

    if ( $responseCode !== 200 && $responseCode !== 403) {    // Anything other than a 200 OK is an issue
      decideResponse($responseCode, $responseString );
    }
    elseif ( $responseCode == 403) {
      load403Warn("Expired access credentials");
    }
    else {
      // After a successful delete, go back to the search page
      successMessage('Monitor delete is successful.');
      $_SERVER['REQUEST_METHOD'] = '';   // Unset our POST before reloading the page :)
       echo '<script>
                window.setTimeout(function() {
                window.location = "/monitor/index.php?&page=searchMonitor.php";
                }, 4000);
             </script>';
    }
  }
  else {
    loadUnknown("API call failed in an unexpected way.  Please use delete button from search page.");
  }
?>
