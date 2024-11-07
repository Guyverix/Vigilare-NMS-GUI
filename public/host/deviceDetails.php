<?php
  /*
    Show details for a given host or device.
    Allow for simple alteration of monitors
    Reflect active events, and allow for historical
    searches to be done at the host level.
  */

  echo "<br><br><br>";
  // Only needed for debugging and bypassing security, etc
  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";

  // Hosts and Devices have A LOT of variables in play.  We need functions specific to this group
  require_once __DIR__ . "/functions/hostFunctions.php";

  // Grab our POSSIBLE values so users can choose what they change
  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
  $post = array();  // We are using post, so give it an empty array to post with
  $quitEarly = 0;


  // This is from an INTERNAL POST ONLY when we have changed something specific
  if ( isset($_POST['findProperties'])) {
    $post = ['id' => $_POST['id']];
    $discoverMyDevice = callApiPost("/discovery/discover", $post, $headers);
    $rawResponse = json_decode($discoverMyDevice['response'], true);
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
      successMessage('Device discovery is complete.');
      $quitEarly = 0;
    }
  }

  // Success or failure, continue loading the page
  $osImg="/images/unsorted/question_mark.png";

  // Begin setting values
  if ( isset($_GET['id'])) {
    $id=$_GET['id'];
  }

  // Kind of catchall for device and host
  $post = [ 'id' => $id ];

  $rawDevicePerformance = callApiPost("/device/performance", $post, $headers);
  $rawDevicePerformance = json_decode($rawDevicePerformance['response'], true);

  $rawDeviceProperties = callApiPost("/device/properties", $post, $headers);
  $rawDeviceProperties = json_decode($rawDeviceProperties['response'], true);

  $rawActiveMonitors = callApiPost("/monitoringPoller/checkName", $post, $headers);
  $rawActiveMonitors = json_decode($rawActiveMonitors['response'], true);

  $rawActiveEvents = callApiGet("/events/findActiveEventByDeviceId/$id", $headers);
  $rawActiveEvents = json_decode($rawActiveEvents['response'], true);

  $rawHistoryEvents = callApiGet("/events/findHistoryEventByDeviceId/$id", $headers);
  $rawHistoryEvents = json_decode($rawHistoryEvents['response'], true);

  $rawAvail = callApiPost("/events/findAliveTime", $post, $headers); // looks 30 days back
  $rawAvail = json_decode($rawAvail['response'], true);
  $availableTime = $rawAvail['data'][0]['totalDowntime'];

  $rawHistoryTime = callApiPost("/events/findHistoryTime", $post, $headers);
  $rawHistoryTime = json_decode($rawHistoryTime['response'], true);
  $historyTime = $rawHistoryTime['data'][0]['totalDowntime'];

  $rawEventTime = callApiPost("/events/findEventTime", $post, $headers);
  $rawEventTime = json_decode($rawEventTime['response'], true);
  $eventTime = $rawEventTime['data'][0]['totalDowntime'];

  $now = new DateTime();
  $now->sub(new DateInterval('P30D'));
  $timeBand = $now->format('Y-m-d H:i:s');

  // Begin data munging so we can display stuff now that we have retrieved our data

  // This is needed because periods cause heartburn at times, esp with Graphite
  $specialHostname = preg_replace('/\./','_', $rawDeviceProperties['data'][0]['hostname']);

  // Get a list of our storage types in monitors
  if ( ! empty($rawActiveMonitors['data'])) {
    foreach ($rawActiveMonitors['data'] as $activeMonitors) {
      $storage[] = $activeMonitors['storage'];
    }
  }
  else {
    $storage = array();
  }

  $storage = array_unique($storage);

  // Find our alarm counts for display
  $sev = alarmCount($rawActiveEvents);

  // Is this something we can drop monitors on?
  $monitorable = isMonitorable($rawDeviceProperties['data'][0]['productionState']);

  // Need to calculate these.  Placeholders right now...
  $alarmTime = $eventTime . " minutes";
  $historyTime = $historyTime . " minutes";
  $availabilityRaw = calcPercentage($availableTime , $timeBand);
  // debugger($availabilityRaw);
  if ( $availabilityRaw <= 0 ) { $availabilityRaw = 0; }
  $availability = $availabilityRaw . "%";

  // New servers should not show availability when they have been active less than 30 days
  $seenDate = $rawDeviceProperties['data'][0]['firstSeen'];
  $epochSeen = strtotime("$seenDate");
  $dateNow = time() - (30 * 24 * 60 * 60);;

  if ( $dateNow < $epochSeen ) {
    $bypass = 'new' ;
  }
  else {
    $bypass = 'live';
  }
  if ( $bypass == 'new' ) {
    $availability = "New Server Bypass";
  }


  // Get our database information cleaned up from hrSystem SNMP storage
  $deviceInformation = hrSystem($rawDevicePerformance);
  $hrSystemUpdate = hrSystemDate($rawDevicePerformance);

  // Figure out our host OS if possible
  $hostOs = hostOs($rawDevicePerformance);
  $osImg = osImages($hostOs);

  /*
     Use the alive type to see if the device is
     actively monitored.  we CAN have monitors against
     a device and still have it be considered unmonitored.
     IE snmptrap, or even from local checks.
  */

  $isMonitored = '<img src="/images/generic/orange_dot.png" style="width:20px;height:20px;"> Inctive </img>' . "\n";

  if ( is_array($rawDeviceProperties['data'])) {
    foreach ($rawDeviceProperties['data'] as $findAlive) {
      if ( $findAlive['isAlive'] == 'alive' ) {
        $isMonitored = '<img src="/images/generic/green_dot.png" style="width:20px;height:20px;"> Alive </img>' . "\n";
      }
      elseif ( $findAlive['isAlive'] == 'dead' ) {
        $isMonitored = '<img src="/images/generic/red_dot.png" style="width:20px;height:20px;"> Dead </img>' . "\n";
      }
    }
  }

  // Show if we CAN ( not do ) have active checks
  $activeMonitors = '<img src="/images/generic/grey_dot.png" style="width:20px;height:20px;"> Active Monitors Disabled </img>' . "\n";
  if ( isset($rawDeviceProperties['data'][0]['productionState']) && $rawDeviceProperties['data'][0]['productionState'] == 0 ) {
    $activeMonitors = '<img src="/images/generic/green_dot.png" style="width:20px;height:20px;"> Active Monitors Enabled </img>' . "\n";
  }

  // Show if SNMP is configured and available
  $snmpState = '<img src="/images/generic/grey_dot.png" style="width:20px;height:20px;"> SNMP Inactive </img>' . "\n";
  if (isset($rawDeviceProperties['data'][0]['properties'])) {
    $findSnmpState = json_decode($rawDeviceProperties['data'][0]['properties'], true);
    if ($findSnmpState['snmpEnable'] == 'true') {
     $snmpState = '<img src="/images/generic/green_dot.png" style="width:20px;height:20px;"> SNMP Enabled </img>' . "\n";
    }
  }

  // Not existing yet, but have the bubble in place for when code exists
  $maintenanceState = '<img src="/images/generic/grey_dot.png" style="width:20px;height:20px;"> No Active Maintenance </img>' . "\n";

