<?php
/**
 * Vigilare — Landing Dashboard (Bootstrap 5, improved contrast on dark theme)
 *
 * Changes in this revision:
 *  - Brighter text for all data cells and numbers
 *  - Tables and list groups no longer inherit white backgrounds
 *  - Higher-contrast headers and progress tracks
 *  - Removed emoji in empty states per preference
 */

  require_once(__DIR__ . '/../functions/generalFunctions.php');
  //checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../config/api.php";

  // Grab our POSSIBLE values so users can choose what they change
  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
  $post = array();  // We are using post, so give it an empty array to post with
  $quitEarly = 0;

  // So much work to try to make the clock work anywhere...  sigh
  if (isset($_COOKIE['clientTimezone'])) {
    $localTime = $_COOKIE['clientTimezone'];
  }
  // Session should alredy be set.  This is for testing..
  if (! isset($_SESSION)) {
    session_start();
  }
  if (! isset($localTime) && isset($_SESSION['time'])) {
    $localTime = $_SESSION['time'];
  }
  else {
   if (empty($localTime)) {
     // default to UTC 0 as that SHOULD be the default
     $localTime = "GMT 0";
   }
  }
  $raw = explode( ' ', $localTime);
  $offset = $raw[1];
  $localOffset = ($offset * 3600);
  $localTime2 = (strtotime("now") + $localOffset);
  $timeNow = date('Y-m-d H:i:s',$localTime2);

// Use device/view to get counts and states of the host
$rawDeviceList = callApiPost("/device/view", $headers);

// Extract device list safely
$deviceList = extractEventsList($rawDeviceList);  // same helper we wrote earlier

// Initialize counters
$summaryDevice = [
    'devices' => [
        'up'          => 0,
        'down'        => 0,
        'unreachable' => 0,
        'total'       => 0,
    ]
];

// Count based on isAlive and prudctionState is 0 "active"
foreach ($deviceList as $d) {
    $state           = strtolower($d['isAlive'] ?? 'unknown');
    $productionState = (int)($d['productionState'] ?? -1);

    // Always increment total
    $summaryDevice['devices']['total']++;

    // Only increment detailed counts if productionState = 0
    if ($productionState === 0) {
        switch ($state) {
            case 'alive':
                $summaryDevice['devices']['up']++;
                break;
            case 'dead':
                $summaryDevice['devices']['down']++;
                break;
            case 'unknown':
            default:
                $summaryDevice['devices']['unreachable']++;
                break;
        }
    }
}

// get MTTR mean-time-to-repair
// Default is now -1 days
// Long term this should be run by housekeeping and stored
// in the database for retrieval in 1, 7, 30, 90 day intervals?
$mttrStart = date('Y-m-d H:i:s', strtotime('-1 days'));
$post = ['startEvent' => "$mttrStart" ];
$rawMttr = callApiPost("/reporting/mttr", $post, $headers);
$mttrValue = json_decode($rawMttr['response'], true);
$mttr= $mttrValue['data'][0]['avg_active_minutes_in_window'];


// -----------------------------------------------------------------------------
// 1) SAMPLE DATA (replace with your real calls)
// -----------------------------------------------------------------------------
// sla defined by aliveness
// mtta is for ack
// mttr is how long alarm was active as an average
$summary = [
  'checks'  => ['ok' => 1842, 'warning' => 37, 'critical' => 6, 'unknown' => 5],
  'mtta'    => '0m N/A',
];
$summary['devices'] = $summaryDevice['devices'];
$summary['mttr'] = $mttr .'m';


// Calculate the availibility percentage based on devices up / down + unknown
$avail_pct_inclusive = calcAvailability($summary);
$avail_pct_excl      = calcAvailability($summary, 'exclude_unknown');  // e.g., 99.621
$summary['sla']['availability_pct'] = $avail_pct_inclusive;

//Fetch active events and convert to lightweight alerts
$rawActiveEvents = callApiGet("/events/view/eventCounter/DESC/order", $headers);

// 2) Extract the real list of events from a variety of shapes
$eventList = extractEventsList($rawActiveEvents);

