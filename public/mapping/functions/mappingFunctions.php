<?php
/*
  Built-in functions for mapping UI to use in different pages
  This should cover monitoring, hostgroups, and maintenance evnets

  there will likely be some duplication against maintenance here
  and in the reporting section.
*/


// Safety net if we somehow do not have the varaibles to deal with the API
if (! isset($apiHostname)) {
  include_once __DIR__ . '/../../../config/api.php';
}

// Use curl from one spot when we can
// https://stackoverflow.com/questions/8115683/php-curl-custom-headers
function curlCall($url, $type = null, $options) {
  if ( is_null($type)) {
    $type="get";
  }
  $ch2 = curl_init();
  if (is_set($options['token'])) {
    $requestHeaders = [ 'Authorization: Bearer ' . $options['token'] ];
    curl_setopt($ch2, CURLOPT_HTTPHEADER, $requestHeaders);
  }
  curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch2, CURLOPT_URL,$url);

  if ($type == "post" ) {
    curl_setopt($ch2, CURLOPT_POST, 1);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, $options);
  }
  $rawResult = curl_exec($ch2);
  curl_close($ch2);
  if ( ! is_array($rawResult)) {
    $rawResult = json_decode($rawResult, true);
  }
  return $rawResult;  // One way or another return an array :)
}
/*
// Sanity test
$url     = $apiHttp . $apiHostname . ':' . $apiPort . '/device/properties';
$type    = 'post';
$options = ['id' => '74'];
var_dump(curlCall($url,$type,$options));
*/


// https://stackoverflow.com/questions/9166914/using-default-arguments-in-a-function
function createDevice($hostname, $address, $productionState = null) {
  if ( null === $productionState ) {
    $productionState = 1;
  }
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/device/create';
  $type = 'post';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $options['hostname'] = $hostname;
  $options['address'] =  $address;
  $options['productionState'] = $productionState;
  $result = curlCall($url,$type,$options);
  return $result;
}

function updateDevice($id, $hostname, $address, $productionState = null) {
  if ( null === $productionState ) {
    $productionState = 1;
  }
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/device/update';
  $type = 'post';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $options['id'] = $id;
  $options['hostname'] = $hostname;
  $options['address'] =  $address;
  $options['productionState'] = $productionState;
  $result = curlCall($url,$type,$options);
  return $result;
}

function deleteDevice($id) {
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/device/delete';
  $type = 'post';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $options['id'] = $id;
  $result = curlCall($url,$type,$options);
  return $result;
}

function createHostgroup($hostgroup, $hostname) {
  if ( null === $hostname ) {
    $hostname = [];
  }
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/globalMapping/hostgroup/create';
  $type = 'post';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $options['hostgroup'] =  $hostgroup;
  $options['hostname'] = $hostname;
  $result = curlCall($url,$type,$options);
  return $result;
}

// This one is a little odd.  hostname is an array in the db.
// make sure what we have is legit before attempting to use it
function updateHostgroup($hostgroup, $hostname, $action) {
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/globalMapping/hostgroup/update';
  $type = 'post';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $options['hostgroup'] =  $hostgroup;
  $options['hostname'] = $hostname;
  $result = curlCall($url,$type,$options);
  return $result;
}

function deleteHostgroup($hostgroup) {
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/globalMapping/hostgroup/delete';
  $type = 'post';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $options['hostgroup'] =  $hostgroup;
  $result = curlCall($url,$type,$options);
  return $result;
}

// returns array of id, monitorNames
function findMonitorNames() {
  $options = array();
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/monitors/list';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $type = 'post';
  $result = curlCall($url,$type,$options);
  return $result;
}

// Initial create of new monitor.  Add hosts and groups seporately from definition
function createMonitor($checkName, $checkAction, $type, $iteration, $storage) {
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/monitors/createMonitors';
  $type = 'post';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $options['checkName'] = $checkName;
  $options['checkAction'] = $checkAction;
  $options['type'] = $type;
  $options['iteration'] = $iteration;
  $options['storage'] = $storage;
  $options['hostId'] = '';
  $options['hostGroup'] = '';
  $result = curlCall($url,$type,$options);
  return $result;
}

// Does NOT add hosts to the changed monitor, simply edits the check
function udpateMonitor($id, $checkName, $checkAction, $type, $iteration, $storage) {
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/monitors/updateMonitor';
  $type = 'post';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $options['id'] = $id;
  $options['checkName'] = $checkName;
  $options['checkAction'] = $checkAction;
  $options['type'] = $type;
  $options['iteration'] = $iteration;
  $options['storage'] = $storage;
  $result = curlCall($url,$type,$options);
  return $result;
}

