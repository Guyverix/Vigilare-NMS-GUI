<?php
  // Get our utilities in place asap.

  include_once __DIR__ . "/../../functions/generalFunctions.php";

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";
  echo "<br><br><br>";
  checkCookie($_COOKIE);
  checkTimer($_COOKIE);
  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
  $post = array();  // We are using post, so give it an empty array to post with
  $quitEarly = 0;
  if (empty($_POST)) {
    $quitEarly = 1;
  }

  if ( isset($_POST['submitReport'])) {
    $post = $_POST;
    $rawRunReport = callApiPost('/reporting/run', $post, $headers);
    $runReport = json_decode($rawRunReport['response'], true);
    $responseCode = $runReport['statusCode'];
    $responseString = $runReport['data'];

    if ($responseCode !== 200 && $responseCode !== 403) {    // Anything other than a 200 OK is an issue
      decideResponse($responseCode, $responseString );
      $quitEarly = 1;
      exit(); // Do not let the remainder of the page render
    }
    elseif ( $responseCode == 403) {
      load403Warn("Expired access credentials.");
      $quitEarly = 1;
      exit(); // Do not let the remainder of the page render
    }
    else {
      // After a successful update, wait and then load the main page
      successMessage($responseString);
      $quitEarly = 2;
      echo '<script>
              window.setTimeout(function() {
                window.location = "/reporting/index.php?&page=searchExistingReporting.php";
              }, 3000);
            </script>';
    }
  }

  $rawPost = json_decode($_POST['templateArgs'],true);
  $template = $rawPost['template'];
  $usedVars = json_decode($rawPost['usedVars'],true);

  if ( $quitEarly == 0 ) {
  // This is the landing page
  ?>
<div class="container">
  <div class=" text-center mt-5 ">
    <h1>Set Report Variables for <?php echo $template; ?></h1><br>
  </div>
  <div class="row">
    <div class="col align-self-center">
      <div class="controls">
        <div class="row">
          <div class="col-4">
            <form id="run-report-form" role="form" action="" method="POST">
            <div class="form-group">
              <?php
              foreach ($usedVars as $singleVar) {
                echo "<center><label for='form_" . $singleVar . "'>Set value for: <b>" . $singleVar . "</b></label></center>\n";
                echo "<center><input id='form_" . $singleVar . "' type='text' name='" . $singleVar . "' class='form-control' value='' required='required' data-error='You must set a value that matches what the filter is expecting.'></center>\n";
              }
              echo "<input type='hidden' name='template' value='" . $template . "'>";
              echo "<center><button form='run-report-form' type='submit' name='submitReport' value='true' class='btn btn-info btn-md'>Run Report Now</button></center>";
              echo "</div> <!-- form-group -->\n";
              echo "</form>";
              echo " </div>  <!-- col -->\n";
              ?>
    <div class="col">
      <table class='table table-bordered table-striped bg-dark table-light'><b>Common Variable Values</b>
        <!-- yes, there are Easter Eggs here, byte me -->
        <tr><th>Common Name</th><th>Example Values</th></tr>
        <tr><td>startEvent</td><td>timestamp in the format of: 1972-06-22 07:11:00</td><tr>
        <tr><td>endEvent</td><td>timestamp in the format of: 2199-10-09 09:12:00, or now()</td><tr>
        <tr><td>eventAddress</td><td>IPv4 or IPv6 address: 192.168.0.1</td><tr>
        <tr><td>eventAgeOut</td><td>integer: 86400</td><tr>
        <tr><td>eventRaw</td><td>string: "foo bar baz"</td><tr>
        <tr><td>eventReceiver</td><td>string: "trap" or IP address</td><tr>
        <tr><td>eventDetails</td><td>string: "foo bar baz"</td><tr>
        <tr><td>eventProxyIp</td><td>string: IP address or hostname</td><tr>
        <tr><td>eventType</td><td>integer: 0</td><tr>
        <tr><td>eventMonitor</td><td>integer: 1</td><tr>
        <tr><td>eventSummary</td><td>string: "foo bar baz"</td><tr>
        <tr><td>randomCustomVar</td><td>Likely string, but must ask report owner</td><tr>
      </table>
    </div>  <!-- end column -->
  </div> <!-- end row -->
  <?php
  }
  elseif ($quitEarly == 1) {
    // Show our generic error page
    loadUnknown("API calls failed in an unexpected way.  Please go back to parent page and try again.");
  }
  else {
    // catchall....
  }