// 3) Map to the lightweight dashboard format ($alerts)
$alerts = array_map(function ($e) {
    return [
        'id'       => (string)($e['id'] ?? 0),
        'evid'       => (string)($e['evid'] ?? 0),
        'time'     => formatEventTime($e['startEvent'] ?? $e['stateChange'] ?? $e['firstSeen'] ?? ''), // 'YYYY-MM-DD HH:MM'
        'device'   => $e['device'] ?? $e['hostname'] ?? 'unknown',
        'severity' => (int)($e['eventSeverity'] ?? 0),
        'summary'  => $e['eventSummary'] ?? ($e['eventName'] ?? 'Event'),
        // If you have a real ack flag, use it here. Defaulting to false.
        'acked'    => isset($e['acked']) ? filter_var($e['acked'], FILTER_VALIDATE_BOOLEAN) : false,
    ];
}, $eventList);

//debugger($eventList);
//exit();
// hotspot data pull from API
$window="-1 days";
$post = ['window' => date('Y-m-d H:i:s', strtotime("$window")) ];
$rawHotSpots = callApiPost('/events/findHotSpot', $post, $headers);
$filterHotSpots = json_decode($rawHotSpots['response'], true);
$filterHotSpots['window'] = $window ;

$topResources = array_map(function ($h) use ($window) {
  return [
      'id'     => (int)($h['id'] ?? 0),
      'device' => (string)($h['device'] ?? "unknown device"),
      'metric' => (string)($h['eventName'] ?? "unknown metric"),
      'value'  => (int)($h['repeats_24h'] ?? "?"),
      'unit'   => (string)(" in $window"),
  ];
}, $filterHotSpots['data']);


/*
//debugger($topResources);
//debugger($filterHotSpots);
//exit();
// MOCK DATA hotspot
$topResources = [
  ['device' => 'db-prod-01',  'metric' => 'CPU',         'value' => 92, 'unit' => '%'],
  ['device' => 'web-02',      'metric' => 'Memory',      'value' => 87, 'unit' => '%'],
  ['device' => 'edge-sw-03',  'metric' => 'Int Gi0/2',   'value' => 78, 'unit' => '% util'],
  ['device' => 'nas-backup1', 'metric' => 'Disk /data',  'value' => 73, 'unit' => '%'],
  ['device' => 'vpn-gw',      'metric' => 'Packet Loss', 'value' => 4.2, 'unit' => '%'],
];
*/

// Application grouping showing up down on dashboard
$rawAppGroupList = callApiGet("/events/findAppGroupDown", $headers);
$appGroupList = json_decode($rawAppGroupList['response'], true);
$sites = array_map(function ($s) {
  $down = (int)($s['down_devices'] ?? 0);
  $labels = ['ok', 'warning', 'critical'];
  return [
     'site' => ($s['groupName'] ?? "unknown group"),
     'status' => ($labels[min(max($down, 0), 2)]),
     'up' => ($s['up_devices'] ?? 0 ),
     'down' => ($s['down_devices'] ?? 0 ),
  ];
}, $appGroupList['data']);
/*
// MOCK DATA
$sitesMock = [
  ['site' => 'PLEX', 'status' => 'ok',       'up' => 42, 'down' => 0],
  ['site' => 'NAS', 'status' => 'warning',  'up' => 38, 'down' => 1],
  ['site' => 'JELLYFIN', 'status' => 'ok',       'up' => 29, 'down' => 0],
  ['site' => 'SONARR', 'status' => 'critical', 'up' => 19, 'down' => 2],
];
*/

// Setting API calls for Maintenance
$rawMaintenanceList = callApiGet("/maintenance/findAllMaintenance", $headers);
$maintenanceList = json_decode($rawMaintenanceList['response'], true);
$maintenance = array_map(function ($m) {
  return [
    'when' => ($m['startTime'] ?? '1972-06-22 07:11:00'),
    'scope' => ("Groups: ". $m['groups'] ." Applications: " . $m['application'] . " Devices: " . $m['device']),
    'note' => ($m['summary']),
  ];
}, $maintenanceList['data']);

/*
// MOCK DATA MAINTENANCE
$maintenance2 = [
  ['when' => '2025-08-27 02:00–03:00', 'scope' => 'db-prod-01', 'note' => 'MariaDB patch window'],
  ['when' => '2025-08-28 01:00–02:30', 'scope' => 'core-net',   'note' => 'Firmware upgrade (dist)'],
];
*/







$tickets = [
  ['id' => 'INC-12455', 'sev' => 'P2', 'title' => 'Edge switch errors in SJC', 'status' => 'In Progress'],
  ['id' => 'INC-12441', 'sev' => 'P3', 'title' => 'Intermittent 5xx on web tier', 'status' => 'ACK'],
];

