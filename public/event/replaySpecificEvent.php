<!DOCTYPE html>
<html lang="en">
<!-- <META HTTP-EQUIV=Refresh CONTENT="30">  -->
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Main Event UI Page" />
  <meta name="author" content="Chris Hubbard" />

  <title>Test and Change Trap Event Map UI</title>
  <link href="/js/sb-demo/css/styles.css" rel="stylesheet" />
  <link href="/js/bootstrap-5/css/bootstrap.css" rel="stylesheet" />
</head>

<!-- fas == font awesome javascript.  Has nice icons, etc -->
<!-- https://fontawesome.com/search?m=free  choose icon, and find the name.  Call in the i class= to integrate in -->

<body class="sb-nav-fixed">
  <!-- Navbar -->
  <!-- Upper left to right across the top -->
  <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">

    <!-- Navbar Branding -->
    <a class="navbar-brand ps-3" href="/event/replayEvent.php">Change Trap Events</a>

    <!-- Sidebar Toggle -->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>

    <!-- Navbar Search -->
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
      <div class="input-group">
        <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
        <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
      </div>
    </form>

    <!-- Far right top -->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
      <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
          <li><a class="dropdown-item" href="/user/settings.php">Settings</a></li>
          <li><a class="dropdown-item" href="/user/lockAccount.php">Lock Access</a></li>
          <li><hr class="dropdown-divider" /></li>
          <li>
            <div class="form-check form-switch">
              <label class="form-check-label" for="lightSwitch"> Dark Mode </label>
              <input class="form-check-input" type="checkbox" id="lightSwitch"/>
            </div>
          </li>
          <li><a class="dropdown-item" href="/user/logout.php">Logout</a></li>
        </ul>
      </li>
    </ul>
  </nav>

  <!-- left side vertical menu -->
  <div id="layoutSidenav">
    <div id="layoutSidenav_nav">
      <nav class="sb-sidenav accordion sb-sidenav-dark bg-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
          <div class="nav">
            <div class="sb-sidenav-menu-heading">Main</div>
              <a class="nav-link" href="/index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
              Dashboard
              </a>
              <a class="nav-link" href="/event/index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-bell"></i></div>
              Event
              </a>
              <a class="nav-link" href="/infrastructure/index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-network-wired"></i></div>
              Infrastructure
              </a>
              <a class="nav-link" href="/mapping/index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-diagram-project"></i></div>
              Mapping
              </a>
              <a class="nav-link" href="/reporting/index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-flag"></i></div>
              Reporting
              </a>
              <a class="nav-link" href="/daemon/index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-stopwatch"></i></div>
              Daemon
              </a>


              <div class="sb-sidenav-menu-heading">Support</div>
              <a class="nav-link" href="/admin/index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-lock-open"></i></div>
              Admin
              </a>
              <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
              <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
              Documentation
              <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
              </a>
              <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                  <a class="nav-link" href="/support/hosts.html" target="_blank">Host</a>
                  <a class="nav-link" href="/support/infrastructure.html" target="_blank">Infrastructure</a>
                  <a class="nav-link" href="/support/mapping.html" target="_blank">Mapping</a>
                  <a class="nav-link" href="/support/reporting.html" target="_blank">Reporting</a>
                  <a class="nav-link" href="/support/daemon.html" target="_blank">Daemon</a>
                  <a class="nav-link" href="/support/admin.html" target="_blank">Admin</a>
                  <a class="nav-link" href="https://github.com/Guyverix/Vigilare-NMS-GUI" target="_blank">Gitlab UI</a>
                  <a class="nav-link" href="https://github.com/Guyverix/Vigilare-NMS-API" target="_blank">Gitlab API</a>
                  <a class="nav-link" href="http://webserver01:83/" target="_blank">PHP MyAdmin</a>
                </nav>
              </div>
            </div>
          </div>
        </nav>
      </div>

      <!-- Add Main panel content here -->
      <!-- Figure out what the web browsers timeszone is set to -->
      <div id="layoutSidenav_content">
        <main>
          <div class="container-fluid">
            <div class="card mb-1 bg-light">


<?php
/* Calling this page:
replaySpecificEvent.php?evid=" . $event['evid'] . "&table=history"

API call example: http://larvel01:8002/events/findId/61f65391ac757
*/

// Setup requirements
require_once __DIR__ . '/../../config/api.php';
$evid=$_GET['evid'];
$table=$_GET['table'];

// Grab a single event
$ch=curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl . ':' . $apiPort . "/" . $table . "/findId/" . $evid);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$output = curl_exec($ch) ;
$output = json_decode($output, true);
curl_close($ch);

//print_r($output);
//echo "<br><br>\n";
$existingEvent=array();

