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

  // This should not really be needed, as refreshes will get the same data 99% of the time.
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
    $post = [ 'id' => $id ];
    // We need to grab any perf data in the database
    $rawDevicePerformance = callApiPost("/device/performance", $post, $headers);
    $rawDevicePerformance = json_decode($rawDevicePerformance['response'], true);

    if ($rawDevicePerformance['statusCode'] !== 200) {
      $quitEarly = 1;
    }
    elseif (empty($rawDevicePerformance)) {
      $performanceData = array();
    }
    else {
      // debugger($rawDevicePerformance);
      // exit();
      $performanceData = $rawDevicePerformance['data'];
    }
  }

  /*
    There are going to be 3 types of values in our data.
    These are all going to get the same filtering and look
    set for them.

    1) simple string (rare and likely weird results)
    2) JSON string as k => v pairs
    3) array containing JSON arrays and k => v pairs

    filter logic will look at the first character of the value before doing JSON
    decoding.  If it starts with [ its an array, if it starts with { it is simple
    JSON.  Everything else is considered a simple string.
  */

//   debugger($performanceData);
//   exit();

  if ( $quitEarly == 0 ) {
    ?>
    <center><h1><?php echo 'Details for <a href="/host/index.php?&page=deviceDetails.php&id=' . $id . '">' . $hostname . '</a>' ; ?></h1></center><br>

    <div class="container-fluid">
      <div class="row justsify-content-center">
    <?php
    if ( !empty($performanceData)) {
      foreach ($performanceData as $deviceData) {
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
        $knownPrebuilt = glob(__DIR__ . '/performance/*.php');
        /* loop through every match */
        foreach($knownPrebuilt as $singlePrebuilt) {
          if(strpos($singlePrebuilt, $deviceData['checkName']) !== False) {
            $cardSize = '';
            $cardRadius = '2%';
            include_once($singlePrebuilt);
          } // end if
        }  // end inside foreach
      } // end foreach
    }
    else {
      /* We are missing a prebuilt page */
      switch ($deviceData['value']) {
        /* support finding an array first */
        case is_array($deviceData['value']) !== false:
          echo '<table class="table table-striped table-bordered" style="white-space: nowrap;"><b>' . $deviceData['checkName'] . '</b><br>Last Update: ' . $deviceData['date']. "\n";
          echo '<thead><tr><th>Name</th><th>Value</th></tr></thead>' . "\n";
          echo '<tbody>' . "\n";
          $valueData = json_decode($deviceData['value'], true);
          foreach ($valueData as $value) {
            foreach($value as $k => $v) {
              echo '<tr><td>' . $k . '</td><td>' . $v . '</td></tr>' . "\n";
            }
          }
          echo '</tbody>' . "\n";
          echo '</table>' . "\n";
          break;
        /* support NOT getting a JSON string */
        case str_starts_with($deviceData['value'], '{') !== false:
          $valueData = json_decode($deviceData['value'], true);
          echo '<table class="table table-striped table-bordered" style="white-space: nowrap;"><b>' . $deviceData['checkName'] . '</b><br>Last Update: ' . $deviceData['date']. "\n";
          echo '<thead><tr><th>Name</th><th>Value</th></tr></thead>' . "\n";
          echo '<tbody>' . "\n";
          foreach ($valueData as $k => $v) {   // basic K => V pairs..
            if (is_array($v)) {                // Nested K => pairs (gah)
              foreach ($v as $finalKey => $finalValue) {
                echo '<tr><td>' . $k . '</td><td>' . $finalKey . ' ' . $finalValue . '</td></tr>' . "\n";
              }
            }
            else {
              echo '<tr><td>' . $k . '</td><td>' . $v . '</td></tr>' . "\n";
            }
          }
          echo '</tbody>' . "\n";
          echo '</table>' . "\n";
        break;
        /* Support a catchall last resort match likely will never work correctly */
        default:
          // This is going to be a basic table, but allow for accordion as well
          echo '<table class="table table-striped table-bordered" style="white-space: nowrap;"><b>' . $deviceData['checkName'] . '</b><br> Last Update: ' . $deviceData['date'] . "\n";
          echo '<thead><tr><th>Value</th></tr></thead>'."\n";
          echo '<tbody>' . "\n";
          echo '<tr><td>' . $deviceData['value'] . '</td></tr>' . "\n";
          echo '</body>' . "\n";
          echo '</table>' . "\n";
          break;
        } // end switch
    } // end if
  } // end first if
  else {
    // Something went very wrong with the API call, but keep the layout clean...
    loadUnknown("Page load failed in an unusual way.  Please reload, or go back one page and try again.");
  }

?>
    </div>
  </div>
