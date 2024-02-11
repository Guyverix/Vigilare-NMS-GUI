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

  <!-- Simplest way to POST the data I could find -->
  <script>
  "use strict";
  function submitForm(oFormElement) {
    var xhr = new XMLHttpRequest();
    xhr.onload = function() {
    alert (xhr.responseText);
    history.go(-2);
  } // success case
  xhr.onerror = function() {
    alert ("Failed to create mapping.");
  } // failure case
  xhr.open (oFormElement.method, oFormElement.action, true);
  xhr.send (new FormData (oFormElement));
  return false;
  }
  </script>


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

      <!-- DataTable ONLY -->
      <div id="layoutSidenav_content">
        <main>
          <div class="container-fluid">
            <div class="card mb-1 bg-light">



<?php
/* Get all our POST values now */
include_once __DIR__ . '/../../config/api.php';

$currentExistingEvent=json_decode($_POST['existingEvent'], true);
$currentTrapMapping=json_decode($_POST['trapMapping'], true);  // might not even be needed at all TBH, but keep it JIC

/* Set variable names identical to what the PDO trap reciever uses */
$event_summary  = $currentExistingEvent['eventSummary'];
$details        = $currentExistingEvent['eventRaw'];
$event_severity = $currentExistingEvent['eventSeverity'];
$event_name     = $currentExistingEvent['eventName'];
$event_age_out  = $currentExistingEvent['eventAgeOut'];
$evid           = $currentExistingEvent['evid'];
$known_hostname = $currentExistingEvent['device'];
$counter        = $currentExistingEvent['eventCounter'];
$event_details  = $currentExistingEvent['eventDetails'];
$event_ip       = $currentExistingEvent['eventAddress'];
$event_source   = $currentExistingEvent['eventProxyIp'];
$receive_time   = $currentExistingEvent['stateChange'];
$receiver       = $currentExistingEvent['eventReceiver'];
$monitor        = $currentExistingEvent['eventMonitor'];

/* Make sure that changes to the default mapping are reflected here as well */
$newMappedOid=$_POST['oid'];
$newDisplayName=$_POST['display_name'];
$newSeverity=$_POST['severity'];
$newAgeOut=$_POST['age_out'];
$newPreProcessing=$_POST['pre_processing'];
$newPostProcessing=$_POST['post_processing'];

/* List group of EXISTING MAPPINGS */
echo '<div class="row">';
//echo '<div class="table-responsive col-md-6">';


/* Show results of validation checking */
$result = exec(sprintf('echo %s | php -l', escapeshellarg('<?php ' . $newPreProcessing)), $output, $exit);
$result2 = exec(sprintf('echo %s | php -l', escapeshellarg('<?php ' . $newPostProcessing)), $output2, $exit2);
//echo '</div>';


if ( $exit !== 0 || $exit2 !== 0) {
//  echo '<div class="table-responsive col-md-6">';
  echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">';
  echo "<center><table class='table-borderless'><th><center>You failed!</center></th><tr><td>";
  echo "Please fix syntax errors in pre-processing or post-processing on previous page<br>";
  echo "PHP kinda sucks in giving falure details.  I have no more details to give.<br>";
  echo '<center><button><a href="javascript:history.back()">Go Back</a> </button>';
  echo "</td></td></tr></table>";
  echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
  echo "</center></div>";
}
else {
  echo '<div class="alert alert-success alert-dismissible fade show" role="alert">';
  echo "<center><table class='table-borderless'><th><center>Basic PHP syntax check Success!</center></th><tr><td>";
  echo "A basic PHP syntax validation has been completed for pre-processing and post-processing.<br>";
  echo "This does not mean the code is good, just that it does not break things.<br>";
  echo "</td></td></tr></table>";
  echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>';
  echo "</center></div>";
}
echo '</div>';

/* this is going to be the SECOND row */
echo '<div class="row">';
echo '<div class="table-responsive col-lg-5">';

echo "<table class='table-bordered'><th><center>Existing Event from database (template for testing)</center></th>";
echo "<tr><td>";
echo "<ul class='list-group'>";
echo '<li class="list-group-item">Summary: ' .$event_summary . "</li>\n";
echo '<li class="list-group-item">Severity: ' . $event_severity . "</li>\n";
echo '<li class="list-group-item">Alarm Name: ' . $event_name . "</li>\n";
echo '<li class="list-group-item">Age Out (seconds): ' . $event_age_out . "</li>\n";
echo '<li class="list-group-item">Event ID: ' . $evid . "</li>\n";
echo '<li class="list-group-item">Database Hostname: ' . $known_hostname . "</li>\n";
echo '<li class="list-group-item">Database Counter: ' . $counter . "</li>\n";
echo '<li class="list-group-item">Database Details: ' . $event_details . "</li>\n";
echo '<li class="list-group-item">Database Host IP: ' . $event_ip . "</li>\n";
echo '<li class="list-group-item">Database Proxy IP: ' . $event_source . "</li>\n";
echo '<li class="list-group-item">Received time: ' . $receive_time . "</li>\n";
echo '<li class="list-group-item">Receiver type: ' . $receiver . "</li>\n";
echo '<li class="list-group-item">Do we monitor: ' . $monitor . "</li>\n";
echo '<li class="list-group-item">Raw Event ($details) as array:<pre>';
foreach ( $details as $k => $v) {
  echo "Index Key: " . $k . " contains value: " . $v . "\n";
}
echo  "</pre></li>\n</ul>\n";
echo "</td></tr></table>";
echo '</div>';