// -----------------------------------------------------------------------------
// 2) HELPERS
// -----------------------------------------------------------------------------
function calcAvailability(array $summary, string $mode = 'inclusive'): ?float {
    $d = $summary['devices'] ?? [];
    $up       = (int)($d['up'] ?? 0);
    $down     = (int)($d['down'] ?? 0);
    // accept either 'unknown' or 'unreachable'
    $unknown  = (int)($d['unknown'] ?? ($d['unreachable'] ?? 0));

    if ($mode === 'exclude_unknown') {
        $den = $up + $down;
        return $den > 0 ? round(($up / $den) * 100, 3) : null;
    }

    // inclusive (unknown treated as down)
    $total = $up + $down + $unknown;
    return $total > 0 ? round(($up / $total) * 100, 3) : null;
}

function sevBadge(int $sev): string {
  $cls = match(true) {
    $sev >= 5 => 'bg-danger',
    $sev === 4 => 'bg-warning text-dark',
    $sev === 3 => 'bg-primary',
    $sev === 2 => 'bg-info text-dark',
    $sev === 1 => 'bg-secondary text-dark',
    default   => 'bg-secondary',
  };
  return '<span class="badge '.$cls.'">S-'.$sev.'</span>';
}

// below threshold sets color
function pctBarReverse(int|float $value, string $label = ''): string {
  $val = max(0, min(100, (float)$value));
  $cls = $val <= 90 ? 'bg-danger' : ($val <= 75 ? 'bg-warning text-dark' : 'bg-success');
  $label = $label !== '' ? htmlspecialchars($label) : $val.'%';
  return '<div class="progress" role="progressbar" aria-valuenow="'.$val.'" aria-valuemin="0" aria-valuemax="100">'
       .   '<div class="progress-bar '.$cls.'" style="width: '.$val.'%">'.$label.'</div>'
       . '</div>';
}

// above threshold sets color
function pctBar(int|float $value, string $label = ''): string {
  $val = max(0, min(100, (float)$value));
  $cls = $val >= 90 ? 'bg-danger' : ($val >= 75 ? 'bg-warning text-dark' : 'bg-success');
  $label = $label !== '' ? htmlspecialchars($label) : $val.'%';
  return '<div class="progress" role="progressbar" aria-valuenow="'.$val.'" aria-valuemin="0" aria-valuemax="100">'
       .   '<div class="progress-bar '.$cls.'" style="width: '.$val.'%">'.$label.'</div>'
       . '</div>';
}