// Make pretty variables since this is going to get somewhat complex
foreach ($output['data'][0] as $key => $value) {
  if ( $key == "eventRaw" ) { $existingEvent["$key"] = json_decode($value, true); }
  elseif ( $key == "eventDetails" ) { $existingEvent["$key"] = json_decode($value, true); }
  else {
    $existingEvent["$key"] = $value ;
  }
}

// print_r($existingEvent);  //DEBUGGING

echo '<div class="row">' . "\n";
  echo '<div class="table-responsive col-lg-6">' . "\n";

    echo "<table class='table-bordered'><th><center>Existing Event from database (template for testing)</center></th>\n";
    echo "<tr><td>\n";
    echo "<ul class='list-group'>";
    echo '<li class="list-group-item">Summary: ' .           $output['data'][0]['eventSummary'] . "</li>\n";
    echo '<li class="list-group-item">Severity: ' .          $output['data'][0]['eventSeverity'] . "</li>\n";
    echo '<li class="list-group-item">Alarm Name: ' .        $output['data'][0]['eventName'] . "</li>\n";
    echo '<li class="list-group-item">Age Out (seconds): ' . $output['data'][0]['eventAgeOut'] . "</li>\n";
    echo '<li class="list-group-item">Event ID: ' .          $output['data'][0]['evid'] . "</li>\n";
    echo '<li class="list-group-item">Database Hostname: ' . $output['data'][0]['device'] . "</li>\n";
    echo '<li class="list-group-item">Database Counter: ' .  $output['data'][0]['eventCounter'] . "</li>\n";
    echo '<li class="list-group-item">Database Details: ' .  $output['data'][0]['eventDetails'] . "</li>\n";
    echo '<li class="list-group-item">Database Host IP: ' .  $output['data'][0]['eventAddress'] . "</li>\n";
    echo '<li class="list-group-item">Database Proxy IP: ' . $output['data'][0]['eventProxyIp'] . "</li>\n";
    echo '<li class="list-group-item">Received time: ' .     $output['data'][0]['stateChange'] . "</li>\n";
    echo '<li class="list-group-item">Receiver type: ' .     $output['data'][0]['eventReceiver'] . "</li>\n";
    echo '<li class="list-group-item">Do we monitor: ' .     $output['data'][0]['eventMonitor'] . "</li>\n";
    echo '<li class="list-group-item">Raw Event ($details) as array:<pre>' . "\n";
    if ( ! is_array($existingEvent['eventRaw'])) {
      json_decode($existingEvent['eventRaw'], true);
    }
    foreach ( $existingEvent['eventRaw'] as $k => $v) {
      echo "Index Key: " . $k . " contains value: " . $v . "\n";
    }
    echo  "</pre>\n</li>\n</ul>\n";
    echo "</td></tr>\n</table>\n";
  echo "</div>\n";


// Now that we have a pretty host, find the OID mapping that it uses
foreach ($existingEvent['eventRaw'] as $oidValue) {
  switch ($oidValue) {
    case strpos($oidValue, "1.3.6.1.6.3.1.1.4.1.0" ) !== false:
      $trapOid=preg_replace('/.*1.3.6.1.6.3.1.1.4.1.0 /','', $oidValue);
      break;
    case strpos($oidValue, "iso.3.6.1.6.3.1.1.4.1.0" ) !== false:
      $trapOid=preg_replace('/.*iso.3.6.1.6.3.1.1.4.1.0 /','', $oidValue);
      break;
  }
}
if ( !empty($existingEvent['eventRaw']['eventName'])&& empty($trapOid)) { $trapOid= $existingEvent['eventRaw']['eventName']; }  // Adds support for outside mappings
// Stop the train if we do NOT have a parsible OID value
if ( empty($trapOid)) {
  echo '<div class="table-responsive col-lg-6">' . "\n";
    echo "<center><table class='table-bordered bg-warning'>\n<th>\n<center>Fatal Issue</center>\n</th>\n<tr>\n<td>\n";
      echo "The event in question was not generated by an SNMP trap<br>";
      echo "or the SNMP trap did NOT follow RFC spec for 1.3.6.1.6.3.1.1.4.1.0<br><br>";
      echo "Confirm that this was not generated via pollers.  They can leverage the output directly<br>to make events.<br>";
    echo "</td>\n</tr>\n</table>\n</center>\n";
  echo "</div>\n";
//  echo "</div>\n";
}
else {

  // Grab a single mapping (hopefully)
  $ch=curl_init();
  curl_setopt($ch, CURLOPT_URL, $apiUrl . ':' . $apiPort . "/mapping/find");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POSTFIELDS, "oid=$trapOid");
