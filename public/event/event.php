<?php
  /*
    What are we going to display today?
  */
/*
  if ( ! isset($_COOKIE['showEventAck'])) {
    $convert = 8640000;
    $options = array(
      'expires' => time() + $convert,
      'path' => '/',
      'domain' => '',
      'secure' => 'false',
      'httponly' => 'false',
      'samesite' => 'Lax'
    );
debugger($options);
    $test = setcookie('showEventAck', 'true', $options);
    $_COOKIE['showEventAck'] = 'true';
debugger($test);
  }
*/

  echo '<META HTTP-EQUIV=Refresh CONTENT="45"> ';
  header("Cache-Control: no-store, no-cache, must-revalidate");
  // header('Refresh: 45');  // reload the damn page every X seconds

  echo '<br><br><br>'; // only needed if we have a horozontal bar


  // After POST sometimes it was not loading the functions.. sigh..
  if ( ! function_exists('debugger')) {
    require(__DIR__ . '/../../functions/generalFunctions.php');
  }
  checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  $displayAck = '';
  $displaySeverity = '';

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";

  // Hosts and Devices have A LOT of variables in play.  We need functions specific to this group
  include __DIR__ . "/functions/eventFunctions.php";

  // Grab our POSSIBLE values so users can choose what they change
  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
  $cookieTimezone = $_COOKIE['clientTimezone'];
  $post = array();  // We are using post, so give it an empty array to post with
  $quitEarly = 0;

  // echo $cookieTimezone;
  // timezone stuff: https://stackoverflow.com/questions/6939685/get-client-time-zone-from-browser

  // Modal calling post here.  Move to history before loading active events page
  if (isset($_POST['realMoveToHistory'])) {
    // debugger($_POST);
    $post = ['id' => $_POST['id']];
    $post += ['reason' => $_POST['reason']];
    $moveToHistory = callApiPost("/events/moveToHistory", $post, $headers);
    // Disalbe refreshes causing reposts
    echo '<script type="text/javascript">' . "\n";
    echo 'if ( window.history.replaceState ) {' . "\n";
    echo '  window.history.replaceState( null, null, window.location.href );' . "\n";
    echo '}' . "\n";
    echo '</script>' . "\n";
  }

  if (isset($_COOKIE['showEventAck'])) {
    $displayAck = $_COOKIE['showEventAck'];
  }
  else {
    $displayAck = 'true';
  }
  // Show only certain severities

  if (isset($_COOKIE['showEventSeverity'])) {
    $displaySeverity = $_COOKIE['showEventSeverity'];
    $displaySeverity = explode(',', $displaySeverity);
  }
  else {
    // explode saves as string, so match string on defaults
    $displaySeverity = [ 0 => "0", 1 => "1", 2 => "2", 3 => "3", 4 => "4", 5 => "5" ];
  }
  // debugger($displaySeverity);

  // Pull our events now
  // Get our event information now
  $rawActiveEvents = callApiGet("/events/view/eventSeverity/DESC/order", $post, $headers);
  $activeEvents = json_decode($rawActiveEvents['response'], true);

  // Try to count our events
  $eventCount = count($activeEvents['data']);

  $eventList = $activeEvents['data'];

  // second attempt at good counts
  $eventCount= count($eventList);

  /*
    All this work to make our timezone dynamic, Gah!
  */
  $cookieTimezone = explode(' ', $cookieTimezone);
  $localOffset = ( $cookieTimezone[1] * 3600);   // hour offset * minutes in an hour
  if ( empty($localOffset)) {
    $localOffset = 0;
  }
  $localTime = (strtotime("now") + $localOffset);
  echo '<!-- cookieTimezone ' . print_r($cookieTimezone, true) . ' localTime ' . $localTime . '-->' . "\n";

  echo '<div class="container-fluid">';
  echo "<table width='100%'><td>";
  // Little table to allow for filtering severities
  echo "<form id='saveFilter' action='' method='POST'>";
  for ($i = 0; $i < 6; $i++) {
    $checked = '';
    switch ($i) {
      case 1:
        if (in_array($i,$displaySeverity)) { $checked = 'checked'; }
        echo '<label><button class="btn btn-sm btn-outline-secondary "><input type="checkbox" name="activeFilter[]" value="1" ' . $checked . ' > Debug </button> </label>' . "\n";
        break;
      case 2:
        if (in_array($i,$displaySeverity)) { $checked = 'checked'; }
        echo '<label><button class="btn btn-sm btn-outline-primary ">  <input type="checkbox" name="activeFilter[]" value="2" ' . $checked . ' > Information </button> </label>' . "\n";
        break;
      case 3:
        if (in_array($i,$displaySeverity)) { $checked = 'checked'; }
        echo '<label><button class="btn btn-sm btn-outline-info "><input type="checkbox" name="activeFilter[]" value="3" ' . $checked . ' > Error </button> </label>' . "\n";
        break;
      case 4:
        if (in_array($i,$displaySeverity)) { $checked = 'checked'; }
        echo '<label><button class="btn btn-sm btn-outline-warning "><input type="checkbox" name="activeFilter[]" value="4" ' . $checked . ' > Warning </button> </label>' . "\n";
        break;
      case 5:
        if (in_array($i,$displaySeverity)) { $checked = 'checked'; }
        echo '<label><button class="btn btn-sm btn-outline-danger "><input type="checkbox" name="activeFilter[]" value="5" ' . $checked . ' > Critical </button></label>' . "\n";
        break;
    }  // end switch
  }  // end for loop
  echo '&nbsp<button type="submit" class="btn btn-sm btn-outline-primary" name="saveFilter" form="saveFilter"><i class="fas fa-bookmark"></i> Save filter</button>' . "\n";
  echo "</form></td>\n";
  
  echo "<td>";
  echo "<form id='chooseAck' action='' method='POST'>";
  if ($displayAck == 'true') {
    echo '<label><button type="submit" name="viewAck" form="chooseAck" value="false" class="btn btn-sm btn-outline-success ">Hide Acknoweleged</button></label>' . "\n";
  }
  else {
    echo '<label><button type="submit" name="viewAck" form="chooseAck" value="true" class="btn btn-sm btn-outline-success ">Show Acknoweleged</button></label>' . "\n";
  }
  echo "</form>\n";
  echo "</td>";

  echo "</div><div class='col text-right'>";
  echo '<td class="text-end"><button class="btn btn-sm btn-outline-dark ">Last Refresh: ' . date('Y-m-d H:i:s',$localTime) . "&nbsp&nbsp</button>  </td>";
  echo "</table>";
  echo "<br>";
  // Load the move to history modal here
  if (isset($_POST['moveToHistory']) ) {
    $evid = $_POST['evid'];
    $hostname = $_POST['hostname'];
    //debugger($_POST);
    //exit();
    unset($_POST);
    modalMoveToHistory($evid, $hostname);
    // Only load the JS when we need it
    echo '<script type="text/javascript">' . "\n";
    echo "    var myModal = new bootstrap.Modal(document.getElementById('eventToHistoryModal'), {})" . "\n";
    echo '    myModal.toggle()' . "\n";
    echo 'if ( window.history.replaceState ) {' . "\n";
    echo '  window.history.replaceState( null, null, window.location.href );' . "\n";
    echo '}' . "\n";
    echo '</script>' . "\n";
  }

  // Load the event details modal here
  if (isset($_POST['displayDetails']) ) {
    // unset($_POST['displayDetails']);
    // debugger($_POST);
    $eventDetails = $_POST;
    showEventModal($eventDetails);
    // Only load the JS when we need it
    echo '<script type="text/javascript">' . "\n";
    echo "    var myModal = new bootstrap.Modal(document.getElementById('showEventModal'), {})" . "\n";
    echo '    myModal.toggle()' . "\n";
    echo 'if ( window.history.replaceState ) {' . "\n";
    echo '  window.history.replaceState( null, null, window.location.href );' . "\n";
    echo '}' . "\n";
    echo '</script>' . "\n";
  }

  echo '</div>  <div class="container-fluid">';