function safe($s): string { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

// Compute quick totals for severity bands
$sevTotals = [
  'critical' => $summary['checks']['critical'] ?? 0,
  'warning'  => $summary['checks']['warning']  ?? 0,
  'ok'       => $summary['checks']['ok']       ?? 0,
  'unknown'  => $summary['checks']['unknown']  ?? 0,
];
$sevSum = array_sum($sevTotals) ?: 1; // avoid divide-by-zero

?>
<!doctype html>
<html lang="en">
<head>
  <!-- Refresh here does not flash screen and simply makes sense -->
  <META HTTP-EQUIV=Refresh CONTENT="30">
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vigilare — Overview</title>
  <link id="bootstrap-css" rel="stylesheet" href="/css/bootstrap/bootstrap.min.css">
  <link id="theme-css" rel="stylesheet" href="/css/dark/vigilare-dashboard.css">

</head>
<body>
<div class="container-fluid py-3">
  <div class="d-flex align-items-baseline justify-content-between mb-3">
    <h1 class="h3 mb-0">Overview</h1>
    <div class="muted">Last updated: <?php echo $timeNow; ?></div>
  </div>

  <!-- ROW 1: Global Health & Totals -->
  <div class="row g-3">
    <div class="col-xl-3 col-md-6">
      <div class="card h-100">
        <div class="card-header">Global Health</div>
        <div class="card-body">
          <div class="d-flex gap-3 align-items-center">
            <?php
              $down = (int)($summary['devices']['down'] ?? 0) + (int)($summary['devices']['unreachable'] ?? 0);
              $total = (int)($summary['devices']['total'] ?? 0) ?: 1;
              $healthPct = 100 - round(($down / $total) * 100);
              echo pctBar($healthPct, $healthPct.'% healthy');
            ?>
          </div>
          <div class="mt-3 small muted">Based on devices down/unreachable.</div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card h-100">
        <div class="card-header ">Devices</div>
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <div class="muted">Up</div>
              <div class="stat-lg text-success"><?php echo (int)$summary['devices']['up']; ?></div>
            </div>
            <div>
              <div class="muted">Down</div>
              <div class="stat-lg text-danger"><?php echo (int)$summary['devices']['down']; ?></div>
            </div>
            <div>
              <div class="muted">Unknown</div>
              <div class="stat-lg text-warning"><?php echo (int)$summary['devices']['unreachable']; ?></div>
            </div>
            <div>
              <div class="muted">Total</div>
              <div class="stat-lg"><?php echo (int)$summary['devices']['total']; ?></div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card h-100">
        <div class="card-header ">Checks</div>
        <div class="card-body">
          <div class="row g-2 text-center">
            <div class="col-3"><div class="muted">OK</div><div class="stat-lg text-success"><?php echo (int)$summary['checks']['ok']; ?></div></div>
            <div class="col-3"><div class="muted">Warn</div><div class="stat-lg text-warning"><?php echo (int)$summary['checks']['warning']; ?></div></div>
            <div class="col-3"><div class="muted">Crit</div><div class="stat-lg text-danger"><?php echo (int)$summary['checks']['critical']; ?></div></div>
            <div class="col-3"><div class="muted">Unk</div><div class="stat-lg"><?php echo (int)$summary['checks']['unknown']; ?></div></div>
          </div>
          <div class="mt-3">
            <?php
              $critPct = round(($sevTotals['critical']/$sevSum)*100);
              $warnPct = round(($sevTotals['warning']/$sevSum)*100);
              $okPct   = round(($sevTotals['ok']/$sevSum)*100);
              $unkPct  = 100 - $critPct - $warnPct - $okPct;
            ?>
            <div class="progress" style="height: 10px;">
              <div class="progress-bar bg-danger" style="width: <?php echo $critPct; ?>%"></div>
              <div class="progress-bar bg-warning text-dark" style="width: <?php echo $warnPct; ?>%"></div>
              <div class="progress-bar bg-success" style="width: <?php echo $okPct; ?>%"></div>
              <div class="progress-bar bg-secondary" style="width: <?php echo $unkPct; ?>%"></div>
            </div>
            <div class="small muted mt-2">Critical/Warning/OK/Unknown mix</div>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3 col-md-6">
      <div class="card h-100">
        <div class="card-header ">SLA & Ops</div>
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <div class="muted">Availability</div>
              <div class="stat-lg text-warning"><?php echo number_format((float)$summary['sla']['availability_pct'], 2); ?>%</div>
            </div>
            <div>
              <div class="muted">MTTA</div>
              <div class="stat-lg"><?php echo safe($summary['mtta']); ?></div>
            </div>
            <div>
              <div class="muted">MTTR</div>
              <div class="stat-lg"><?php echo safe($summary['mttr']); ?></div>
            </div>
          </div>
          <div class="mt-2 small"><a href="/reporting/index.php">View SLA reports →</a></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ROW 2: Alarms + Performance Snapshot -->
  <div class="row g-3 mt-1">
    <div class="col-xl-7">
      <div class="card h-100">
        <div class="card-header  d-flex justify-content-between align-items-center">
          <span>Top Active Alerts</span>
          <a class="small" href="/event/index.php?&page=event.php">All events →</a>
        </div>
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-hover table-sm align-middle mb-0">
              <thead>
                <tr>
                  <th class="text-secondary">Time</th>
                  <th class="text-secondary">Device</th>
                  <th class="text-secondary">Sev</th>
                  <th class="text-secondary">Summary</th>
                  <th class="text-secondary">Ack</th>
                </tr>
              </thead>
              <tbody>
              <?php if (empty($alerts)): ?>
                <tr><td colspan="5" class="text-center muted py-4">No active alerts</td></tr>
              <?php else: foreach ($alerts as $a): ?>
                <tr>
                  <td class="text-nowrap"><?php echo safe($a['time']); ?></td>
                  <td><a href="/host/index.php?&page=deviceDetails.php&id=<?php echo urlencode($a['id']); ?>"><?php echo safe($a['device']); ?></a></td>
                  <td><?php echo sevBadge((int)$a['severity']); ?></td>
                  <td><?php echo safe($a['summary']); ?></td>
                  <td><?php echo $a['acked'] ? '<span class="badge bg-secondary">ACK</span>' : '<span class="badge bg-primary">New</span>'; ?></td>
                </tr>
              <?php endforeach; endif; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-5">
      <div class="card h-100">
        <div class="card-header  d-flex justify-content-between align-items-center">
          <span>Performance Hotspots</span>
          <a class="small" href="/reporting/index.php">View all →</a>
        </div>
        <div class="card-body">
          <?php if (empty($topResources)): ?>
            <div class="muted">No high utilization right now.</div>
          <?php else: foreach ($topResources as $r): ?>
            <div class="mb-3">
              <div class="d-flex justify-content-between small">
                <div><a href="host/index.php?page=deviceDetails.php&id=<?php echo urlencode($r['id']); ?>"><?php echo safe($r['device']); ?></a> · <?php echo safe($r['metric']); ?></div>
                <div><?php echo safe($r['value'].' '.$r['unit']); ?></div>
              </div>
              <?php echo pctBar((float)$r['value']); ?>
            </div>
          <?php endforeach; endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- ROW 3: Sites Map (compact) + Ops Panels -->
  <div class="row g-3 mt-1">
    <div class="col-xl-6">
      <div class="card h-100">
        <div class="card-header  d-flex justify-content-between align-items-center">
          <span>Sites Status (compact)</span>
          <a class="small" href="/sites/index.php">Sites view →</a>
        </div>
        <div class="card-body">
          <div class="row g-2">
            <?php foreach ($sites as $s): 
              $stateCls = match($s['status']) {
                'ok' => 'bg-success', 'warning' => 'bg-warning text-dark', 'critical' => 'bg-danger', default => 'bg-secondary'
              };
            ?>
              <div class="col-6 col-md-4">
                <div class="p-2 rounded border" style="border-color:var(--border-1)">
                  <div class="d-flex justify-content-between align-items-center mb-1">
                    <div class="fw-semibold" style="color:var(--text-strong)"><?php echo safe($s['site']); ?></div>
                    <span class="badge <?php echo $stateCls; ?> text-uppercase"><?php echo safe($s['status']); ?></span>
                  </div>
                  <div class="small muted">Up: <?php echo (int)$s['up']; ?> · Down: <span class="text-danger"><?php echo (int)$s['down']; ?></span></div>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>

    <div class="col-xl-3">
      <div class="card h-100">
        <div class="card-header  d-flex justify-content-between align-items-center">
          <span>Upcoming Maintenance</span>
          <a class="small" href="/maintenance/index.php">All →</a>
        </div>
        <div class="card-body">
          <?php if (empty($maintenance)): ?>
            <div class="muted">No scheduled maintenance.</div>
          <?php else: ?>
            <ul class="list-unstyled mb-0">
              <?php foreach ($maintenance as $m): ?>
              <li class="mb-2">
                <div class="fw-semibold" style="color:var(--text-strong)"><?php echo safe($m['when']); ?></div>
                <div class="small">Scope: <a href="/host/view.php?name=<?php echo urlencode($m['scope']); ?>"><?php echo safe($m['scope']); ?></a></div>
                <div class="small muted"><?php echo safe($m['note']); ?></div>
              </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <div class="col-xl-3">
      <div class="card h-100">
        <div class="card-header  d-flex justify-content-between align-items-center">
          <span>Tickets</span>
          <a class="small" href="/tickets/index.php">Open tickets →</a>
        </div>
        <div class="card-body">
          <?php if (empty($tickets)): ?>
            <div class="muted">No active tickets.</div>
          <?php else: ?>
            <ul class="list-group list-group-flush">
              <?php foreach ($tickets as $t): ?>
              <li class="list-group-item">
                <div class="d-flex justify-content-between">
                  <a href="/tickets/view.php?id=<?php echo urlencode($t['id']); ?>" class="fw-semibold" style="color:var(--text-strong)"><?php echo safe($t['id']); ?></a>
                  <span class="badge <?php echo $t['sev']==='P1'?'bg-danger':($t['sev']==='P2'?'bg-warning text-dark':'bg-secondary'); ?>"><?php echo safe($t['sev']); ?></span>
                </div>
                <div class="small" style="color:var(--text-default)"><?php echo safe($t['title']); ?></div>
                <div class="small muted">Status: <?php echo safe($t['status']); ?></div>
              </li>
              <?php endforeach; ?>
            </ul>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

  <!-- FOOTER LINKS -->
  <div class="mt-4 small muted">
    <a href="/settings/preferences.php">Dashboard preferences</a> ·
    <a href="/about.php">About Vigilare</a>
  </div>
</div>

<!-- Optional Bootstrap JS (no extra libs required) -->
<script src="/js/bootstrap-5/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>  -->
</body>
</html>
