<?php
  /*
    Show device Perfromance, well really it is more of the statistical information
    available from the API call for information kept in the database itself.
    This CAN show stuff that is graphable, but that is not the intent.  In general
    I was looking at this as closer to static data that can slowly change over time
    IE ports being listened to, or IP routes that can change over time.  These things
    can be run on slower iteration cycles, we just want to keep a record of these things
    and show them on demand.
  */

  echo "<br><br><br>";
  // Only needed for debugging and bypassing security, etc
  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  // checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";

  // Hosts and Devices have A LOT of variables in play.  We need functions specific to this group
  require_once __DIR__ . "/functions/hostFunctions.php";

  // This should not really be needed, as likely refreshes will get the same data 99% of the time.
  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
  $post = array();  // We are using post, so give it an empty array to post with
  $quitEarly = 0;


  // Set default data for the page
  if (isset($_POST['id'])) {
    $id = $_POST['id'];
  }
  else {
    $quitEarly = 1;
  }

  if (isset($_POST['hostname'])) {
    $hostname = $_POST['hostname'];
  }
  else {
    $quitEarly = 1;
  }

  if (isset($_POST['performanceData'])) {
    $performanceData = $_POST['performanceData'];
  }
  else {
    $performanceData = array();
  }

  /*
    There are going to be 3 types of values in our data.
    These are all going to get the same filtering and look
    set for them.

    1) simple string
    2) JSON string as k => v pairs
    3) array containing JSON arrays and k => v pairs

    filter logic will look at the first character of the value before doing JSON
    decoding.  If it starts with [ its an array, if it starts with { it is simple
    JSON.  Everything else is considered a simple string.

    I am thinking an accordion style page is the best way to deal with adhoc data
    however, I will likely have straight tables as well.  If there are many database
    entries, it will get really big really quick, and accordion will likely be
    the best way to go, but initially with a smaller set of entries, straight tables
    seems both reasonable and pretty?
  */

// debugger(json_decode($performanceData,true));
// exit();

  if ( $quitEarly == 0 ) {
  ?>
  <center><h1><?php echo 'Performance for <a href="/host/index.php?&page=deviceDetails.php&id=' . $id . '">' . $hostname . '</a>' ; ?></h1></center>
  <div class="container">
  <?php
  $acc = 'true';
  foreach ( json_decode($performanceData, true) as $deviceData) {
    /*
      We are going to have "known" types to deal with and then everything else
      Deal with the known types by checkName.  Everything else we are just gonig
      to try like hell to give decent output.
      Heavily nested JSON is the reason for needing more than one validation.
      the ipRoute for example is UGLY in the nesting.

      The chatchall stuff is never going to be as pretty as the known stuff
      but what is the point of saving the info in the db if we are not going
      to use it.
    */
    $knownPrebuilt = ["ipRoute", "portsUsed", "lm-sensors", "hrSystem", "ssIndex2"];
    if (in_array($deviceData['checkName'], $knownPrebuilt)) {
      switch($deviceData['checkName']) {
        case "ipRoute":
          if ( file_exists(__DIR__ . '/performance/' . $deviceData['checkName'] . '.php')) {
            include_once __DIR__ . ('/performance/' . $deviceData['checkName'] . '.php');
          }
          break;
        case "portsUsed":
          if ( file_exists(__DIR__ . '/performance/' . $deviceData['checkName'] . '.php')) {
            include_once __DIR__ . '/performance/' . $deviceData['checkName'] . '.php';
          }
          break;
        case "lm-sensors":  // This one is pretty messy!
          if ( file_exists(__DIR__ . '/performance/' . $deviceData['checkName'] . '.php')) {
            include_once __DIR__ . '/performance/' . $deviceData['checkName'] . '.php';
          }
          break;
        case "hrSystem":
          // This is on the main page, no need to duplicate it here
          break;
        case "ssIndex2":
          if ( file_exists(__DIR__ . '/performance/' . $deviceData['checkName'] . '.php')) {
            include_once __DIR__ . '/performance/' . $deviceData['checkName'] . '.php';
          }
          break;
        case "somethingElse":
          echo "Create Your Table Here Silly";
          break;
      }
    }
     // We dont have anything specific "prebuilt" so just try to show the data as best we can
    else {
      switch ($deviceData['value']) {
        case is_array($deviceData['value']) !== false:
          echo '<table class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;"><b>' . $deviceData['checkName'] . ' Last Update: ' . $deviceData['date']. '</b>';
          echo '<thead><tr><th>Name</th><th>Value</th></tr></thead><tbody>';
          $valueData = json_decode($deviceData['value'], true);
          foreach ($valueData as $value) {
            foreach($value as $k => $v) {
              echo '<tr><td>' . $k . '</td><td>' . $v . '</td></tr>';
            }
          }
          echo '</tbody>';
          echo '</table>';
        break;
        case str_starts_with($deviceData['value'], '{') !== false:
//          echo "<br>DEUBG " .  print_r($deviceData, true) . "<br>\n";
          $valueData = json_decode($deviceData['value'], true);
          echo '<table class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;"><b>' . $deviceData['checkName'] . ' Last Update: ' . $deviceData['date']. '</b>';
          echo '<thead><tr><th>Name</th><th>Value</th></tr></thead><tbody>';
//debugger($valueData);
          foreach ($valueData as $k => $v) {   // basic K => V pairs..
            if (is_array($v)) {                // Nested K => pairs (gah)
              foreach ($v as $finalKey => $finalValue) {
                echo '<tr><td>' . $k . '</td><td>' . $finalKey . ' ' . $finalValue . '</td></tr>';
              }
            }
            else {
              echo '<tr><td>' . $k . '</td><td>' . $v . '</td></tr>';
            }
          }
          echo '</tbody>';
          echo '</table>';
        break;
        default:
          // This is going to be a basic table, but allow for accordion as well
          if ( $acc == 'true' ) {
            $title = $deviceData['checkName'];
            echo '<div class="container mt-2">';
              echo '<div class="accordion" id="' . $title . '_Head\">';
                echo '<div class="accordion-item">';
                  echo '<h2 class="accordion-header" id="' . $title . '">';
                    echo '<button class="accordion-button bg-light collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#div' . $title . '" aria-expanded="true" aria-controls="div"' . $title . '\">' . preg_replace('/_/', ' ', $title) . '</button>';
                    // echo '<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#div' . $title . '" aria-expanded="true" aria-controls="div"' . $title . '\">' . preg_replace('/_/', ' ', $title) . '</button>';
                  echo '</h2>';
                  echo '<div id="div' . $title . '" class="accordion-collapse bg-light collapse" aria-labelledby="' . $title . '" data-bs-parent="#' . $title . '_Head">';
                    echo '<div class="accordion-body">';
          }
          echo '<table class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;"><b>' . $deviceData['checkName'] . ' Last Update: ' . $deviceData['date'] . '</b>';
          echo '<thead><tr><th>Value</th></tr></thead><tbody>';
          echo '<tr><td>' . $deviceData['value'] . '</td></tr>';
          echo '</body>';
          echo '</table>';
          if ($acc == 'true') {
                      echo '</div>';
                    echo '</div>';
                  echo '</div>';
                echo '</div>';
              echo '</div>';
            echo '</div>';
          }
        break;
      } // end switch
    }  // end else
  }  // end  foreach
  ?>
  </div>
  <?php

  }

  else {
    // Something went very wrong with the API call, but keep the layout clean...
    loadUnknown("Page load failed in an unusual way.  Please reload, or go back one page and try again.");
  }
?>
