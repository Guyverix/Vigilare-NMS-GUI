<?php
  /*
    This is going to render RRD files that
    match a given template name.
    This will require some oddball filtering I expect.
  */

  echo '<br><br><br>'; // only needed if we have a horozontal bar

  // Only needed for debugging and bypassing security, etc
  require_once(__DIR__ . '/../../../functions/generalFunctions.php');
  // checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../../config/api.php";

  // Hosts and Devices have A LOT of variables in play.  We need functions specific to this group
  require_once __DIR__ . "/../functions/hostFunctions.php";

  if ( isset($_POST['hostname'])) {
    $hostname = $_POST['hostname'];
  }
  if ( isset($_POST['id'])) {
    $id = $_POST['id'];
  }
  if ( isset($_POST['templateName'])) {
    $templateName = $_POST['templateName'];
  }
  if (isset($_POST['files'])) {
    $files = $_POST['files'];
    $fileString = json_decode($files, true);
    $fileString = rtrim($fileString, ',');  // Remove any dangling commas
    $fileArray = explode(',', $fileString);
  }
  if (isset($_POST['startRange']) && isset($_POST['startNumber'])) {
    $startNumber = $_POST['startNumber'];
    $startRange = $_POST['startRange'];
    $startTime = '-' . $_POST['startNumber'] . $_POST['startRange'];
  }
  else {
    $startNumber = '1';
    $startRange  = 'd';
    $startTime = '-1d';
  }

  if (isset($_POST['endRange']) && isset($_POST['endNumber']) && $_POST['endNumber'] !== 'now') {
    $endNumber = $_POST['endNumber'];
    $endRange = $_POST['endRange'];
    $endTime = '-' . $_POST['endTime'] . $_POST['endRange'];
  }
  else {
    $endRange = 'h';
    $endNumber = 'now';
    $endTime = 'now';
  }

  // debugger($_POST);
  // debugger($fileArray);
  echo '<div class="container">';
  echo '<div class=" text-center mt-5 ">';
  echo '<h1>Available RRD Graphs for <a href="/host/index.php?&page=deviceDetails.php&id=' . $id . '">' . $hostname . '</a></h1><br>';
  echo '</div>';
  echo '<form id="changeTimes" method="POST" action="">';
  echo '<table class="table bg-dark table-dark">';
  echo '<thead><th>From Range</th><th>Until Range</th></thead>';
  echo '<tbody><tr><td>';
  echo '<input type="text" name="startNumber" value="' . $startNumber . '" size="3"> ';
  echo '<select name="startRange" id="startRange">';
    if ( $startRange == 'd' ) { echo '<option value="d" selected>days</option>'; } else { echo '<option value="d" selected>days</option>'; }
    if ( $startRange == 'h' ) { echo '<option value="h" selected>hours</option>'; } else { echo '<option value="h">hours</option>'; }
    if ( $startRange == 'w' ) { echo '<option value="w" selected>weeks</option>'; } else { echo '<option value="w">weeks</option>'; }
    if ( $startRange == 'm' ) { echo '<option value="m" selected>months</option>'; } else { echo '<option value="m">months</option>'; } 
  echo '</select>';
  echo '</td><td>';
  echo '<input type="text" name="endNumber" value="' . $endNumber . '" size="3">';
  echo '<select name="endRange" id="endRange">';
    if ( $endRange == 'd' ) { echo '<option value="d" selected>days</option>'; } else { echo '<option value="d">days</option>'; }
    if ( $endRange == 'h' ) { echo '<option value="h" selected>hours</option>'; } else { echo '<option value="h">hours</option>'; }
    if ( $endRange == 'w' ) { echo '<option value="w" selected>weeks</option>'; } else { echo '<option value="w">weeks</option>'; }
    if ( $endRange == 'm' ) { echo '<option value="m" selected>months</option>'; } else { echo '<option value="m">months</option>'; }
  echo '</select>';
  echo '<input type="hidden" name="id" value="' . $id . '">';
  echo '<input type="hidden" name="hostname" value="' . $hostname . '">';
  echo '<input type="hidden" name="templateName" value="' . $_POST['templateName'] . '">';
  echo '<input type="hidden" name="files" value="' . htmlspecialchars($files) . '">';
  echo '</td></tr></tbody>';
  echo '</table>';
  echo '<center><button type="submit" form="changeTimes" class="btn btn-primary">Change Timeframe</button> <p></p> <input type="button" type="submit" class="btn btn-primary" value="back one page" onclick="history.back()"/></center>';
  echo '</form>';

  echo '<div class="container">';
  echo '<table class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;"><center><b>RRD Graphs for template ' . $templateName . '</b></center>';
  // Inside our table we are going to make one call per file to render the RRD back to us
  foreach($fileArray as $fileName) {
    $post =  [ 'hostname' => $hostname ];
    $fileName = trim($fileName, '"');
    $post += [ 'file' => $fileName ];
    $post += [ 'filter' => $templateName ];
    $post += [ 'start' => $startTime ];
    $post += [ 'IgnoreMatch' => ["/run"] ];
    $rawRenderRrd = callApiPost("/render/render", $post, $headers);
    $renderRrd = json_decode($rawRenderRrd['response'], true);
    // debugger($renderRrd);
    if ( $renderRrd['statusCode'] !== 200 ) {
      // echo '<tr><td>' . loadUnknown("API calls failed in an unexpected way.") . '</td></tr>';
      echo '<tr><td>' . loadUnknown($renderRrd['data']) . '</td></tr>';
    }
    else {
      if (array_key_exists(0, $renderRrd['data'])) {
        foreach ($renderRrd['data'] as $renderRrdArray) {
          echo '<tr><td><center><img class="NO-CACHE" src="' .  $apiHttp . $apiHostname . ':' . $apiPort . $renderRrdArray['image'] . '" width="900" height="200"></img></center></td></tr>';
        }
      }
      else {
        echo '<tr><td><center><img class="NO-CACHE" src="' .  $apiHttp . $apiHostname . ':' . $apiPort . $renderRrd['data']['image'] . '" width="900" height="200"></img></center></td></tr>';
        // echo '<tr><td><center><img class="NO-CACHE" src="' .  $apiHttp . $apiHostname . ':' . $apiPort . $renderRrd['data']['image'] . '"></img></center></td></tr>';  // DEFAULT sizing..
      }
    }
  }
  echo '</tbody>';
  echo '</table>';
?>
