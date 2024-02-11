<?php
/*
  These are function calls specific to hosts that are outside
  of the default ones that all pages can use.
*/

function hostOs($rawDevicePerformance = null) {
  if (is_null($rawDevicePerformance)) {
    $rawDevicePerformance['data'] = array();
  }
  foreach ($rawDevicePerformance['data'] as $devicePerformance) {
    if ($devicePerformance['checkName'] == 'hostOs') {
      return $devicePerformance['value'];
    }
  }
  return "Unknown";
}

// Return the image associated with the OS
function osImages($hostOses) {
  switch ($hostOses) {
    case strpos( $hostOses, 'brcarm' ) !== false:             // leave for now, but mips !== arm in OS return data always
      $osImg="/images/misc/wifi.png";                         // find a better solution for figuring out wifi?
      break;
    case strpos($hostOses, 'armv7l' ) !== false:
      $osImg="/images/misc/cpu-ARM-icon.png";                 // find a better solution for figuring out wifi?
      break;
    case strpos($hostOses, 'armv6l' ) !== false:
      $osImg="/images/misc/cpu-ARM-icon.png";                 // find a better solution for figuring out wifi?
      break;
    case strpos($hostOses, 'Dell' ) !== false:
      $osImg="/images/misc/dell.png";                      // Dell is not an OS type but they sure try to make it look like one
      break;
    case strpos($hostOses, 'Ubuntu' ) !== false:
      $osImg="/images/linux/ubuntu.png";
      break;
    case strpos($hostOses, 'Red Hat' ) !== false:
      $osImg="/images/linux/redhat.png";
       break;
    case strpos($hostOses, '.el7.x86_64' ) !== false:
      $osImg="/images/linux/redhat.png";
       break;
    case strpos($hostOses, 'Mint' ) !== false:
      $osImg="/images/linux/mint.png";
      break;
    case strpos($hostOses, 'Debian' ) !== false:
      $osImg="/images/linux/debian.png";
      break;
    case strpos($hostOses, 'Windows Version 6' ) !== false:
      $osImg="/images/windows/windows10.jpg";
      break;
    case strpos($hostOses, 'Windows Version Blah' ) !== false:
      $osImg="/images/windows/windows10.jpg";
      break;
    case strpos($hostOses, 'PopOS' ) !== false:
      $osImg="/images/linux/popos.png";
      break;
    case strpos($hostOses, 'Arch' ) !== false:
      $osImg="/images/linux/arch.png";
      break;
    case strpos($hostOses, 'ReadyNAS' ) !== false:
      $osImg="/images/tux/linux-tux-3-logo-svg-vector.svg";
      break;
    case strpos($hostOses, 'MyCloudPR' ) !== false:
      $osImg="/images/tux/linux-tux-3-logo-svg-vector.svg";
      break;
    case strpos($hostOses, 'Linux' ) !== false:               // Last gasp try to see if this is some kind of Linux
      $osImg="/images/tux/linux-tux-3-logo-svg-vector.svg";   // This may end up being default for Linux
      break;
    case strpos($hostOses, 'mips' ) !== false:
      $osImg="/images/misc/wifi.png";                         // find a better solution for figuring out wifi?
      break;
    default:
      $osImg="/images/unsorted/question_mark.png";
      break;
  }
  return $osImg;
}

/*
  Get the accordion function "standardized" and pray this works
  ID for this is defined as $title_Head
*/
function configureAccordion( $title ) {
  $AccOne="<div class='container mt-2'>\n";
  $AccTwo="<div class=\"accordion\" id=\"" . $title . "_Head\">\n";
  $AccThree="<div class='accordion-item'>\n";
  $AccFour="<h2 class='accordion-header' id=\"" . $title . "\">\n";
  $AccHead=$AccOne . $AccTwo . $AccThree . $AccFour;
  $AccButton="<button class=\"accordion-button\" type=\"button\" data-bs-toggle=\"collapse\" data-bs-target=\"#div" . $title . "\" aria-expanded=\"true\" aria-controls=\"div" . $title . "\">" . preg_replace('/_/', ' ', $title) . "</button>\n";
  $AccCollapse='</h2>' . "\n" . '<div id="div' . $title . '" class="accordion-collapse collapse" aria-labelledby="' . $title . '" data-bs-parent="#' . $title . '_Head">' . "\n" . '<div class="accordion-body">' . "\n";
  $AccTitleHeaders = $AccHead . $AccButton . $AccCollapse ;
  return "$AccTitleHeaders";
}

// For those DUH moments in ones life
function includeLoaded() {
  echo "LOADED INCLUDE FILE\n";
}