// nuke monitor $id
function deleteMonitor($id) {
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/monitors/deleteMonitor';
  $type = 'post';
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  $options['id'] = $id;
  $result = curlCall($url,$type,$options);
  return $result;
}

// add single hostId to monitor $id
function monitorAddHost($id, $hostId) {
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/monitors/monitorAddHost';
  $type = 'post';
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  $options['id'] = $id;
  $options['hostId'] = $hostId;
  $result = curlCall($url,$type,$options);
  return $result;
}

// add single hostgroup to monitor $id
function monitorAddHostgroup($id, $hostId) {
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/monitors/monitorAddHostgroup';
  $type = 'post';
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  $options['id'] = $id;
  $options['hostId'] = $hostId;
  $result = curlCall($url,$type,$options);
  return $result;
}

// remove single hostId from monitor $id
function monitorDeleteHost($id, $hostId) {
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/monitors/monitorDeleteHost';
  $type = 'post';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $options['id'] = $id;
  $options['hostId'] = $hostId;
  $result = curlCall($url,$type,$options);
  return $result;
}

// remove single hostgroup from monitor $id
function monitorDeleteHostgroup($id, $hostId) {
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/monitors/monitorDeleteHostgroup';
  $type = 'post';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $options['id'] = $id;
  $options['hostId'] = $hostId;
  $result = curlCall($url,$type,$options);
  return $result;
}

function findMonitors($type) {
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/monitors/findMonitors';
  $type = 'post';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $options['type'] = $type;
  $result = curlCall($url,$type,$options);
  return $result;
}

function findMonitorsByCheckName($checkName) {
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/monitors/findMonitorsByCheckName';
  $type = 'post';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $options['checkName'] = $checkName;
  $result = curlCall($url,$type,$options);
  return $result;
}

function findMonitorsDisable() {
  $options = array();
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/monitors/findMonitorsDisable';
  $type = 'post';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $result = curlCall($url,$type,$options);
  return $result;
}

function findMonitorsAll() {
  $options = array();
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/monitors/findMonitorsAll';
  $type = 'post';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $result = curlCall($url,$type,$options);
  return $result;
}

function createMaintenance($start, $end, $summary, $detail, $apps = null ) {
  if ( null === $apps ) {
    $apps = '';
  }
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/maintenance/createEvent';
  $type = 'post';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $options['start'] = $start;
  $options['end'] = $end;
  $options['summary'] = $summary;
  $options['detail'] = $detail;
  $options['apps'] = $apps;
  $result = curlCall($url,$type,$options);
  return $result;
}

function updateMaintenance($id, $start, $end, $summary, $detail, $apps = null ) {
  if ( null === $apps ) {
    $apps = '';
  }
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/maintenance/updateEvent';
  $type = 'post';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $options['id'] = $id;
  $options['start'] = $start;
  $options['end'] = $end;
  $options['summary'] = $summary;
  $options['detail'] = $detail;
  $options['apps'] = $apps;
  $result = curlCall($url,$type,$options);
  return $result;
}

function deleteMaintenance($id ) {
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/maintenance/deleteEvent';
  $type = 'post';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $options['id'] = $id;
  $result = curlCall($url,$type,$options);
  return $result;
}

function cancelMaintenance($id, $reason ) {
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/maintenance/cancelEvent';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $type = 'post';
  $options['id'] = $id;
  $options['reason'] = $reason;
  $result = curlCall($url,$type,$options);
  return $result;
}

function completeMaintenance($id, $result) {
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/maintenance/completeEvent';
  $type = 'post';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $options['id'] = $id;
  $options['result'] = $result;
  $result = curlCall($url,$type,$options);
  return $result;
}

function findOpenMaintenance() {
  $options = array();
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/maintenance/openMaintenance';
  $type = 'post';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $result = curlCall($url,$type,$options);
  return $result;
}

function findCompleteMaintenance() {
  $options = array();
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/maintenance/completeMaintenance';
  $type = 'post';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $result = curlCall($url,$type,$options);
  return $result;
}

function findAllMaintenance() {
  $options = array();
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/maintenance/allMaintenance';
  $type = 'post';
  if ( isset($_COOKIE['token'])) { $options['token'] = $_COOKIE['token']; }
  if ( isset($_COOKIE['userId'])) { $options['userId'] = $_COOKIE['userId']; }
  $result = curlCall($url,$type,$options);
  return $result;
}

?>