//  echo "<br><br><br>";
//  debugger($rawDevicePerformance);
//  exit();

/*
  debugger($rawDeviceProperties);
  exit();
  echo "<br><br><br>";
  debugger($rawDeviceProperties);
  debugger($rawActiveMonitors);
  debugger ($deviceInformation);
  debugger($sev);
  debugger($rawActiveEvents);
  debugger($rawHistoryEvents);
  debugger($rawActiveMonitors);
  debugger($storage);
  exit();
*/


  if ( $quitEarly == 0 ) {
  ?>
  <!-- begin tabbed interface -->
  <center><h1>Device</h1></center>
  <div class="container">
    <?php
      // Only findPropterties needs to talk to the API at this point
      echo '<form id="findProperties" action="" method="POST"><input type="hidden" name="id" value="' . $id . '"></form>' . "\n";

      // Add additional hidden inputs with the data we have already pulled.  Dont call the API unless needed for something else
      echo '<form id="hostProperties" method="POST" action="/host/index.php?&page=hostPropertiesEdit.php">' . "\n";
        echo '<input type="hidden" name="id" value="' . $id . '">' . "\n";
        echo '<input type="hidden" name="hostname" value="' . $rawDeviceProperties['data'][0]['hostname'] . '">' . "\n";
        echo '<input type="hidden" name="deviceProperties" value="' . htmlspecialchars($rawDeviceProperties['data'][0]['properties']) . '">' . "\n";
      echo '</form>' . "\n";

      echo '<form id="hostModify"     action="/host/index.php?&page=modifyDevice.php"          method="POST">' . "\n";
        echo '<input type="hidden" name="id" value="' . $id . '">' . "\n";
        echo '<input type="hidden" name="hostname" value="' . $rawDeviceProperties['data'][0]['hostname'] . '">' . "\n";
        echo '<input type="hidden" name="address" value="' . $rawDeviceProperties['data'][0]['address'] . '">' . "\n";
        echo '<input type="hidden" name="firstSeen" value="' . $rawDeviceProperties['data'][0]['firstSeen'] . '">' . "\n";
        echo '<input type="hidden" name="productionState" value="' . $rawDeviceProperties['data'][0]['productionState'] . '">' . "\n";
        echo '<input type="hidden" name="isAlive" value="' . $rawDeviceProperties['data'][0]['isAlive'] . '">' . "\n";
      echo '</form>' . "\n";

      echo '<form id="hostDelete"     action="/host/index.php?&page=deviceDelete.php" method="POST">' . "\n";
        echo '<input type="hidden" name="id" value="' . $id . '">' . "\n";
        echo '<input type="hidden" name="hostname" value="' . $rawDeviceProperties['data'][0]['hostname'] . '">' . "\n";
        echo '<input type="hidden" name="address" value="' . $rawDeviceProperties['data'][0]['address'] . '">' . "\n";
        echo '<input type="hidden" name="firstSeen" value="' . $rawDeviceProperties['data'][0]['firstSeen'] . '">' . "\n";
        echo '<input type="hidden" name="productionState" value="' . $rawDeviceProperties['data'][0]['productionState'] . '">' . "\n";
        echo '<input type="hidden" name="isAlive" value="' . $rawDeviceProperties['data'][0]['isAlive'] . '">' . "\n";
      echo '</form>' . "\n";

      echo '<form id="hostMonitors" method="POST" action="/host/index.php?&page=deviceMonitors.php">' . "\n";
        echo '<input type="hidden" name="id" value="' . $id . '">' . "\n";
        echo '<input type="hidden" name="hostname" value="' . $rawDeviceProperties['data'][0]['hostname'] . '">' . "\n";
        echo '<input type="hidden" name="activeMonitors" value="' . htmlspecialchars(json_encode($rawActiveMonitors['data'], 1)) . '">' . "\n";
      echo '</form>' . "\n";

      echo '<form id="addMonitors"    action="/host/addMonitors.php"         method="POST"><input type="hidden" name="id" value="' . $id . '"></form>' . "\n";

      echo '<form id="hostGraphs"     action="/host/index.php?&page=deviceGraphs.php"          method="POST">' . "\n";
        echo '<input type="hidden" name="id" value="' . $id . '">' . "\n";
        echo '<input type="hidden" name="hostname" value="' . $rawDeviceProperties['data'][0]['hostname'] . '">' . "\n";
        echo '<input type="hidden" name="activeMonitors" value="' . htmlspecialchars(json_encode($rawActiveMonitors['data'], 1)) . '">' . "\n";
      echo '</form>' . "\n";

      echo '<form id="performance"    action="/host/index.php?&page=devicePerformance.php"   method="POST">' . "\n";
        echo '<input type="hidden" name="id" value="' . $id . '">' . "\n";
        echo '<input type="hidden" name="id" value="' . $id . '">' . "\n";
        echo '<input type="hidden" name="hostname" value="' . $rawDeviceProperties['data'][0]['hostname'] . '">' . "\n";
        echo '<input type="hidden" name="performanceData" value="' . htmlspecialchars(json_encode($rawDevicePerformance['data'], 1)) . '">' . "\n";
      echo '</form>' . "\n";

      // Decide if we have run discovery against host before or not
      if ( ! isset($rawDeviceProperties['data'][0]['properties'])) {
        echo '<button form="findProperties" name="findProperties" type="submit" class="btn btn-success">Discover Properties</button> ' . "\n";
      }
      else {
        echo '<button form="hostProperties" type="submit" class="btn btn-primary">Change Properties</button> ' . "\n";
      }
      echo '<button form="hostModify" type="submit" class="btn btn-warning">Modify Device</button> ' . "\n";

      // Decide if we have monitors to show or not
      if ( isset($rawActiveMonitors['data'][0])) {
        echo '<button form="hostMonitors" type="submit" class="btn btn-primary">Change Monitors</button> ' . "\n";
      }
      else {
        echo '<button form="hostMonitors" type="submit" class="btn btn-success">Add Monitors</button> ' . "\n";
      }

      // Decide if we have Host or Device components we are aware of for display ( from database not graphs? )
      if ( isset($rawDevicePerformance['data'][0])) {
        echo '<button form="performance" type="submit" class="btn btn-primary">Device Performance</button> ' . "\n";
      }

      // Decide if we have any RRD or Graphite graphs to display (influx will come with V2?)
      if ( in_array("rrd", $storage) || in_array("graphite", $storage)) {
        echo '<button form="hostGraphs" type="submit" class="btn btn-primary">Graphs</button> ' . "\n";
      }

      echo '<button class="btn btn-primary"> &nbsp </button> ' . "\n";  // Just a simple spacer that does nothing
      echo '<button form="hostDelete" type="submit" class="btn btn-danger">Delete Device</button> ' . "\n";



      // We now have our tabs across the top.
      // Build out our Device table now.
      echo '<table class="table table-striped bg-dark table-dark"><tbody>' . "\n";
      //      echo '<tr><td><b>Device:</b> ' . $rawDeviceProperties['data'][0]['hostname'] . '</td><td><b>Device Id:</b> ' . $rawDeviceProperties['data'][0]['id'] . '</td><td><b>Address:</b> ' . $rawDeviceProperties['data'][0]['address'] . '</td><td>' . $isMonitored . '<br>' . $activeMonitors . '<br>' .  $snmpState . '<br>' . $maintenanceState . '</td></tr>' . "\n";
      echo '<tr>' . "\n";
      echo '<td><b>Device:</b> ' . $rawDeviceProperties['data'][0]['hostname'] . '<br>' . "\n";
      echo '<b>Device Id:</b> ' . $rawDeviceProperties['data'][0]['id'] . '<br>' . "\n";
      echo '<b>Address:</b> ' . $rawDeviceProperties['data'][0]['address'] . '</td>' . "\n";
      echo '<td>' . $isMonitored . '<br>' . $activeMonitors . '<br>' .  $snmpState . '<br>' . $maintenanceState . '</td></tr>' . "\n";
        // Build subtable left side
        echo '<tr><td>' . "\n";
        echo '<table class="table table-striped bg-dark table-dark"><tbody>' . "\n";
        echo '<tr><td><img src="' . $osImg . '" style="width:250px;height:250px;"></img></td>' . "\n";
        echo '<td>' . "\n";
          foreach (array_reverse($sev) as $singleSeverity) {
            echo '<button class="btn btn-' . $singleSeverity['color'] . '">' . $singleSeverity['count'] . '</button><br>' . "\n";
          }
          echo '</td><td>' . "\n";
          // second column sub-sub table (sue me, it works)
          echo '<table class="table table-striped bg-dark table-dark"> <center><b> Details 30 days </b></center> <tbody>' . "\n";
            echo '<tr><td align="right">Availability:</td><td> ' . $availability . '</td></tr>' . "\n";
            echo '<tr><td align="right">Active alarm SUM:</td><td> ' . $alarmTime . '</td></tr>' . "\n";
            echo '<tr><td align="right">History alarm SUM:</td><td> ' . $historyTime . '</td></tr>' . "\n";
            echo '<tr><td align="right">Monitorable:</td><td> ' . $monitorable . '</td></tr>' . "\n";
            echo '<tr><td align="right">First Seen:</td><td> ' . $rawDeviceProperties['data'][0]['firstSeen'] . '</td></tr>' . "\n";
          echo '</tbody></table>' . "\n";
          echo '</td>' . "\n";
        echo '<tr>' . "\n";
        echo '</tbody></table></td>' . "\n";
        // thrid column
        echo '<td colspan=2>' . "\n";
          echo '<table class="table table-striped bg-dark table-dark"><center><b>System information: ' . $hrSystemUpdate . '</b><center><tbody>' . "\n";
          foreach($deviceInformation as $deviceDetails) {
            foreach($deviceDetails as $hrSystem => $hrValue) {
              echo '<tr><td align="right">' . $hrSystem . ':</td><td>' . $hrValue . '</td></tr>' . "\n";
            }
          }
          echo '</tbody></table>' . "\n";
        echo '</td></tr>' . "\n";
      echo '</tbody></table>' . "\n";


  // At this point we are at the end of the define UI page.
  // now build simple-datatable with active and historical events
  echo '<table id="dt-host-events" class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;">Active Events' . "\n";
  echo '<head><tr><th>Start</th><th>Summary</th></tr></head>' . "\n";
  echo '<tbody>' . "\n";
  if ( count($rawActiveEvents['data']) == 0 ) {
    echo "<tr><td colspan=2 class='table-success'><center>No active events found</center></td></tr>";
  }
  else {
    foreach ($rawActiveEvents['data'] as $activeEvent) {
          switch ($activeEvent['eventSeverity']) {
            case "0":
              $rowColor=' class="table-success"';
              $linkColor=' class="link-danger"';
              break;
            case "1":
              $rowColor=' class="table-secondary"';
              $linkColor=' class="link-danger"';
              break;
            case "2":
              $rowColor=' class="table-primary"';
              $linkColor=' class="link-danger"';
              break;
            case "3":
              $rowColor=' class="table-info"';
              $linkColor=' class="link-danger"';
              break;
            case "4":
              $rowColor=' class="table-warning"';
              $linkColor=' class="link-danger"';
              break;
            case "5":
              $rowColor=' class="table-danger"';
              $linkColor=' class="link-primary"';
              break;
          }
      echo "<tr><td " . $rowColor . ">" . $activeEvent['startEvent'] . "</td><td " . $rowColor . ">" . $activeEvent['eventSummary'] . "</td></tr>";
    }
  }
  echo "</tbody></table>";
?>
  <table id="dt-host-history" class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;">Historical Events
  <head><tr><th>End</th><th>Summary</th></tr></head>
<?php
  if ( count($rawHistoryEvents['data']) == 0 ) {
    echo "<tr><td colspan=2 class='table-success'><center>No historical events found</center></td></tr>";
  }
  else {
    foreach ($rawHistoryEvents['data'] as $historyEvent) {
          switch ($historyEvent['eventSeverity']) {
            case "0":
              $rowColor=' class="table-success"';
              $linkColor=' class="link-danger"';
              break;
            case "1":
              $rowColor=' class="table-secondary"';
              $linkColor=' class="link-danger"';
              break;
            case "2":
              $rowColor=' class="table-primary"';
              $linkColor=' class="link-danger"';
              break;
            case "3":
              $rowColor=' class="table-info"';
              $linkColor=' class="link-danger"';
              break;
            case "4":
              $rowColor=' class="table-warning"';
              $linkColor=' class="link-danger"';
              break;
            case "5":
              $rowColor=' class="table-danger"';
              $linkColor=' class="link-primary"';
              break;
          }
      echo "<tr><td " . $rowColor . ">" . $historyEvent['endEvent'] . "</td><td " . $rowColor . ">" . $historyEvent['eventSummary'] . "</td></tr>\n";
    }
  }
  echo "</table>";
  ?>
  <!-- Add our JS here so we dont have to escape or make it look uglier than it does -->
  <script> window.addEventListener("DOMContentLoaded", event => {
    const datatablesSimple = document.getElementById("dt-host-events");
    if (datatablesSimple) {
      new simpleDatatables.DataTable("#dt-host-events", {
        searchable: true,
        sortable: true,
        storable: true,
        paging: true,
        perPage: 5,
        perPageSelect:[5,10,25,50],
        labels: {
          placeholder: "Search Active Events"
        }
        });
      }
    });
  </script>

  <script> window.addEventListener("DOMContentLoaded", event => {
    const datatablesSimple = document.getElementById("dt-host-history");
    if (datatablesSimple) {
      new simpleDatatables.DataTable("#dt-host-history", {
        searchable: true,
        sortable: true,
        storable: true,
        paging: true,
        perPage: 10,
        perPageSelect:[10,25,50,100],
        labels: {
          placeholder: "Search Historical Events"
        }
        });
      }
    });
  </script>


  <!-- datatables not loaded with footer, add it now -->
  <script src="/js/simple-datatables/simple-datatables.js"></script>



  <?php
  }
  else {
    // Something went very wrong with the API call, but keep the layout clean...
    loadUnknown("API calls failed in an unexpected way.  Please reload");
  }
?>