//  curl_setopt($ch, CURLOPT_POSTFIELDS, "oid=*");
  $mapping = curl_exec($ch) ;
  $mapping = json_decode($mapping, true);
  curl_close($ch);

  // If we cannot find a match, use *
  if ( empty($mapping['data'][0]) ){
    $ch=curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl . ':' . $apiPort . "/mapping/find");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, "oid=*");
    $mapping = curl_exec($ch) ;
    $mapping = json_decode($mapping, true);
    curl_close($ch);
  }

//echo "HERE I AM";
//  echo "Event was created by this Mapping Data<pre>";
//  print_r($mapping['data'][0]);
//  var_dump($mapping);
//echo "END";

//  echo "</pre>";
//  echo "<br><br>\n";
//  If we need to use json instead of posting the array:
 // echo $encodeExistingEvent;


  $encodeExistingEvent = json_encode($existingEvent);
  $encodeMapping = json_encode($mapping['data'][0]);

//  echo '<div class="row">';
  echo '<div class="table-responsive col-lg-6">' . "\n";

  echo '<table class="table table-responsive table-bordered bg-dark" >' . "\n";
  echo "<th><center>Parsed Values and Changes</center></th>\n";
  echo "<tr><td>\n";

  echo "Parsed OID Mapped from 1.3.6.1.6.3.1.1.4.1.0 is: " . $trapOid . "<br>\n";
  echo "Retrieved value in Mapping is: " . $mapping['data'][0]['oid']. "<br><br>\n";
  echo 'Variables that can be manipulated in pre-processing and post-processing are:<br> $evid, $known_hostname, $receive_time, $event_age_out, $counter, $details,<br> $receiver, $event_severity, $event_ip, $event_source, $event_name, $event_type, $monitor, $event_summary' . "<br>\n";


  echo "</td></tr><tr><td>";

  echo '<form id="testMapping" action="/event/replayTestMapping.php" method="POST">';

  echo "<input type='hidden' name='existingEvent' value='" . $encodeExistingEvent . "'><br>";
  echo "<input type='hidden' name='trapMapping' value='" . $encodeMapping . "'><br>";

  echo '<label for="displayName" class="form-label">Use what we suggested: Trap OID number: (never use *)</label><br>';
  if ( $mapping['data'][0]['oid'] == '*' ) {
    echo '<input type="displayName" class="form-control" id="oid" value="' . $trapOid . '" name="oid"><br>';
  }
  else {
    echo '<input type="displayName" class="form-control" id="oid" value="' . $mapping['data'][0]['oid'] . '" name="oid"><br>';
  }

  echo '<label for="displayName" class="form-label">Default Alarm Name: (never use unmapped) Suggested is camelCaseformat </label><br>';
  echo '<input type="displayName" class="form-control" id="displayName" value="' . $mapping['data'][0]['display_name'] . '" name="display_name"><br>';

  echo '<label for="severity" class="form-label">Default Event Severity:</label><br>';
  echo '<input type="displayName" class="form-control" id="severity" value="'. $mapping['data'][0]['severity'] . '" name="severity"><br>';

  echo '<label for="age_out" class="form-label">Default Age Out Timer:</label><br>';
  echo '<input type="displayName" class="form-control" id="age_out" value="' . $mapping['data'][0]['age_out'] . '" name="age_out"><br>';

  echo '<label for="pre_processing">Event Pre-processing:</label><br>';
  echo '<textarea class="form-control" rows="5" cols="80" id="pre_processing" name="pre_processing">' . $mapping['data'][0]['pre_processing'] . '</textarea><br>';

  echo '<label for="post_processing">Event Post-processing: (Not usually needed)</label><br>';
  echo '<textarea class="form-control" rows="3" id="post_processing" name="post_processing">' . $mapping['data'][0]['post_processing'] . '</textarea><br>';

  echo '<button type="submit" class="btn btn-primary"  value="post request">Test Mapping</button><br>';
  echo '</form>';
  echo "</td></tr></table>";
  echo '</div>';
  echo '</div>';
}

?>




                 </div>
               </div>
             </div>
           </main>
         </div>

  <footer class="py-4 bg-light mt-auto">
    <div class="container-flex px-4">
      <div class="d-flex align-items-center justify-content-between small">
        <?php echo ' <div class="text-muted">&copy; Vigilare NMS Monitoring ' . date('Y') . ' </div>'; ?>
        <div>
          <a href="#">Privacy Policy</a>
          &middot;
          <a href="#">Terms &amp; Conditions</a>
        </div>
      </div>
    </div>
  </footer>

  <script src="/js/font-awesome/all.min.js" crossorigin="anonymous"></script>
  <script src="/js/bootstrap-5/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>

  <script src="/js/simple-datatables/simple-datatables.min.js"></script>
  <script src="/js/simple-datatables/script.js"></script>
  <script src="/js/light-switch-bootstrap-main/switch.js"></script>
  <script src="/js/sb-demo/js/scripts.js"></script>
</body>
</html>



