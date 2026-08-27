<!DOCTYPE html>
<html lang="en">
<META HTTP-EQUIV=Refresh CONTENT="30">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Main Event Table" />
  <meta name="author" content="Chris Hubbard" />

  <title>Event Table</title>
  <link href="/js/sb-demo/css/styles.css" rel="stylesheet" />
  <link href="/js/bootstrap-5/css/bootstrap.css" rel="stylesheet" />
  <!-- Needs loaded early for inline script to calc the timezone -->
  <script src="/js/jquery/jquery-1.7.1.min.js"></script> 
  <script src="/js/bootstrap-5/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</head>

<!-- fas == font awesome javascript.  Has nice icons, etc -->
<!-- https://fontawesome.com/search?m=free  choose icon, and find the name.  Call in the i class= to integrate in -->
<body class="bg-light">
  <?php
  require_once __DIR__ . "/../../config/api.php";
  // Later version of UI will already have a session set, so this part can go away then
  if ( ! isset($_SESSION)) {
    session_start();
    $timezone = $_SESSION['time'];
    $raw=explode( ' ', $timezone);
    $offset=$raw[1];
    $localOffset=( $offset * 3600);
  }
  ?>

  <!-- Figure out the BROWSER time.  Server local should ALWAYS be UTC.  End user however can be anywhere  -->
  <script type="text/javascript">
    $(document).ready(function() {
    if("<?php echo $timezone; ?>".length==0){
        var visitortime = new Date();
        var visitortimezone = "GMT " + -visitortime.getTimezoneOffset()/60;
        $.ajax({
          type: "GET",
          url: "/event/timezone.php",
          data: 'time='+ visitortimezone,
          success: function(){
          location.reload();
          }
        });
      }
    });
  </script>

<?php
  // this should get changed later to use a function call
  $ch=curl_init();
  curl_setopt($ch, CURLOPT_URL, $apiUrl .":" . $apiPort . "/events/view/eventSeverity/DESC/order");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  $output = curl_exec($ch) ;
  $output = json_decode($output, true);
  $eventCount= count($output);
?>
<!-- Modal -->
<?php
$staticValuesTop = '
    <div class="modal-dialog modal-xl">
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <h4 style="color:red;"><span class="glyphicon glyphicon-lock"></span> Event Details</h4>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">';

$staticValuesBottom = '
        </div>
      </div>
    </div>
  </div>
</div>';

?>

  <!-- DataTable ONLY hopefully -->
<?php
  $localTime = (strtotime("now") + $localOffset);
  echo '<p class="text-end">Last Refresh: ' . date('Y-m-d H:i:s',$localTime) . "&nbsp&nbsp  </p>";
  /*
    BG color needs to be inherited somehow for entire page
    Dont foget this is normally within an iframe
  */
?>

               <table id="datatablesSimple" class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate">
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
                   <div id="dataTable01">

                     <!-- This table data is PHP generated -->
                     <!-- future should be able to use DOM for more options -->
                   <?php
