<?php
/*
  Although UI is using hostname, we actually prefer id from the device/host table
  as the id value is the final truth.  Unfortunately, the filesystem and Graphite
  paths are defined on a hostname basis.  Due to this, queries are based on hostname
  when we have to, but will be id when possible elsewhere.
*/


// Safety net if we somehow do not have the varaibles to deal with the API
if (! isset($apiHostname)) {
  include_once __DIR__ . '/../../../config/api.php';
}

// Use curl from one spot when we can
function curlCall($url, $type = null, $options) {
  if ( is_null($type)) {
    $type="get";
  }
  $ch2 = curl_init();
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

function getEventDecending() {
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/events/view/eventSeverity/DESC/order';
  $type = 'get';
  $options=[];
  $result = curlCall($url,$type,$options);
  return $result;
}

// https://stackoverflow.com/questions/9166914/using-default-arguments-in-a-function
function getEventSorted( $filterName = null, $direction = null) {
  if ( null === $filterName ) {
    $filterName = 'eventSeverity';
  }
  if ( null === $direction ) {
    $direction = "DESC";
  }
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/events/view/' . $filterName . '/' . $direction . '/order';
  $type = 'get';
  $options=[];
  $result = curlCall($url,$type,$options);
  return $result;
}

function getEventFiltered( $filterName = null, $direction = null , $filterValue = null ) {
  if ( null === $filterName ) {
    $filterName = 'eventSeverity';           // Can be any field from event
  }
  if ( null === $direction ) {
    $direction = "DESC";                     // Logic in place for before, after, ASC, DESC.  ASC and DESC only make sense with $filterValue of order or something numberic.
  }
  if ( null === $filterValue ) {
    $filterValue = 'order' ; // Note that it is URLized.  values are filter or the word order
    // $filterValue = '2021-02-04%2002:25:57' ; // Note that it is URLized.  values are filter or the word order
  }
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url = $apiHttp . $apiHostname . ':' . $apiPort . '/events/view/' . $filterName . '/' . $direction . '/' . $filterValue;
  $type = 'get';
  $options=[];
  $result = curlCall($url,$type,$options);
  return $result;
}

// Basic show modal with predefined body
function showEventModal($array) {
//  echo '<div class="modal modal-xl" tabindex="-1" id="showEventModal">';
  echo '<div class="modal modal-xl" id="showEventModal">';
  echo '  <div class="modal-dialog modal-xl">';
  echo '    <div class="modal-content">';
  echo '      <div class="modal-header">';
  echo '        <h5 class="modal-title">Event Detail</h5>';
  echo '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
  echo '      </div>';
  echo '      <div class="modal-body">';
  echo '        <table class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate">';
  foreach ($array as $k => $v) {
    if ( $k !== 'eventRaw') {
      echo '      <tr><td>' . $k . '</td><td>' . $v . '</td></tr>';
    }
  }
  echo '        </table>';
  echo '      </div>';
  echo '      <div class="modal-footer">';
  echo '      </div>';
  echo '    </div>';
  echo '  </div>';
  echo '</div>';
}

function modalMoveToHistory($evid, $hostname) {
  $user = $_COOKIE['realName'];
  $userId = $_COOKIE['id'];

  echo '<div class="modal" tabindex="-1" id="eventToHistoryModal">';
  echo '  <div class="modal-dialog">';
  echo '    <div class="modal-content">';
  echo '      <div class="modal-header">';
  echo '        <h5 class="modal-title">Move event to history</h5>';
  echo '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
  echo '      </div>';
  echo '      <div class="modal-body">';

  echo '        <form class="form-inline" id="realMoveToHistory" role="form" action="" method="POST">';  // Should post to itself outside of the modal

  echo '        <div class="form-floating mb-3">';
  echo '          <input class="form-control" type="text" name="id" value="' . $evid . '" readonly>';
  echo '          <label for="id">Event ID</label>';
  echo '        </div>';

  echo '        <div class="form-floating mb-3">';
  echo '          <input class="form-control" type="text" name="hostname" value="' . $hostname . '" readonly>';
  echo '          <label for="id">Hostname</label>';
  echo '        </div>';

  echo '        <div class="form-floating mb-3">';
  echo '          <input class="form-control" type="textarea" name="reason" value="">';
  echo '          <label for="reason">Reason to move</label>';
  echo '        </div>';

  echo '        <div class="form-floating mb-3">';
  echo '          <input class="form-control" type="text" name="user" value="' . $user . '" readonly>';
  echo '          <label for="user">Your Username</label>';
  echo '        </div>';
  echo '        <input type="hidden" name="userid" value="' . $userId . '">';

  echo '        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">';
  echo '          <button type="submit" class="btn btn-warning" name="realMoveToHistory" form="realMoveToHistory"> Move to history </button> ';
  echo '        </div>';

  echo '        </form>';

  echo '      </div>';
  echo '      <div class="modal-footer">';
  echo '      </div>';
  echo '    </div>';
  echo '  </div>';
  echo '</div>';
}

function modalMoveFromHistory($evid, $hostname) {
  $user = $_COOKIE['realName'];
  $userId = $_COOKIE['id'];

  echo '<div class="modal" tabindex="-1" id="eventFromHistoryModal">';
  echo '  <div class="modal-dialog">';
  echo '    <div class="modal-content">';
  echo '      <div class="modal-header">';
  echo '        <h5 class="modal-title">Move event from history to active event</h5>';
  echo '        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
  echo '      </div>';
  echo '      <div class="modal-body">';

  echo '        <form class="form-inline" id="realMoveFromHistory" role="form" action="" method="POST">';  // Should post to itself outside of the modal

  echo '        <div class="form-floating mb-3">';
  echo '          <input class="form-control" type="text" name="id" value="' . $evid . '" readonly>';
  echo '          <label for="id">Event ID</label>';
  echo '        </div>';

  echo '        <div class="form-floating mb-3">';
  echo '          <input class="form-control" type="text" name="hostname" value="' . $hostname . '" readonly>';
  echo '          <label for="id">Hostname</label>';
  echo '        </div>';

  echo '        <div class="form-floating mb-3">';
  echo '          <input class="form-control" type="textarea" name="reason" value="">';
  echo '          <label for="reason">Reason to move</label>';
  echo '        </div>';

  echo '        <div class="form-floating mb-3">';
  echo '          <input class="form-control" type="text" name="user" value="' . $user . '" readonly>';
  echo '          <label for="user">Your Username</label>';
  echo '        </div>';
  echo '        <input type="hidden" name="userid" value="' . $userId . '">';

  echo '        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">';
  echo '          <button type="submit" class="btn btn-warning" name="realMoveFromHistory" form="realMoveFromHistory"> Move from history </button> ';
  echo '        </div>';

  echo '        </form>';

  echo '      </div>';
  echo '      <div class="modal-footer">';
  echo '      </div>';
  echo '    </div>';
  echo '  </div>';
  echo '</div>';
}

?>
