<?php
  /*
    We have graphs?  Here is where we show them at.
  */

  echo '<br><br><br>'; // only needed if we have a horozontal bar

  // Only needed for debugging and bypassing security, etc
  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  // checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";

  // Hosts and Devices have A LOT of variables in play.  We need functions specific to this group
  require_once __DIR__ . "/functions/hostFunctions.php";

  // Grab our POSSIBLE values so users can choose what they change
  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
  $post = array();  // We are using post, so give it an empty array to post with
  $quitEarly = 0;

  // Set default data for the page
  if (isset($_POST['id'])) {
    $id = $_POST['id'];
  }
  else {
    $quitEarly = 1;
  }

  if (isset($_POST['hostname'])) {
    $hostname = $_POST['hostname'];
  }
  else {
    $quitEarly = 1;
  }

  /*
    Unused currently, but future?
  */
  if (isset($_POST['startTime'])) {
   $startTime = $_POST['startTime'];
  }
  if (isset($_POST['endTime'])) {
    $endTime = $_POST['endTime'];
  }
  if (isset($_POST['timeDelta'])) {
    $timeDelta = $_POST['timeDelta'];
  }
  if(isset($_POST['ignoreMatch'])) {
    $ignoreMatch = $_POST['ignoreMatch'];
  }

  /*
    Decide at some point if we want only active
    monitor graphs or ANY that exist without filtering.
    The activeMonitor list will state if we are working
    with RRD, Graphite, Influx, or TBD and go from there.
  */

  if(isset($_POST['activeMonitors'])) {
    $rawActiveMonitors = $_POST['activeMonitors'];
    $activeMonitors = json_decode($rawActiveMonitors, true);
  }

  $storage = array();
  foreach($activeMonitors as $activeMonitor) {
    if (! in_array($activeMonitor['storage'], $storage)) {
      $storage[] = $activeMonitor['storage'];
    }
  }


  $post = [ 'hostname' => $hostname ];
  $post += ['task' => 'findGraphs'];  // task is specific to Graphite, but will not harm RRD API
  if (in_array('rrd', $storage)) {
    $rawRrdGraphs = callApiPost("/render/findRrd", $post, $headers);

    // Remeber a given RRD file can have many templates using it
    $rrdGraphs =  json_decode($rawRrdGraphs['response'], true);

    // Make a CSV of our files to POST
    $rrdFileList = '';
    foreach ($rrdGraphs['data'] as $val) {
      $rrdFileList = $rrdFileList . '"' . $val. '",';
    }
    $rrdFileList = rtrim($rrdFileList, ',');
    $post += ['files' => $rrdFileList ];
    $rawRrdTemplates = callApiPost("/render/findRrdTemplates", $post, $headers);
    $rrdTemplates = json_decode($rawRrdTemplates['response'], true);
    /*
      At this point we know what file can use what template
      Make an array of the available templates, as we are going to use them to make
      The RRD Display.  We are going to render based on the template name initially
     */
    $rrdTemplateName = array();
/*
    foreach ($rrdTemplates['data'] as $rrdTemplate) {
      foreach ($rrdTemplate as $fileName => $fileTemplate) {
        if (! in_array($fileTemplate, $rrdTemplateName)) {
          $rrdTemplateName[] = $fileTemplate;
        }
      }
    }
*/

    foreach ($rrdTemplates['data'] as $rrdTemplate) {
      foreach ($rrdTemplate as $fileName => $fileTemplate) {
        if (array_key_exists($fileTemplate, $rrdTemplateName)) {
          $rrdTemplateName[$fileTemplate] .= '"' . $fileName . '",';
        }
        else {
          $rrdTemplateName[$fileTemplate] = '"' . $fileName . '",';
        }
      }
    }


  }