// grab the supported templates for rendering stuff
function retrieveTemplate($classId, $nameId) {
  global $apiHttp;
  global $apiHostname;
  global $apiPort;
  $url=$apiHttp . $apiHostname . ':' . $apiPort . '/discovery/search';
  $type='post';
  $options=['Class' => $classId, 'Name' => $nameId];
  $result = curlCall($url,$type,$options);
  return $result;
}

// Convert int in rrd to a string value
function snmpHrStorageType( $intFromOid ) {
  switch ($intFromOid) {
    case "1":
      return "hrStorageOther";
      break;
    case "2":
      return "hrStorageRam";
      break;
    case "3":
      return "hrStorageVirtualMemory";
      break;
    case "4":
      return "hrStorageFixedDisk";
      break;
    case "5":
      return "hrStorageRemovableDisk";
      break;
    case "6":
      return "hrStorageFloppyDisk";
      break;
    case "7":
      return "hrStorageCompactDisc";
      break;
    case "8":
      return "hrStorageRamDisk";
      break;
    case "9":
      return "hrStorageFlashMemory";
      break;
    case "10":
      return "hrStorageNetworkDisk";
      break;
    default:
      return "borked";
      break;
  }
}

// Retrun if we can set monitors against this host
function isMonitorable($guess = null) {
  $monitorable = "unknown";
  if (is_null($guess)) {
    $guess = 4;  // should only ever be 0 or 1
  }
  switch ($guess) {
    case "1":
      $monitorable = 'no';
      break;
    case "0":
      $monitorable = 'yes';
      break;
    default:
      break;
  }
  return $monitorable;
}

function alarmCount($rawActiveEvents = null) {
  if (is_null($rawActiveEvents)) {
    $rawActiveEvents['data'] = array();
  }
  // Count the number of each event severity here
  $sev[0]['count'] = 0; $sev[0]['color'] = 'success';
  $sev[1]['count'] = 0; $sev[1]['color'] = 'secondary';
  $sev[2]['count'] = 0; $sev[2]['color'] = 'info';
  $sev[3]['count'] = 0; $sev[3]['color'] = 'primary';
  $sev[4]['count'] = 0; $sev[4]['color'] = 'warning';
  $sev[5]['count'] = 0; $sev[5]['color'] = 'danger';
  foreach ( $rawActiveEvents['data'] as $activeEvents) {
    switch ($activeEvents['eventSeverity']) {
    case "5":
      $sev[5]['count']++;
      break;
    case "4":
      $sev[4]['count']++;
      break;
    case "3":
      $sev[3]['count']++;
      break;
    case "2":
      $sev[2]['count']++;
      break;
    case "1":
      $sev[1]['count']++;
      break;
    case "0":
      $sev[0]['count']++;
      break;
    }
  }
  return $sev;
}

function hrSystem($rawDevicePerformance = null) {
  if (is_null($rawDevicePerformance)) {
    $rawDevicePerformance['data'] = array();
  }
  foreach ($rawDevicePerformance['data'] as $devicePerformance) {
    if ($devicePerformance['checkName'] == 'hrSystem') {
      $hrSystem = json_decode($devicePerformance['value'], true);
      foreach ($hrSystem as $k => $v) {
        $filter = substr(strrchr($k, '.'),1);
        $deviceInformation[] = [$filter => "$v"];
      }
    }
  }
  return $deviceInformation;
}

function hrSystemDate($rawDevicePerformance = null) {
  if (is_null($rawDevicePerformance)) {
    $rawDevicePerformance['data'] = array();
  }
  foreach ($rawDevicePerformance['data'] as $devicePerformance) {
    if ($devicePerformance['checkName'] == 'hrSystem') {
      return $devicePerformance['date'];
    }
  }
  return "none available";
}

// String format = '2013-7-26 17:01:10'
function getElapsedMinutes($dateString) {
    $givenDate = new DateTime($dateString);
    $currentDate = new DateTime("now");

    // Calculate the difference
    $interval = $currentDate->diff($givenDate);

    // Convert the difference to minutes
    $minutes = $interval->days * 24 * 60;       // Days converted to minutes
    $minutes += $interval->h * 60;              // Hours converted to minutes
    $minutes += $interval->i;                   // Minutes

    return $minutes;
}

// We only need a delta and a complete value to return a percent value
function calcPercentage($delta, $datestamp) {
  $complete = getElapsedMinutes($datestamp);
  $percent = (((($delta * 100) / $complete) - 100)* -1);
  // echo "Percent uptime " . $percent . "% \n";
  $clean = round($percent, 3);
  // echo "Percent Rounded " . $clean . "% \n";
  return $clean;
}
?>
