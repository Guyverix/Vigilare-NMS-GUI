<?php
  /*
    What are we going to display today?
  */

  // header('Refresh: 45');  // reload the damn page every X seconds NOT ON HISTORY

  //echo '<META HTTP-EQUIV=Refresh CONTENT="10"> ';
  echo '<br><br><br>'; // only needed if we have a horozontal bar


  // After POST sometimes it was not loading the functions.. sigh..
  if ( ! function_exists('debugger')) {
    require(__DIR__ . '/../../functions/generalFunctions.php');
  }
  // checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";

  // Hosts and Devices have A LOT of variables in play.  We need functions specific to this group
  // require_once __DIR__ . "/functions/eventFunctions.php";
  include __DIR__ . "/functions/eventFunctions.php";

  // Grab our POSSIBLE values so users can choose what they change
  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
  $cookieTimezone = $_COOKIE['clientTimezone'];
  $post = array();  // We are using post, so give it an empty array to post with
  $quitEarly = 0;


  // echo $cookieTimezone;
  // timezone stuff: https://stackoverflow.com/questions/6939685/get-client-time-zone-from-browser
  // Pull our events now

  // Modal calling post here.  Move to history before loading active events page
  if (isset($_POST['realMoveToActive'])) {
    // debugger($_POST);
    $post = ['id' => $_POST['id']];
    $post += ['reason' => $_POST['reason']];
    $moveToHistory = callApiPost("/event/moveFromHistory", $post, $headers);
    // Disalbe refreshes causing reposts
    echo '<script type="text/javascript">' . "\n";
    echo 'if ( window.history.replaceState ) {' . "\n";
    echo '  window.history.replaceState( null, null, window.location.href );' . "\n";
    echo '}' . "\n";
    echo '</script>' . "\n";
  }

  // Get our event information now
  $rawActiveEvents = callApiGet("/history/viewLimit/200", $post, $headers);
  $activeEvents = json_decode($rawActiveEvents['response'], true);


  // Try to count our events
  $eventCount = count($activeEvents['data']);

  $eventList = $activeEvents['data'];

  // second attempt at good counts
  $eventCount= count($eventList);

  $cookieTimezone = explode(' ', $cookieTimezone);
  $localOffset = ( $cookieTimezone[1] * 3600);   // hour offset * minutes in an hour
  if ( empty($localOffset)) {
    $localOffset = 0;
  }

  $localTime = (strtotime("now") + $localOffset);

  // This needs a home INSIDE the display!
  echo '<p class="text-end">Last Refresh: ' . date('Y-m-d H:i:s',$localTime) . "&nbsp&nbsp  </p>";

  // Load the move to history modal here
  if (isset($_POST['moveFromHistory']) ) {
    $evid = $_POST['evid'];
    $hostname = $_POST['hostname'];
    //debugger($_POST);
    //exit();
    unset($_POST);
    modalMoveFromHistory($evid, $hostname);
    // Only load the JS when we need it
    echo '<script type="text/javascript">' . "\n";
    echo "    var myModal = new bootstrap.Modal(document.getElementById('eventFromHistoryModal'), {})" . "\n";
    echo '    myModal.toggle()' . "\n";
    echo 'if ( window.history.replaceState ) {' . "\n";
    echo '  window.history.replaceState( null, null, window.location.href );' . "\n";
    echo '}' . "\n";
    echo '</script>' . "\n";
  }

  // Load the event details modal here
  if (isset($_POST['displayDetails']) ) {
    unset($_POST['displayDetails']);
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

?>
  <div class="container-fluid">
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
            echo '<td><center><a href="/host/index.php?&page=deviceDetails.php&id=' . $events['id'] . '" target="_blank" ' . $linkColor . ' > ' .  $events['device'] . ' </a></center></td>';
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
               echo '<form id="moveFromHistory' . $events['evid'] . '" role="form" action="" method="POST">';
               echo '<input type="hidden" name="event" value="' . json_encode($events,1) . '">';
               echo '<input type="hidden" name="evid" value="' . $events['evid'] . '">';
               echo '<input type="hidden" name="hostname" value="' . $events['device'] . '">';
               echo '<button type="submit" class="btn btn-sm btn-outline-primary" name="moveFromHistory" form="moveFromHistory' . $events['evid'] . '"><i class="fas fa-plane"></i></button>';
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
echo '  </div>';
//echo '  <script src="/js/simple-datatables/simple-datatables.js"></script>';
//echo '  <script src="/js/simple-datatables/script.js"></script>';

?>
