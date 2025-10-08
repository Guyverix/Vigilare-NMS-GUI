<?php
  // Get our utilities in place asap.

  include_once __DIR__ . "/functions/eventFunctions.php";
  include_once __DIR__ . "/../../functions/generalFunctions.php";

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";
  echo "<br><br><br>";
  // Grab our POSSIBLE values so users can choose what they change
  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];

  // Get our local timezones so we can get  stuff looking right
  $cookieTimezone = $_COOKIE['clientTimezone'];
  $cookieTimezoneArr = explode(' ', $cookieTimezone);
  $localOffset = ((int)($cookieTimezoneArr[1] ?? 0) * 3600);

  $post = array();  // We are using post, so give it an empty array to post with
  $quitEarly = 0;

  $rawActiveEvents = callApiGet("/events", $headers);
  $activeEvents = json_decode($rawActiveEvents['response'], true);
  $eventCount = count($activeEvents['data']);
  // debugger($activeEvents);
  // exit();

  $rawHistoryEvents = callApiGet("/history/viewLimit/200", $headers);
  $historyEvents = json_decode($rawHistoryEvents['response'], true);
  $historyCount = count($historyEvents['data']);
  // debugger($historyEvents);
  // exit();

?>
          <div class="container-fluid">
            <div class="card mb-1">
             <div class="card-body table-responsive">Active Events
               <table id="dt-activeEvents" class="table table-striped table-hover" data-loading-template="loadingTemplate">
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
                 <tfoot>
                   <tbody>
                   <div id="dt-activeEvents1">

                     <!-- This table data is PHP generated -->
                     <!-- future should be able to use DOM for more options -->
                     <?php
                       foreach($activeEvents['data'] as $event) {
                           echo "<tr>\n";
                           echo "<td><center><a href='/host/index.php?&page=deviceDetails.php&id=" . $event['id'] . "' target='_blank' " . $linkColor . ' > ' .  $event['device'] . ' </a></center></td>';
                           echo "<td>" . $event['eventName'] . "</td>\n";
                           echo "<td>" . $event['eventSummary'] . "</td>\n";

                           /*  Convert UTC to local users timezone so they understand when events happened */
                           $localTime = convertUtcLocal($event['startEvent'] . ' UTC', $localOffset);
                           echo "<td>" . $localTime ." (" . $cookieTimezone . ")</td>\n";

                           $localTime = convertUtcLocal($event['stateChange'] . ' UTC', $localOffset);
                           echo "<td>" . $localTime ." (" . $cookieTimezone . ")</td>\n";

                           echo "<td>" . $event['eventCounter'] . "</td>\n";
                           echo "<td>" . sevBadge($event['eventSeverity']) . "</td>\n";
                           echo "<td><center><a href='/event/index.php?&page=replaySpecificEvent.php&evid=" . $event['evid'] ."&table=events'><i class='fas fa-heartbeat'></i></a> &nbsp&nbsp&nbsp ";
                           echo "</td>\n";
                           echo "</tr>\n";
                         }  // end foreach loop
                       echo "</div>";
                       ?>
                       <!-- Back to static HTML -->
                       </tbody>
                     </table>
                   </div>
                 </div>
               </div>
<!--              </div>  -->
          <div class="container-fluid">
            <div class="card mb-1">
             <div class="card-body table-responsive">Historical Events
               <table id="dt-historyEvents" class="table table-striped table-hover" data-loading-template="loadingTemplate">
                 <thead>
                   <tr>
                     <th><center>Device</center></th>
                     <th><center>Monitor</center></th>
                     <th><center>Summary</center></th>
                     <th><center>First Seen</center></th>
                     <th><center>End Event</center></th>
                     <th><center>Count</center></th>
                     <th><center>Severity</center></th>
                     <th><center>Manipulation</center></th>
                   </tr>
                 </thead>
                 <tfoot>
                   <tbody>
                   <div id="dt-historyEvents1">

                     <!-- This table data is PHP generated -->
                     <!-- future should be able to use DOM for more options -->
                     <?php
                       foreach($historyEvents['data'] as $event) {
                           echo "<tr>\n";
                           echo "<td><center><a href='/host/index.php?&page=deviceDetails.php&id=" . $event['id'] . "' target='_blank' > " .  $event['device'] . ' </a></center></td>';
                           echo "<td>" . $event['eventName'] . "</td>\n";
                           echo "<td>" . $event['eventSummary'] . "</td>\n";

                           // Convert UTC to local time from browser
                           $localTime = convertUtcLocal($event['startEvent'] . ' UTC', $localOffset);
                           echo "<td>" . $localTime ." (" . $cookieTimezone . ")</td>\n";

                           $localTime = convertUtcLocal($event['endEvent'] . ' UTC', $localOffset);
                           echo "<td>" . $localTime ." (" . $cookieTimezone . ")</td>\n";

                           echo "<td>" . $event['eventCounter'] . "</td>\n";
                           echo "<td>" . sevBadge($event['eventSeverity']) . "</td>\n";
                           echo "<td><center><a href='/event/index.php?&page=replaySpecificEvent.php&evid=" . $event['evid'] ."&table=history'><i class='fas fa-heartbeat'></i></a> &nbsp&nbsp&nbsp ";
                           echo "</td>\n";
                           echo "</tr>\n";
                         }  // end foreach
                       echo "</div>";
                       ?>
                       <!-- Back to static HTML -->
                       </tbody>
                     </table>
                   </div>
                 </div>
               </div>
             </div>
           </main>
         </div>

  <script> window.addEventListener("DOMContentLoaded", event => {
    const datatablesSimple = document.getElementById("dt-activeEvents");
    if (datatablesSimple) {
      new simpleDatatables.DataTable("#dt-activeEvents", {
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
  <script> window.addEventListener("DOMContentLoaded", event => {
    const datatablesSimple = document.getElementById("dt-historyEvents");
    if (datatablesSimple) {
      new simpleDatatables.DataTable("#dt-historyEvents", {
        searchable: true,
        sortable: true,
        storable: true,
        paging: true,
        perPage: 25,
        perPageSelect:[25,50,100,200],
        labels: {
          placeholder: "Search Historical Events"
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
