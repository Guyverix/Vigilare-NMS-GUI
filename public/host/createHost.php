<?php
  /*
    Just a simple way to create a new host
    this will support the entire page, or just a GET with the info
  */

  /*
    We need to make some API calls, so load defaults and boilerplate to do so
  */

  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";

  echo "<br><br><br>";
  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
  $post = array();  // We are using post, so give it an empty array to post with
  $quitEarly = 0;

  // Called from page itself
  if (isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $post = $_POST;
    $callApi = callApiPost('/device/create', $post, $headers);  // returns an array
    $parentDecode = json_decode($callApi['response'], 1);
    $responseCode = $parentDecode['statusCode'];
    $childDecode = json_decode($parentDecode['result'], 1);
    $responseString = json_encode($childDecode, 1);
    if ( $responseCode !== 200 && $responseCode !== 403) {    // Anything other than a 200 OK is an issue
      decideResponse($responseCode, $responseString );
    }
    elseif ( $responseCode == 403) {
      load403Warn("Expired access credentials");
      $quitEarly = 1;
    }
    else {
      // After a successful creation display success and continue loading page
      successMessage('Device creation is successful.');
      echo '<script type="text/javascript">' . "\n";
      echo 'if ( window.history.replaceState ) {' . "\n";
      echo '  window.history.replaceState( null, null, window.location.href );' . "\n";
      echo '}' . "\n";
      echo '</script>' . "\n";
      $quitEarly = 0;
    }
  }
  elseif ( isset($_GET['hostname']) && isset($_GET['address'])) {
    $post  = [ 'hostname' => $_GET['hostname'] ];
    $post += [ 'address' => $_GET['address'] ];
    $post += [ 'productionState' => 0 ];                        // They dont get to choose if they used a GET
    $callApi = callApiPost('/device/create', $post, $headers);  // returns an array
    $parentDecode = json_decode($callApi['response'], 1);
    $responseCode = $parentDecode['statusCode'];
    $childDecode = json_decode($parentDecode['result'], 1);
    $responseString = json_encode($childDecode, 1);

    if ( $responseCode !== 200 && $responseCode !== 403) {    // Anything other than a 200 OK is an issue
      decideResponse($responseCode, $responseString );
    }
    elseif ( $responseCode == 403) {
      load403Warn("Expired access credentials");
      $quitEarly = 1;
    }
    else {
      // After a successful creation with GET we are done.
      successMessage('Device creation is successful.');
      $quitEarly = 2;
    }
  }
  else {
    // We are simply going to load the page like normal people do
  }


  // This is the main UI where get was not set, and there was no post
  if ( $quitEarly == 0 ) {
?>
    <div class="container-md ">
      <div class="col-md-3 col-md-3">
        <center><h3><b>Create New Host</b></h3></center>
        <form id="form" action="" method="POST">
          <div class="mb-3 md-3">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="popover" title="Hostname" data-bs-content="Full Qualified Domain name.  This should be able to be resolved via DNS, or the local /etc/hosts file">?</button>
            <label for="displayName" class="form-label">FQDN:</label>
            <input type="displayName" class="form-control" id="hostName" placeholder="foo.bar.example.com" name="hostname">
          </div>
          <div class="mb-3">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="popover" title="IP Address" data-bs-content="IP address to talk to this host.  Never use localhost or 127.0.0.1.  You will not be able to activly monitor the host if you do.">?</button>
            <label for="oid" class="form-label">IP Address:</label>
            <input type="displayName" class="form-control" id="address" placeholder="192.168.15.1" name="address">
          </div>
          <div class="mb-3">
            <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="popover" title="Monitor" data-bs-content="0 == monitored host, 1 == not activly monitored">?</button>
            <label for="severity" class="form-label">Is monitoring Active:</label>
            <div>
              <input class="form-check-input" type="radio" name="productionState" id="monitoring" value="0" checked>
              <label class="form-check-label" for="monitoring">Monitored</label>
            </div>
            <div>
              <input class="form-check-input" type="radio" name="productionState" id="noMonitoring" value="1">
              <label class="form-check-label" for="noMonitoring">No Monitoring</label>
            </div>
          </div>
          <button type="submit" class="btn btn-primary" form="form">Create New Host</button>
        </form>
      </div>
    </div>
<?php

  }
  elseif ($quitEarly == 1) {
    // Something went very wrong with the API call, but keep the layout clean...
    loadUnknown("Page load failed in an unusual way.  Please try again.");
  }
  else {
    // Dont load the page, as we succeeded in creating the host and informed the user from a GET call.
  }
