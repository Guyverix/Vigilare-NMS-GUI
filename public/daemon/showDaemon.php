<?php
  /*
    We are going to show our running daemons...
  */

  echo "<br><br><br>";
  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";

  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];

  $post = array();  // We can use post, so give it an empty array to post with
  $rawDaemonList = callApiGet("/monitoringPoller/housekeeping", $headers);
  if (! is_array($rawDaemonList)) {
    $rawDaemonList = json_decode($rawDaemonList, true);
  }
  $daemonList = json_decode($rawDaemonList['response'], true);
  $daemons = $daemonList['data'];
  $quitEarly = 0;

  switch ($daemonList['statusCode']) {
   case 403:
     load4XX();
     $quitEarly = 1;
   case 200:
     break;
   default:
     decideResponse($daemonList['statusCode']);
     $quitEarly = 1;
  }

  // debugger($daemons);
  // Code does not exist yet for branch daemon pollers
  $fakeIp='192.168.15.65';
  if ($quitEarly == 0) {
    echo '<center><h1>Summary status of known daemons</h1></center>';
    echo '<div class="container-lg">';
    echo '<table id="dt-status" class="table table-striped table-hover" data-loading-template="loadingTemplate" style="white-space: nowrap;">';
    echo '<thead>';
    echo '<th>Daemon IP</th>';
    echo '<th>Daemon</th>';
    echo '<th>Iteraction Cycle</th>';
    echo '<th>Last Heartbeat</th>';
    echo '<th>PID</th>';
    echo '</thead>';
    echo '<tbody>';
    foreach ($daemons as $daemon) {
      if ($daemon['device'] === "snmptrapd" || $daemon['device'] === 'mysql') {
        $pid = 'OS controlled';
      }
      else {
        $pid = $daemon['pid'];
      }
      $iteration = preg_replace('/iteration_/','',$daemon['component']);
      echo '<tr><td>' . $fakeIp . '</td><td>' . $daemon['device'] . '</td><td>' . $iteration . '</td><td>' . $daemon['lastTime'] . '</td><td>' . $pid . '</td><tr>';
    }
    echo '</tbody>';
    echo '</table>';
    echo '</div>';

  }
  else {
    // Something went very wrong with the API call, but keep the layout clean...
    loadUnknown("API calls failed in an unexpected way.  Please reload");
  }
?>
