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

// -----------------------------------------------------------------------------
// 1) SAMPLE DATA (replace with your real calls)
// -----------------------------------------------------------------------------
$summary = [
  'devices' => ['up' => 128, 'down' => 3, 'unreachable' => 1, 'total' => 132],
  'checks'  => ['ok' => 1842, 'warning' => 37, 'critical' => 6, 'unknown' => 5],
  'sla'     => ['availability_pct' => 99.92],
  'mtta'    => '12m',
  'mttr'    => '42m',
];

$alerts = [
  ['id' => 9012, 'time' => '2025-08-26 13:44', 'device' => 'db-prod-01', 'severity' => 5, 'summary' => 'MySQL replication behind by 900s', 'acked' => false],
  ['id' => 9011, 'time' => '2025-08-26 13:11', 'device' => 'edge-sw-03', 'severity' => 4, 'summary' => 'Interface Gi0/2 errors > 2%', 'acked' => true],
  ['id' => 9007, 'time' => '2025-08-26 12:58', 'device' => 'web-02',      'severity' => 4, 'summary' => 'HTTP 5xx rate > 1% (last 5m)', 'acked' => false],
  ['id' => 9002, 'time' => '2025-08-26 12:20', 'device' => 'vpn-gw',      'severity' => 3, 'summary' => 'Tunnel latency spike > 180ms', 'acked' => true],
];

$topResources = [
  ['device' => 'db-prod-01',  'metric' => 'CPU',         'value' => 92, 'unit' => '%'],
  ['device' => 'web-02',      'metric' => 'Memory',      'value' => 87, 'unit' => '%'],
  ['device' => 'edge-sw-03',  'metric' => 'Int Gi0/2',   'value' => 78, 'unit' => '% util'],
  ['device' => 'nas-backup1', 'metric' => 'Disk /data',  'value' => 73, 'unit' => '%'],
  ['device' => 'vpn-gw',      'metric' => 'Packet Loss', 'value' => 4.2, 'unit' => '%'],
];

$sites = [
  ['site' => 'SJC', 'status' => 'ok',       'up' => 42, 'down' => 0],
  ['site' => 'IAD', 'status' => 'warning',  'up' => 38, 'down' => 1],
  ['site' => 'FRA', 'status' => 'ok',       'up' => 29, 'down' => 0],
  ['site' => 'SIN', 'status' => 'critical', 'up' => 19, 'down' => 3],
  ['site' => 'SEA', 'status' => 'error',    'up' => 19, 'down' => 2],
];

$maintenance = [
  ['when' => '2025-08-27 02:00–03:00', 'scope' => 'db-prod-01', 'note' => 'MariaDB patch window'],
  ['when' => '2025-08-28 01:00–02:30', 'scope' => 'core-net',   'note' => 'Firmware upgrade (dist)'],
];

$tickets = [
  ['id' => 'INC-12455', 'sev' => 'P2', 'title' => 'Edge switch errors in SJC', 'status' => 'In Progress'],
  ['id' => 'INC-12441', 'sev' => 'P3', 'title' => 'Intermittent 5xx on web tier', 'status' => 'ACK'],
  ['id' => 'STOI-1542', 'sev' => 'P3', 'title' => 'Gardener Cluster crash', 'status' => 'ACK'],
];

