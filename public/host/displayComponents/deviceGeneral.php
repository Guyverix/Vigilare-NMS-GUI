<?php
/*
  This page is where we are going to setup our different
  containers / cards / etc.  We want users to be able
  to change things to what makes sense for them

  This page is not used to manipulate code, only set
  the display for bootstrap to render.

  There should not be any calculations done here.
*/

/*
  Useful if we cannot remember the array details
  to build our containers.

  Each require has this varialbe available:
  debugger($sharedDevice);

  Filtered example: (only historical events)
  debugger($sharedDevice['historyEvents']);
*/

/*
  This is top row, by default 2 containers
  host information, and some details for the host
*/

//  debugger($sharedDevice['performance']);


echo "<div class='container'>";
  echo "<div class='row'>";
    echo "<div class='col-lg'>";
      require (__DIR__ . '/leftGeneralHost.php');
    echo "</div>";
    echo "<div class='col-sm'>";
      require (__DIR__ . '/rightGeneralHostMonitorsAccordion.php');
      require (__DIR__ . '/rightGeneralHostPortsUsedAccordion.php');
      require (__DIR__ . '/rightGeneralHostIpRouteAccordion.php');
      require (__DIR__ . '/rightGeneralHostSsIndexAccordion.php');
    echo "</div>";
  echo "</div>";
echo "</div>";

/*
  Below here is larger stuff.  Specifically events
  and event history.  These are good for at a glance
  but usefulness may be limited
*/

//echo "<div class='container-fluid'>";
echo "<div class='container-lg'>";
  require (__DIR__ . '/bottomGeneralHostActiveEventBadge.php');
  require (__DIR__ . '/bottomGeneralHostHistoricalEventBadge.php');
echo "</div>";

/*
  This is simply a staging area for testing
  drop your container or card on the page
  and check rendering and data easily
*/

//require (__DIR__ . '/right2GeneralHost.php');

/*
  After this point the requires are done, and we go back to
  the parent page and finish with things like footers, etc.
*/

?>
