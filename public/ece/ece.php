<?php
  /*
    Event Correlation Engine GUI

    Show all Parents and state of each.
    This is more of a red-light green-light display

    Default is OK, unless explicitly told there are
    issues based on 1) rules first 2) application variable in event

    Rules are absolute.  If they are set, you cannot override.

    States set in events CAN be overridden. (eventually)


    The API call is going to apply ALL of the rules and return the data
    for all rules it is aware of.  Makes more sense than filtering down
    at least for the V1 version.


  */

  echo '<META HTTP-EQUIV=Refresh CONTENT="120"> ';
  header("Cache-Control: no-store, no-cache, must-revalidate");

  echo '<br><br><br>'; // only needed if we have a horozontal bar

  // After POST sometimes it was not loading the functions.. sigh..
  if ( ! function_exists('debugger')) {
    require(__DIR__ . '/../../functions/generalFunctions.php');
  }
  checkCookie($_COOKIE);  // disable check here to test 40X responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";

  // Hosts and Devices have A LOT of variables in play.  We need functions specific to this group
  include __DIR__ . "/functions/eceFunctions.php";

  // Grab our POSSIBLE values so users can choose what they change
  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
  $cookieTimezone = $_COOKIE['clientTimezone'];
  $post = array();  // We are using post, so give it an empty array to post with
  $quitEarly = 0;

  // echo $cookieTimezone;
  // timezone stuff: https://stackoverflow.com/questions/6939685/get-client-time-zone-from-browser

  // Pull our events now
  // Get our event information now
  $rawActiveEce = callApiGet("/events/view/eventSeverity/DESC/order", $post, $headers);
  $activeEce = json_decode($rawActiveEvents['response'], true);

  // Try to count our ECE returns
  $eceCount = count($activeEce['data']);

  $eceList = $activeEce['data'];

  /*
    All this work to make our timezone dynamic, Gah!
    Managers cant tell time, we have to help them...  :P
  */
  $cookieTimezone = explode(' ', $cookieTimezone);
  $localOffset = ( $cookieTimezone[1] * 3600);   // hour offset * minutes in an hour
  if ( empty($localOffset)) {
    $localOffset = 0;
  }
  $localTime = (strtotime("now") + $localOffset);
  echo '<!-- cookieTimezone ' . print_r($cookieTimezone, true) . ' localTime ' . $localTime . '-->' . "\n";

  // This is where we show our states
  echo '<div class="container-fluid">';

  echo '</div>';
?>