//debugger($rrdTemplateName);
//debugger($rrdTemplates);
//exit();


  if (in_array('graphite', $storage)) {
    // Find our pollers
    $post = [ 'task' => 'findMonitored' ];
    $post += [ 'hostname' => $hostname ];
    $rawListOfPollers = callApiPost("/graphite/test", $post, $headers);
    $fullListOfPollers = json_decode($rawListOfPollers['response'], true);
    foreach ($fullListOfPollers['data'] as $single) {
      $pollers[] = $single['text'];
    }
//debugger($rawListOfPollers);
//debugger($fullListOfPollers);
//debugger($pollers);
    foreach ($pollers as $poller) {
      // Find our checks for each poller
      $post = [ 'task' => 'findChecks'];
      $post += ['hostname' => $hostname ];
      $post += ['checkType' => $poller ];
      $rawListOfChecks = callApiPost("/graphite/test", $post, $headers);
      $listOfChecks = json_decode($rawListOfChecks['response'], true);
      // Drop our service check names into the array now
      foreach($listOfChecks['data'] as $singleCheck) {
        $graphiteList[$poller][] = $singleCheck['text'] ;
      }
    }
// debugger($rawListOfChecks);
// exit();
//  debugger($graphiteList); // list['nrpe'][0] = blah
  }

  /*
    Pulling graphs and rendering them is expensive.
    We are not pre-rendering on the main page, but only
    here when a user wants to actually SEE the graphs available
  */

  /*
    debugger($rrdTemplateName);   // pretty list of rrd templates defined for files
    debugger($_POST);             // all POST variables
    debugger($rawRrdGraphs);      // RRD file list
    debugger($rawActiveMonitors); // Base active monitor list with storage types
    debugger($activeMonitors);    // json_decoded active monitors
    debugger($storage);           // pretty list of storage types so we know what graph api calls to make
    debugger($rawRrdTemplates);   // Find the templates which can be used for a given file
    exit();
  */

  if ( $quitEarly == 0 ) {
  ?>
  <div class="container">
    <div class=" text-center mt-5 ">
      <h1>Available Graphs for <?php echo '<a href="/host/index.php?&page=deviceDetails.php&id=' . $id . '">' . $hostname . '</a>'; ?></h1><br>
    </div>

    <table class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;"><b>Available RRD Graphs</b>
      <tbody>
  <?php
      foreach ($rrdTemplateName as $rrdKey => $rrdValue) {
        $rrdPrettyName = preg_replace('/snmp/', 'SNMP', $rrdKey);
        $rrdPrettyName = preg_replace('/_/', ' ', $rrdPrettyName);
        // If we hit no_template, attempt to make a decent name from the filename
        if ($rrdKey == 'no_template') {
          $lastSlashPosition = strrpos($rrdValue, '/');
          if ($lastSlashPosition !== false) {
            $rrdPrettyName = substr($rrdValue, $lastSlashPosition + 1);
            $rrdPrettyName = preg_replace('/[",]/', '', $rrdPrettyName);
            $rrdPrettyName = preg_replace('/.rrd/', '', $rrdPrettyName);
          }
        }
        echo '<tr><td>';
          echo '<form id="rrd_' . $rrdKey . '" method="POST" action="/host/index.php?&page=/graphs/rrd.php">';
          echo '<input type="hidden" name="templateName" value="' . $rrdKey . '">';
          echo '<input type="hidden" name="hostname" value="' . $hostname . '">';
          echo '<input type="hidden" name="id" value="' . $id . '">';
          echo '<input type="hidden" name="files" value="' .  htmlspecialchars(json_encode($rrdTemplateName[$rrdKey],1)) . '">';
          echo '<button type="submit" form="rrd_' . $rrdKey . '" class="btn btn-link">' . $rrdPrettyName . '</button>';
          echo '</form>';
        echo '</td></tr>';
      }
   ?>
     </tbody>
   </table>
    <table class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;"><b>Available Graphite Graphs</b>
      <tbody>
  <?php
      foreach ($graphiteList as $graphitePollerName => $graphitePoller) {
        foreach ($graphitePoller as $grapiteKey => $graphiteValue) {
        echo '<tr><td>';
          echo '<form id="graphite_' . $graphiteKey . $graphiteValue . '" method="POST" action="/host/index.php?&page=/graphs/graphite.php">';
          echo '<input type="hidden" name="task" value="findGraphs">';
          echo '<input type="hidden" name="hostname" value="' . $hostname . '">';
          echo '<input type="hidden" name="checkType" value="' . $graphitePollerName . '">';
          echo '<input type="hidden" name="checkName" value="' . $graphiteValue . '">';
          echo '<input type="hidden" name="id" value="' . $id . '">';
          echo strtoupper($graphitePollerName) . ' <button type="submit" form="graphite_' . $graphiteKey . $graphiteValue . '" class="btn btn-link">' . $graphiteValue . '</button>';
          echo '</form>';
        echo '</td></tr>';
      }
    }
   ?>
     </tbody>
   </table>
    <table class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;"><b>Available CanvasJS Graphs From Graphite</b>
      <tbody>
  <?php
      foreach ($graphiteList as $graphitePollerName => $graphitePoller) {
        foreach ($graphitePoller as $grapiteKey => $graphiteValue) {
        echo '<tr><td>';
          echo '<form id="canvasjs_' . $graphiteKey . $graphiteValue . '" method="POST" action="/host/index.php?&page=/graphs/canvasJs.php">';
          echo '<input type="hidden" name="task" value="findGraphs">';
          echo '<input type="hidden" name="hostname" value="' . $hostname . '">';
          echo '<input type="hidden" name="checkType" value="' . $graphitePollerName . '">';
          echo '<input type="hidden" name="checkName" value="' . $graphiteValue . '">';
          echo '<input type="hidden" name="id" value="' . $id . '">';
          echo strtoupper($graphitePollerName) . ' <button type="submit" form="canvasjs_' . $graphiteKey . $graphiteValue . '" class="btn btn-link">' . $graphiteValue . '</button>';
          echo '</form>';
        echo '</td></tr>';
      }
    }
   ?>
     </tbody>
   </table>

  <?php
  }

  else {
    // Something went very wrong with the API call, but keep the layout clean...
    loadUnknown("Page load failed in an unusual way.  Please reload, or go back one page and try again.");
  }
?>