//echo "HERE " . print_r($output['data']) . "\n";
                     foreach($output as $counter) {
//echo "<BR>" . print_r($counter) . "\n";
                       foreach($counter as $event) {
//echo "<br>" . print_r($event) . "\n";
//echo "<br>SEV " . $event['eventSeverity'] . "\n";
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
                           echo "<td><center><a href='/host/hostnameDetails.php?id=" . $event['device'] . "' target='_blank' " . $linkColor . ' > ' .  $event['device'] . ' </a></center></td>';

// MODAL for eventName
//                           echo "<td><a href='/event/eventDetails.php?evid=" . $event['evid'] . "' target='_blank'> " . $event['eventName'] . "</a></td>\n";
//                           echo "<td><a data-bs-toggle='modal' href='#detailModal-" . $event['evid'] . "'> " . $event['eventName'] . "</a></td>\n";
                           echo "<td><p class='link-primary' data-bs-toggle='modal' data-bs-target='#detailModal-" . $event['evid'] . "'><u> " . $event['eventName'] . "</u></p></td>\n";  // Pretend to be a link



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

                           echo "<td><center>" . $event['eventCounter'] . "</center></td>\n";
                           echo "<td><center>" . $event['eventSeverity'] . "</center></td>\n";
                           echo "<td><center><a href='ack.php?evid=" . $event['evid'] ."'><img src=/images/icons/check2-circle.svg class='img-fluid' alt='Acknowledge'></img></a> &nbsp&nbsp&nbsp ";
//                           echo "<a href='ticket.php?evid=" . $event['evid'] ."'><img src=/images/icons/wrench.svg class='img-fluid' alt='Ticket'></img></a> &nbsp&nbsp&nbsp";
                           echo "<a href='history.php?evid=" . $event['evid'] ."'><img src=/images/icons/trash3.svg class='img-fluid' alt='History'></img></a> &nbsp&nbsp&nbsp";
//                           echo "<a href='eventDetails.php?evid=" . $event['evid'] ."'><img src=/images/icons/zoom-in.svg class='img-fluid' alt='Details'></img></a>\n";
                           echo "</center></td></tr>";
                         }  // end inner foreach loop
                       }  // end primary foreach loop
                       echo "</div>";
                       ?>
                       <!-- Back to static HTML -->
                       </tbody>
                     </table>
                   </div>
                 </div>
               </div>
             </div>

<?php
  /*
    This needs luv, but at least it exists
    Also need to figure out how to disable refresh when the modal is loaded or in focus
  */
  foreach ($output['data'] as $events) {
    echo '<div class="modal fade" id="detailModal-' . $events['evid'] . '" role="dialog"> ';
    echo $staticValuesTop . "<br>\n";
    echo "<table>";
    echo "<tr><td>Event ID:</td><td> " . $events['evid'] . "</td></tr>";
    echo "<tr><td>Device:</td><td> " . $events['device'] . "</td></tr>";
    echo "<tr><td>Event Summary:</td><td> " . $events['eventSummary'] . "</td></tr>";
    echo "<tr><td>Start of Event:</td><td> " . $events['startEvent'] . "</td></tr>";
    echo "<tr><td>Last Update:</td><td> " . $events['stateChange'] . "</td></tr>";
    echo "<tr><td>Age Out Value:</td><td> " . $events['eventAgeOut'] . "</td></tr>";
    echo "<tr><td>Event Counter:</td><td> " . $events['eventCounter'] . "</td></tr>";
    echo "<tr><td>Event Severity:</td><td> " . $events['eventSeverity'] . "</td></tr>";
    echo "<tr><td>Event Proxied by:</td><td> " . $events['eventProxyIp'] . "</td></tr>";
    echo "<tr><td>Event Name:</td><td> " . $events['eventName'] . "</td></tr>";
    echo "<tr><td>Event Type:</td><td> " . $events['eventType'] . "</td></tr>";
    echo "<tr><td>Event Receiver:</td><td> " . $events['eventReceiver'] . "</td></tr>";
    echo "<tr><td>Event Monitor:</td><td> " . $events['eventMonitor'] . "</td></tr>";
    echo "<tr><td>Event Details:</td><td> " . $events['eventDetails'] . "</td></tr>";
    if ( is_array($events['eventRaw']) ) {
      echo "<td>Event Raw:</td><td> " . print_r($events['eventRaw']) . "</td></tr>";
    }
    else {
       echo "<td>Event Raw:</td><td> " . $events['eventRaw'] . "</td></tr>";
    }
    echo "</table>";
    echo $staticValuesBottom . "<br>\n";
  }
?>

  <script src="/js/font-awesome/all.min.js" crossorigin="anonymous"></script>
  <script src="/js/bootstrap-5/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="/js/simple-datatables/simple-datatables.js"></script>
  <script src="/js/simple-datatables/script.js"></script>
  <script src="/js/sb-demo/js/scripts.js"></script>
  <script src="/js/light-switch-bootstrap-main/switch.js"></script>

</body>
</html>