// -----------------------------------------------------------------------------
// 2) HELPERS
// -----------------------------------------------------------------------------
function sevBadge(int $sev): string {
  $cls = match(true) {
    $sev >= 5 => 'bg-danger',
    $sev === 4 => 'bg-warning text-dark',
    $sev === 3 => 'bg-info text-dark',
    default   => 'bg-secondary',
  };
  return '<span class="badge '.$cls.'">S'.$sev.'</span>';
}

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
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Vigilare — Overview</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    :root {
      --surface-0: #0f1115;
      --surface-1: #151923;
      --surface-2: #1b2030;
      --border-1:  #232838;
      --text-strong:  #ffffff;
      --text-default: #eef2f8;
      --text-muted:   #cbd3e1;
    }
    body { background-color: var(--surface-0); color: var(--text-default); }
    .card { background-color: var(--surface-1); border: 1px solid var(--border-1); }
    .card-header { background: linear-gradient(180deg,var(--surface-2),#121622); border-bottom: 1px solid var(--border-1); }
    .stat-lg { font-size: 1.8rem; font-weight: 700; color: var(--text-strong); }
    .muted { color: var(--text-muted); }
    a, a:hover { color: #a4d0ff; }

    /* Tables: force dark backgrounds & bright text */
    .table {
      --bs-table-bg: var(--surface-1);
      --bs-table-color: var(--text-default);
      --bs-table-striped-bg: #1a1f2b;
      --bs-table-hover-bg: #1b2130;
      --bs-table-border-color: #2b3245;
    }
    .table thead th { background-color: var(--surface-2); color: #d8def0; }
    .table tbody td { color: var(--text-default); }

    /* List groups: no white background */
    .list-group-item { background-color: var(--surface-1); color: var(--text-default); border-color: var(--border-1); }

    /* Progress track darker for contrast */
    .progress { background-color: #21283a; }

    .badge { font-weight:600; }
  </style>
</head>
<body>
<div class="container-fluid py-3">
  <div class="d-flex align-items-baseline justify-content-between mb-3">
    <h1 class="h3 mb-0">Overview</h1>
    <div class="muted">Last updated: <?php echo safe(date('Y-m-d H:i')); ?></div>
  </div>

  <!-- ROW 1: Global Health & Totals -->
  <div class="row g-3">
    <div class="col-xl-3 col-md-6">
      <div class="card h-100">
        <div class="card-header text-white">Global Health</div>
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
        <div class="card-header text-white">Devices</div>
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
              <div class="muted">Unreachable</div>
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
        <div class="card-header text-white">Checks</div>
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
        <div class="card-header text-white">SLA & Ops</div>
        <div class="card-body">
          <div class="d-flex justify-content-between">
            <div>
              <div class="muted">Availability</div>
              <div class="stat-lg text-info"><?php echo number_format((float)$summary['sla']['availability_pct'], 2); ?>%</div>
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
          <div class="mt-2 small"><a href="/sla/index.php">View SLA reports →</a></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ROW 2: Alarms + Performance Snapshot -->
  <div class="row g-3 mt-1">
    <div class="col-xl-7">
      <div class="card h-100">
        <div class="card-header text-white d-flex justify-content-between align-items-center">
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
                  <td><a href="/host/view.php?name=<?php echo urlencode($a['device']); ?>"><?php echo safe($a['device']); ?></a></td>
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
        <div class="card-header text-white d-flex justify-content-between align-items-center">
          <span>Performance Hotspots</span>
          <a class="small" href="/performance/index.php">View all →</a>
        </div>
        <div class="card-body">
          <?php if (empty($topResources)): ?>
            <div class="muted">No high utilization right now.</div>
          <?php else: foreach ($topResources as $r): ?>
            <div class="mb-3">
              <div class="d-flex justify-content-between small">
                <div><a href="/host/view.php?name=<?php echo urlencode($r['device']); ?>"><?php echo safe($r['device']); ?></a> · <?php echo safe($r['metric']); ?></div>
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
        <div class="card-header text-white d-flex justify-content-between align-items-center">
          <span>Sites Status (compact)</span>
          <a class="small" href="/sites/index.php">Sites view →</a>
        </div>
        <div class="card-body">
          <div class="row g-2">
            <?php foreach ($sites as $s): 
              $stateCls = match($s['status']) {
                'ok' => 'bg-success', 'warning' => 'bg-warning text-dark', 'critical' => 'bg-danger', 'error' => 'bg-orange', 'default' => 'bg-secondary' 
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
        <div class="card-header text-white d-flex justify-content-between align-items-center">
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
        <div class="card-header text-white d-flex justify-content-between align-items-center">
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
