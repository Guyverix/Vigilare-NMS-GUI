<?php
  /*
     Calling this page:
     replaySpecificEvent.php?evid=" . $event['evid'] . "&table=history"
     API call example: http://larvel01:8002/events/findId/61f65391ac757
  */

  // Setup requirements
  echo "<br><br><br>";
  require_once __DIR__ . '/../../config/api.php';
  include_once __DIR__ . "/functions/eventFunctions.php";
  include_once __DIR__ . "/../../functions/generalFunctions.php";

  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
  $post = array();  // We are using post, so give it an empty array to post with

  $evid=$_GET['evid'];
  $table=$_GET['table'];
  //debugger($_GET);

  // Grab a single event
  $rawGetEvent = callApiGet('/' . $table . '/findId/' . $evid, $headers);
  //debugger($rawGetEvent);

  $output = json_decode($rawGetEvent['response'], true);
  //debugger($output);
  //exit();

  // Make pretty variables since this is going to get somewhat complex
  $existingEvent=array();
  foreach ($output['data'][0] as $key => $value) {
    if ( $key == "eventRaw" ) { $existingEvent["$key"] = json_decode($value, true); }
    elseif ( $key == "eventDetails" ) { $existingEvent["$key"] = json_decode($value, true); }
    else {
      $existingEvent["$key"] = $value ;
    }
  }
  //debugger($existingEvent);

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
  echo '<li class="list-group-item">Hostname: '          . $output['data'][0]['device'] . "</li>\n";
  echo '<li class="list-group-item">Counter: '          .  $output['data'][0]['eventCounter'] . "</li>\n";
  echo '<li class="list-group-item">Details: '          .  $output['data'][0]['eventDetails'] . "</li>\n";
  echo '<li class="list-group-item">Host IP: '          .  $output['data'][0]['eventAddress'] . "</li>\n";
  echo '<li class="list-group-item">Proxy IP: '          . $output['data'][0]['eventProxyIp'] . "</li>\n";
  echo '<li class="list-group-item">Received time: ' .     $output['data'][0]['stateChange'] . "</li>\n";
  echo '<li class="list-group-item">Receiver type: ' .     $output['data'][0]['eventReceiver'] . "</li>\n";
  echo '<li class="list-group-item">Do we monitor: ' .     $output['data'][0]['eventMonitor'] . "</li>\n";
/*
    echo '<li class="list-group-item">Raw Event ($details) as array:<pre>' . "\n";
    if ( ! is_array($existingEvent['eventRaw'])) {
      json_decode($existingEvent['eventRaw'], true);
    }
    foreach ( $existingEvent['eventRaw'] as $k => $v) {
      echo "Index Key: " . $k . " contains value: " . $v . "\n";
    }
    echo  "</pre>\n</li>\n</ul>\n";
*/
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

// This is pulling from the raw data, not the database data.
if ( ! empty($existingEvent['eventRaw']['eventName'])&& empty($trapOid)) {
  $trapOid = $existingEvent['eventRaw']['eventName'];  // Adds support for outside mappings
}

// Stop the train if we do NOT have a parsible OID value or eventName
if ( empty($trapOid)) {
  echo '<div class="table-responsive col-lg-6">' . "\n";
    echo "<center><table class='table-bordered bg-warning'>\n<th>\n<center>Fatal Issue</center>\n</th>\n<tr>\n<td>\n";
      echo "The event in question was not generated by an SNMP trap or normal service check<br>";
      echo "or the SNMP trap did NOT follow RFC spec for 1.3.6.1.6.3.1.1.4.1.0<br><br>";
      echo "Confirm that this was not generated via pollers.  They can leverage the output directly<br>to make events.<br>";
    echo "</td>\n</tr>\n</table>\n</center>\n";
  echo "</div>\n";
//  echo "</div>\n";
}
else {

  // Grab a single mapping (hopefully)
  $post = ['oid' => $trapOid];
  $rawMappingFind = callApiPost('/mapping/find', $post, $headers);
  $mapping = json_decode($rawMappingFind['response'], true);
 
  // If we cannot find a match, use '*'
  if ( empty($mapping['data'][0])) {
    $post = ['oid' => '*'];
    $rawMappingFind = callApiPost('/mapping/find', $post, $headers);
    $mapping = json_decode($rawMappingFind['response'], true);
  }

  // debugger($mapping);
  // exit();
  $encodeExistingEvent = json_encode($existingEvent, 1);
  $encodeMapping = json_encode($mapping['data'][0],1 );

  echo '<div class="table-responsive col-lg-6">' . "\n";
  echo '<table class="table table-responsive table-bordered bg-dark" >' . "\n";
  echo "<th><center>Parsed Values and Changes</center></th>\n";
  echo "<tr><td>\n";
  echo "Parsed OID Mapped from 1.3.6.1.6.3.1.1.4.1.0 or event name defined is: " . $trapOid . "<br>\n";
  echo "Retrieved value in Mapping is: " . $mapping['data'][0]['oid']. "<br><br>\n";
  echo 'Variables that can be manipulated in pre-processing and post-processing are:<br> $evid, $known_hostname, $receive_time, $event_age_out, $counter, $details,<br> $receiver, $event_severity, $event_ip, $event_source, $event_name, $event_type, $monitor, $event_summary' . "<br>\n";


  echo "</td></tr><tr><td>";

  echo '<form id="testMapping" action="/event/index.php?&page=replayTestMapping.php" method="POST">';

  echo "<input type='hidden' name='existingEvent' value='" . $encodeExistingEvent . "'><br>";
  echo "<input type='hidden' name='trapMapping' value='" . $encodeMapping . "'><br>";

  // Show what a default event name is, even if it is a raw oid value
  echo '<label for="displayName" class="form-label">Use what we suggested: Trap OID number or monitor name: (never use "*")</label><br>';
  if ( $mapping['data'][0]['oid'] == '*' ) {
    echo '<input type="displayName" class="form-control" id="oid" value="' . $trapOid . '" name="oid"><br>';
  }
  else {
    echo '<input type="displayName" class="form-control" id="oid" value="' . $mapping['data'][0]['oid'] . '" name="oid"><br>';
  }
  // this will hard set to this value
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

  echo '<button type="submit" class="btn btn-warning btn-lg"  value="post request">Test Mapping</button><br>';
  echo '</form>';
  echo "</td></tr></table>";
  echo '</div>';
  echo '</div>';
}

?>




                 </div>
               </div>
             </div>
