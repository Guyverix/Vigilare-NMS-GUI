<?php
// eventData.php - Returns just the table rows for AJAX refresh

require_once(__DIR__ . '/../../functions/generalFunctions.php');
require_once __DIR__ . "/../../config/api.php";
include __DIR__ . "/functions/eventFunctions.php";

checkCookie($_COOKIE);

$headers = ['Authorization: Bearer ' . $_COOKIE['token']];
$cookieTimezone = $_COOKIE['clientTimezone'] ?? 'UTC +0';
$displayAck = $_COOKIE['showEventAck'] ?? 'true';
$displaySeverity = isset($_COOKIE['showEventSeverity'])
  ? explode(',', $_COOKIE['showEventSeverity'])
  : ["0", "1", "2", "3", "4", "5"];

$post = [];
$rawActiveEvents = callApiGet("/events/view/eventSeverity/DESC/order", $post, $headers);
$eventList = json_decode($rawActiveEvents['response'], true)['data'] ?? [];

$cookieTimezone = explode(' ', $cookieTimezone);
$localOffset = ((int)($cookieTimezone[1] ?? 0) * 3600);

foreach ($eventList as $events) {
  if (!in_array($events['eventSeverity'], $displaySeverity)) continue;

  switch ($events['eventSeverity']) {
    case "0": $rowColor = ' class="table-success"'; break;
    case "1": $rowColor = ' class="table-secondary"'; break;
    case "2": $rowColor = ' class="table-primary"'; break;
    case "3": $rowColor = ' class="table-info"'; break;
    case "4": $rowColor = ' class="table-warning"'; break;
    case "5": $rowColor = ' class="table-danger"'; break;
    default:  $rowColor = ''; break;
  }

  echo '<tr' . $rowColor . '>';

  // Device link
  if (empty($events['id'])) {
    echo '<td><center><a href="/host/index.php?&page=createDevice.php&hostname=' . $events['device'] . '&address=' . $events['eventAddress'] . '" target="_blank">' . $events['device'] . '</a></center></td>';
  } else {
    echo '<td><center><a href="/host/index.php?&page=deviceDetails.php&id=' . $events['id'] . '">' . $events['device'] . '</a></center></td>';
  }

  // Monitor name button
  echo '<td><form id="details' . $events['evid'] . '" method="POST">';
  foreach ($events as $k => $v) {
    $v = in_array($k, ['eventRaw','eventDetails']) ? htmlspecialchars(json_encode($v)) : $v;
    echo '<input type="hidden" name="' . $k . '" value="' . $v . '">';
  }
  echo '<button type="submit" class="btn btn-sm btn-link" name="displayDetails" form="details' . $events['evid'] . '">' . $events['eventName'] . '</button>';
  echo '</form></td>';

  echo '<td>' . $events['eventSummary'] . '</td>';

  $startLocal = date('Y-m-d H:i:s', strtotime($events['startEvent'] . ' UTC') + $localOffset);
  $stateLocal = date('Y-m-d H:i:s', strtotime($events['stateChange'] . ' UTC') + $localOffset);

  echo '<td>' . $startLocal . '</td>';
  echo '<td>' . $stateLocal . '</td>';
  echo '<td><center>' . $events['eventCounter'] . '</center></td>';
  echo '<td><center>' . $events['eventSeverity'] . '</center></td>';

  // Action buttons
  echo '<td><table><tr><td>';
  echo '<form id="moveToHistory' . $events['evid'] . '" method="POST">';
  echo '<input type="hidden" name="event" value="' . htmlspecialchars(json_encode($events)) . '">';
  echo '<input type="hidden" name="evid" value="' . $events['evid'] . '">';
  echo '<input type="hidden" name="hostname" value="' . $events['hostname'] . '">';
  echo '<button type="submit" class="btn btn-sm btn-outline-primary" name="moveToHistory"><i class="fas fa-plane"></i></button>';
  echo '</form>';

  echo '</td><td><form id="ackEvent' . $events['evid'] . '" method="POST">';
  echo '<input type="hidden" name="event" value="' . htmlspecialchars(json_encode($events)) . '">';
  echo '<button type="submit" class="btn btn-sm btn-outline-primary" name="ackEvent"><i class="fas fa-check-circle"></i></button>';
  echo '</form>';

  echo '</td><td><form id="ticketEvent' . $events['evid'] . '" method="POST">';
  echo '<input type="hidden" name="event" value="' . htmlspecialchars(json_encode($events)) . '">';
  echo '<button type="submit" class="btn btn-sm btn-outline-primary" name="ticketEvent"><i class="fas fa-suitcase"></i></button>';
  echo '</form>';

  echo '</td></tr></table></td>';
  echo '</tr>';
}