?>
  <table id="dt-events" class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;">
    <thead>
      <tr>
        <th><center>Device</center></th>
        <th><center>Monitor</center></th>
        <th><center>Summary</center></th>
        <th><center>First Seen</center></th>
        <th><center>Last Update</center></th>
        <th><center>Count</center></th>
        <th><center>Severity</center></th>
        <th><center>Manipulation</center></th>
      </tr>
    </thead>
    <tbody>
  <!--    <div id="dt-events-table">  -->

  <?php

  ?>

      <?php
        foreach($eventList as $events) {
          if (in_array($events['eventSeverity'], $displaySeverity)) {
          //          debugger($events);
          switch ($events['eventSeverity']) {
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
          echo '<tr ' . $rowColor . ' >';
          if (empty($events['id'])) {
            // If we do not have a device that has an id, then go to the create device page
            echo '<td><center><a href="/host/index.php?&page=createDevice.php&hostname=' . $events['device'] . '&address=' . $events['eventAddress'] . '" target="_blank" ' . $linkColor . ' > ' .  $events['device'] . ' </a></center></td>';
          }
          else {
            // If we know we have a device id, then go to the device details page
            echo '<td><center><a href="/host/index.php?&page=deviceDetails.php&id=' . $events['id'] . '"' . $linkColor . ' > ' .  $events['device'] . ' </a></center></td>';
          }
          // This defines a modal for seeing as many event details as we can supply
          echo '<td>';
            echo '<form name="displayDetails" id="displayDetails' . $events['evid'] . '" role="form" action="" method="POST">';
            foreach ($events as $k => $v) {
              if ( in_array($k, ['eventRaw','eventDetails'])) {
                echo '<input type="hidden" name="' . $k . '" value="' . htmlspecialchars(json_encode($v,1)) . '">';
              }
              else {
                echo '<input type="hidden" name="' . $k . '" value="' . $v . '">';
              }
            }
            echo '<button type="submit" class="btn btn-sm btn-link" name="displayDetails" form="displayDetails' . $events['evid'] . '">' . $events['eventName'] . '</button>';
            echo '</form>';
          echo '</td>';
          // Normal event summary direct from database
          echo "<td>" . $events['eventSummary'] . "</td>\n";

          // Convert UTC to local time from browser
          $utcRaw1= strtotime($events['startEvent'] . ' UTC');
          $utcRaw=($utcRaw1 + $localOffset);
          $localTime=date('Y-m-d H:i:s', $utcRaw) . " $timezone";
          echo "<td>" . $localTime  . "</td>\n";
          $utcRaw1= strtotime($events['stateChange'] . ' UTC');
          $utcRaw=($utcRaw1 + $localOffset);
          $localTime=date('Y-m-d H:i:s', $utcRaw) . " $timezone";
          echo "<td>" . $localTime ."</td>\n";

          echo "<td><center>" . $events['eventCounter'] . "</center></td>\n";
          echo "<td><center>" . $events['eventSeverity'] . "</center></td>\n";
          echo "<td>";
            // Table inside the table to force the buttons to stay next to each other... sigh....
            echo '<table>';
             echo '<tr><td>';
               echo '<form id="moveToHistory' . $events['evid'] . '" role="form" action="" method="POST">';
               echo '<input type="hidden" name="event" value="' . json_encode($events,1) . '">';
               echo '<input type="hidden" name="evid" value="' . $events['evid'] . '">';
               echo '<input type="hidden" name="hostname" value="' . $events['hostname'] . '">';
               echo '<button type="submit" class="btn btn-sm btn-outline-primary" name="moveToHistory" form="moveToHistory' . $events['evid'] . '"><i class="fas fa-plane"></i></button>';
               echo '</form>';
               echo '</td><td>';
               echo '<form id="ackEvent" role="form" action="" method="POST">';
               echo '<input type="hidden" name="event" value="' . json_encode($events,1) . '">';
               echo '<button type="submit" class="btn btn-sm btn-outline-primary" name="ackEvent" form="ackEvent"><i class="fas fa-check-circle"></i></button>';
               echo '</form>';
               echo '</td><td>';
               echo '<form id="ticketEvent" role="form" action="" method="POST">';
               echo '<input type="hidden" name="event" value="' . json_encode($events,1) . '">';
               echo '<button type="submit" class="btn btn-sm btn-outline-primary" name="ticketEvent" form="ticketEvent"><i class="fas fa-suitcase"></i></button>';
               echo '</form>';
             echo '</td></tr>';
           echo '</table>';
          } // end if in_array filter for ignoring severities
        }  // end foreach loop

        ?>
        </div>
       </tbody>
     </table>
  <script> window.addEventListener("DOMContentLoaded", event => {
    const datatablesSimple = document.getElementById("dt-events");
    if (datatablesSimple) {
      new simpleDatatables.DataTable("#dt-events", {
        searchable: true,
        sortable: true,
        storable: true,
        paging: true,
        perPage: 25,
        perPageSelect:[25,50,100,200],
        labels: {
          placeholder: "Search Active Events"
        }
        });
      }
    });
//    setTimeout(function(){
//      simpleDatatables.datatable.refresh();
//      window.location.reload(1);
//    }, 5000);
  </script>
  <!-- datatables not loaded with footer, add it now -->
  <script src="/js/simple-datatables/simple-datatables.js"></script>

<?php
  echo '  </div>';  // be complete... sigh..
?>
