<?php
  /*
    Show details for a given host or device.
    Allow for simple alteration of monitors
    Reflect active events, and allow for historical
    searches to be done at the host level.

   // End result should be fall through logic from login stating which CSS
   // set to use.  Hard code for now though..

   <link href="/css/zenoss/base-min.css" rel="stylesheet">
   <link href="/css/zenoss/zenoss_base.css" rel="stylesheet">
   <link href="/css/zenoss/zen_event_styles.css" rel="stylesheet">
   <link href="/css/zenoss/zenoss_console_styles.css" rel="stylesheet">


  */
?>
   <link href="/css/vigilare/test_styles.css" rel="stylesheet">

<?php
  // echo "<br><br><br>";  // Drops the page too far for my liking
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
  $sharedDevice['performance'] = $rawDevicePerformance;

  $rawDeviceProperties = callApiPost("/device/properties", $post, $headers);
  $rawDeviceProperties = json_decode($rawDeviceProperties['response'], true);
  $specialHostname = preg_replace('/\./','_', $rawDeviceProperties['data'][0]['hostname']);
  $sharedDevice['properties'] = $rawDeviceProperties;
  $sharedDevice['properties']['data'][0]['graphiteHostname'] = $specialHostname;

  $rawActiveMonitors = callApiPost("/monitoringPoller/checkName", $post, $headers);
  $rawActiveMonitors = json_decode($rawActiveMonitors['response'], true);
  $sharedDevice['monitors'] = $rawActiveMonitors;

  $rawActiveEvents = callApiGet("/events/findActiveEventByDeviceId/$id", $headers);
  $rawActiveEvents = json_decode($rawActiveEvents['response'], true);
  $sharedDevice['activeEvents'] = $rawActiveEvents;

  $rawHistoryEvents = callApiGet("/events/findHistoryEventByDeviceId/$id", $headers);
  $rawHistoryEvents = json_decode($rawHistoryEvents['response'], true);
  $sharedDevice['historyEvents'] = $rawHistoryEvents;

  $rawAvail = callApiPost("/events/findAliveTime", $post, $headers); // looks 30 days back
  $rawAvail = json_decode($rawAvail['response'], true);
  $sharedDevice['availibilityRaw'] = $rawAvail;

  $sharedDevice['availibility'] = $rawAvail;

  $rawHistoryTime = callApiPost("/events/findHistoryTime", $post, $headers);
  $rawHistoryTime = json_decode($rawHistoryTime['response'], true);
  $sharedDevice['historyTime'] = $historyTime;


  $rawEventTime = callApiPost("/events/findEventTime", $post, $headers);
  $rawEventTime = json_decode($rawEventTime['response'], true);
  $sharedDevice['eventTime'] = $eventTime ;

  $now = new DateTime();
  $now->sub(new DateInterval('P30D'));
  $timeBand = $now->format('Y-m-d H:i:s');

//  debugger($sharedDevice);
//  exit();
  /*
     Begin data munging so we can display stuff now that we have retrieved our data
     This should be all the maths and server side work that needs to be done before
     sending to the client
  */

  // Get a list of our storage types in monitors
  if ( ! empty($sharedDevice['monitors'])) {
    foreach ($sharedDevice['monitors'] as $activeMonitors) {
      $storage[] = $activeMonitors['storage'];
    }
  }
  else {
    $storage = array();
  }
  // We only want an array of each type used.  We could try to get from the main array later
  $storage = array_unique($storage);

  // Find our alarm counts for display
  $sev = alarmCount($sharedDevice['activeEvents']);

  // Is this something we can drop monitors on?
  $monitorable = isMonitorable($sharedDevice['properties']['data'][0]['productionState']);

  // Different time metrics and values
  $alarmTime = $sharedDevice['eventTime'] . " minutes";
  $historyTime = $sharedDevice['historyTime'] . " minutes";
  $availableTime = $sharedDevice['availibility']['data'][0]['totalDowntime'];
  $availabilityRaw = calcPercentage($availableTime , $timeBand);

  $eventTime = $sharedDevice['eventTime']['data'][0]['totalDowntime'];
  $historyTime = $sharedDevice['historyTime']['data'][0]['totalDowntime'];

  // Make sure zero is as low as we go
  if ( $availabilityRaw <= 0 ) { $availabilityRaw = 0; }
  $availability = $availabilityRaw . "%";

  // New servers should not show availability when they have been active less than 30 days
  $seenDate = $sharedDevice['properties']['data'][0]['firstSeen'];
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
  $deviceInformation = hrSystem($sharedDevice['performance']);
  $hrSystemUpdate = hrSystemDate($sharedDevice['performance']);

  // Figure out our host OS if possible
  $hostOs = hostOs($sharedDevice['performance']);
  $osImg = osImages($hostOs);

  $sharedDevice['properties']['data'][0]['hostOs'] = $hostOs ?? 'unknown';
  $sharedDevice['properties']['data'][0]['osImg'] = $osImg ?? '/images/unsorted/question_mark.png';

  /*
     Use the alive type to see if the device is
     actively monitored.  we CAN have monitors against
     a device and still have it be considered unmonitored.
     IE snmptrap, or even from local checks.
  */

  // Catchall.  Override later if no longer true
  $isMonitored = '<img src="/images/generic/orange_dot.png" style="width:20px;height:20px;"> Inctive </img>' . "\n";

  if ( is_array($sharedDevice['properties']['data'])) {
    foreach ($sharedDevice['properties']['data'] as $findAlive) {
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
  if ( isset($sharedDevice['properties']['data'][0]['productionState']) && $sharedDevice['properties']['data'][0]['productionState'] == 0 ) {
    $activeMonitors = '<img src="/images/generic/green_dot.png" style="width:20px;height:20px;"> Active Monitors Enabled </img>' . "\n";
  }

  // Show if SNMP is configured and available
  $snmpState = '<img src="/images/generic/grey_dot.png" style="width:20px;height:20px;"> SNMP Inactive </img>' . "\n";
  if (isset($sharedDevice['properties']['data'][0]['properties'])) {
    $findSnmpState = json_decode($sharedDevice['properties']['data'][0]['properties'], true);
    if ($findSnmpState['snmpEnable'] == 'true') {
     $snmpState = '<img src="/images/generic/green_dot.png" style="width:20px;height:20px;"> SNMP Enabled </img>' . "\n";
    }
  }

  // Not existing yet, but have the bubble in place for when code exists
  $maintenanceState = '<img src="/images/generic/grey_dot.png" style="width:20px;height:20px;"> No Active Maintenance </img>' . "\n";

  /*
    After all this, now is the time to start the display
  */

  if ( $quitEarly == 0 ) {
  ?>
  <div class="container-fluid">
    <div class="row-cols-auto">
      <div class="col">
        <center><h2>Device Information</h2></center>
      </div>
    </div>
    <!-- begin tabbed interface -->
    <div class="row">
      <div class="col">
  <?php
      // This should span across the top of the container
      require (__DIR__ . '/displayComponents/deviceTopTabs.php');
      echo "</div>";
    echo "</div>";
//      echo "</div>";
//      echo "</div>";
  echo "<!-- End page DATE " . time() . "-->";
  // This will build out our general information page
  require (__DIR__ . '/displayComponents/deviceGeneral.php');
  }
  else {
    // Something went very wrong with the API call, but keep the layout clean...
    loadUnknown("API calls failed in an unexpected way.  Please reload");
  }
?>