/* Show the results of our changes */
if ( $exit == 0 && $exit2 == 0 ) {

if ( $newDisplayTime !== $currentTrapMapping['display_name'] ) {
  $event_name = $newDisplayName;
}
if ($newSeverity !== $currentTrapMapping['severity']) {
  $event_severity = $newSeverity;
}
if ( $newAgeOut !== $currentTrapMapping['age_out']) {
  $event_age_out = $newAgeOut;
}

// Run your changes here for testing
eval($newPreProcessing);

echo '<div class="table-responsive col-lg-2">';
echo "<center><br><br><br><br><br><br><table class='table-bordered bg-light'>";
echo "<th><center>Proposed changes to Trap Mapping</center></th>";
echo "<tr><td>";
echo "<ul class='list-group'>";
echo '<li class="list-group-item">Default OID: (match value) ' . $newMappedOid . "</li>\n";
if ( $newDisplayName == "unmapped" ) {
  echo '<li class="list-group-item bg-danger">Default Alarm Name: ' . $newDisplayName . "</li>\n";
}
else {
  echo '<li class="list-group-item">Default Alarm Name: ' . $newDisplayName . "</li>\n";
}
echo '<li class="list-group-item">Default Severity: ' . $newSeverity . "</li>\n";
echo '<li class="list-group-item">Default Age Out: (seconds) ' . $newAgeOut . "</li>\n";
echo '<li class="list-group-item">Default Pre-Pocessing:<pre>';
echo  $newPreProcessing . "</pre></li>\n";
echo '<li class="list-group-item">Default Post-Pocessing:<pre>';
echo  $newPostProcessing . "</pre></li>\n";
echo "</ul></td></tr></table>";
echo '<br>Do not forget that pre-processing is what is used to override the<br>';
echo 'default mapping values.  Keep this in mind when looking at the<br>';
echo 'test output<br><br>';
echo 'Incomming traps default to a generic string in the trap receiver.<br>';
echo 'It will default to "SNMP trap received from W.X.Y.Z" if not defined. <br>';
echo '</div>';

echo '<div class="table-responsive col-lg-5">';
echo "<table class='table-bordered'>";
echo "<th><center>Testing results from Pre-Processing</center></th>";
echo "<tr><td>";
echo "<ul class='list-group'>";
echo '<li class="list-group-item">Default OID: (match value) ' . $newMappedOid . "</li>\n";
echo '<li class="list-group-item">Default Alarm name: ' . $newDisplayName . "</li>\n";
echo '<li class="list-group-item">Default Age Out: ' . $newAgeOut . "</li>\n";
echo '<li class="list-group-item">Default Severity: ' . $newSeverity . "</li>\n";
echo '<hr><center><b>Manipulated values that would create an event</b></center><br>';
echo '<li class="list-group-item">Summary: ' .$event_summary . "</li>\n";
echo '<li class="list-group-item">Severity: ' . $event_severity . "</li>\n";
echo '<li class="list-group-item">Alarm name: ' . $event_name . "</li>\n";
echo '<li class="list-group-item">Age Out: ' . $event_age_out . "</li>\n";
echo '<li class="list-group-item">Event ID: ' . $evid . "</li>\n";
echo '<li class="list-group-item">Event Hostname: ' . $known_hostname . "</li>\n";
echo '<li class="list-group-item">Event Counter: ' . $counter . "</li>\n";
echo '<li class="list-group-item">Event details: ' . $event_details . "</li>\n";
echo '<li class="list-group-item">Database Host IP: ' . $event_ip . "</li>\n";
echo '<li class="list-group-item">Database Proxy IP: ' . $event_source . "</li>\n";
echo '<li class="list-group-item">Received Time: ' . $receive_time . "</li>\n";
echo '<li class="list-group-item">Receiver Type: ' . $receiver . "</li>\n";
echo '<li class="list-group-item">Do We monitor: ' . $monitor . "</li>\n";
echo '<li class="list-group-item">Raw Event ($details) as array:<pre>';
foreach ( $details as $k => $v) {
  echo "Index Key: " . $k . " contains value: " . $v . "\n";
}

echo  "</pre></li>\n";
echo "</ul></td></tr></table>";
echo '</div>';
echo '</div>';


echo '<form id="changeMapping" action="' . $apiUrl . ':' . $apiPort . '/globalMapping/trap/update"  method="POST" onsubmit="return submitForm(this);">';
echo '<input type="hidden" name="oid" value="' . $newMappedOid . '">';
echo '<input type="hidden" name="display_name" value="' . $newDisplayName . '">';
echo '<input type="hidden" name="severity" value="' . $newSeverity . '">';
$newPreProcessing=preg_replace( '/";/', '&quot ;', $newPreProcessing);
$newPreProcessing=preg_replace( '/"/', '&quot', $newPreProcessing);
echo '<input type="hidden" name="pre_processing" value="' . $newPreProcessing . '">';
echo '<input type="hidden" name="type" value="' . $receiver . '">';
echo '<input type="hidden" name="parent_of" value="">';
echo '<input type="hidden" name="child_of" value="">';
echo '<input type="hidden" name="age_out" value="' . $newAgeOut . '">';
$newPostProcessing=preg_replace( '/";/', '&quot ;', $newPostProcessing);
$newPostProcessing=preg_replace( '/"/', '&quot', $newPostProcessing);
echo '<input type="hidden" name="post_processing" value="' . $newPostProcessing . '">';
echo '<center><button type="submit" value="post request">Change Mapping Now</button> ';
echo '</form>';
echo '<button><a href="javascript:history.back()">Go Back and change more</a> </button></center>';
}
else {
}

?>

               </div>
             </div>
           </main>
         </div>

  <footer class="py-4 bg-light mt-auto">
    <div class="container-flex px-4">
      <div class="d-flex align-items-center justify-content-between small">
        <?php echo ' <div class="text-muted">&copy; Vigilare NMS Monitoring ' . date(Y) . ' </div>'; ?>
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





