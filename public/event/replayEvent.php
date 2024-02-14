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
  $cookieTimezone = $_COOKIE['clientTimezone'];
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
            <div class="card mb-1 bg-light">
             <div class="card-body table-responsive">Active Events
               <table id="dt-activeEvents" class="table table-striped table-hover bg-light table-dark" data-loading-template="loadingTemplate">
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
                         switch ($event['eventSeverity']) {
                           case "0":
                             $rowColor='class="table-success"';
                             $linkColor='class="link-danger"';
                              break;
                            case "1":
                              $rowColor='class="table-secondary"';
                               $linkColor='class="link-danger"';
                               break;
                             case "2":
                               $rowColor='class="table-primary"';
                               $linkColor='class="link-danger"';
                               break;
                             case "3":
                               $rowColor='class="table-info"';
                               $linkColor='class="link-danger"';
                               break;
                             case "4":
                               $rowColor='class="table-warning"';
                               $linkColor='class="link-danger"';
                               break;
                             case "5":
                               $rowColor='class="table-danger"';
                               $linkColor='class="link-primary"';
                               break;
                           }
                           echo "<tr $rowColor >\n";
                           echo "<td><center><a href='/host/deviceDetails.php?id=" . $event['device'] . "' target='_blank' " . $linkColor . ' > ' .  $event['device'] . ' </a></center></td>';
                           echo "<td>" . $event['eventName'] . "</td>\n";
                           echo "<td>" . $event['eventSummary'] . "</td>\n";

                           // Convert UTC to local time from browser
                           $utcRaw1= strtotime($event['startEvent'] . ' UTC');
                           $utcRaw=($utcRaw1 + $localOffset);
                           $localTime=date('Y-m-d H:i:s', $utcRaw) . " $timezone";
                           echo "<td>" . $localTime  . "</td>\n";
                           $utcRaw1= strtotime($event['stateChange'] . ' UTC');
                           $utcRaw=($utcRaw1 + $localOffset);
                           $localTime=date('Y-m-d H:i:s', $utcRaw) . " $timezone";
                           echo "<td>" . $localTime ."</td>\n";

                           echo "<td>" . $event['eventCounter'] . "</td>\n";
                           echo "<td>" . $event['eventSeverity'] . "</td>\n";
                           echo "<td><center><a href='/event/index.php?&page=replaySpecificEvent.php&evid=" . $event['evid'] ."&table=events'><img src=/images/icons/heartbreak.svg class='img-fluid' alt='replay'></img></a> &nbsp&nbsp&nbsp ";
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
<!--             </div>  -->
          <div class="container-fluid">
            <div class="card mb-1 bg-light">
             <div class="card-body table-responsive">Historical Events
               <table id="dt-historyEvents" class="table table-striped table-hover bg-light table-dark" data-loading-template="loadingTemplate">
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
                         switch ($event['eventSeverity']) {
                           case "0":
                             $rowColor='class="table-success"';
                             $linkColor='class="link-danger"';
                              break;
                            case "1":
                              $rowColor='class="table-secondary"';
                               $linkColor='class="link-danger"';
                               break;
                             case "2":
                               $rowColor='class="table-primary"';
                               $linkColor='class="link-danger"';
                               break;
                             case "3":
                               $rowColor='class="table-info"';
                               $linkColor='class="link-danger"';
                               break;
                             case "4":
                               $rowColor='class="table-warning"';
                               $linkColor='class="link-danger"';
                               break;
                             case "5":
                               $rowColor='class="table-danger"';
                               $linkColor='class="link-primary"';
                               break;
                           }
                           echo "<tr $rowColor >\n";
                           echo "<td><center><a href='/host/deviceDetails.php?id=" . $event['device'] . "' target='_blank' " . $linkColor . ' > ' .  $event['device'] . ' </a></center></td>';
                           echo "<td>" . $event['eventName'] . "</td>\n";
                           echo "<td>" . $event['eventSummary'] . "</td>\n";

                           // Convert UTC to local time from browser
                           $utcRaw1= strtotime($event['startEvent'] . ' UTC');
                           $utcRaw=($utcRaw1 + $localOffset);
                           $localTime=date('Y-m-d H:i:s', $utcRaw) . " $timezone";
                           echo "<td>" . $localTime  . "</td>\n";
                           $utcRaw1= strtotime($event['endEvent'] . ' UTC');
                           $utcRaw=($utcRaw1 + $localOffset);
                           $localTime=date('Y-m-d H:i:s', $utcRaw) . " $timezone";
                           echo "<td>" . $localTime ."</td>\n";

                           echo "<td>" . $event['eventCounter'] . "</td>\n";
                           echo "<td>" . $event['eventSeverity'] . "</td>\n";
                           echo "<td><center><a href='/event/index.php?&page=replaySpecificEvent.php&evid=" . $event['evid'] ."&table=history'><img src=/images/icons/heartbreak.svg class='img-fluid' alt='ack'></img></a> &nbsp&nbsp&nbsp ";
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
