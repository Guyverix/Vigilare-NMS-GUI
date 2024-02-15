<br><br><br>
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


          <div class="container-fluid">
            <div class="card mb-1 bg-light">



<?php
include_once __DIR__ . '/../../config/api.php';

/* Get all our POST values now */
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

// If we have any errs from php -l
if ( $exit !== 0 || $exit2 !== 0) {
//  echo '<div class="table-responsive col-md-6">';
  echo "<br><br><br>";
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
  echo "<br><br><br>";
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
//echo '<center><button type="submit" value="post request">Change Mapping Now</button> ';
echo '<center><button type="submit" class="btn btn-danger btn-lg" value="post request">Change Mapping Now</button> ';
echo '</form>';

echo '<button type="button" class="btn btn-warning btn-lg"> <a href="javascript:history.back()">Go Back and change more</a> </button></center>';
//echo '<button><a href="javascript:history.back()">Go Back and change more</a> </button></center>';
}
else {
}

?>

               </div>
             </div>
