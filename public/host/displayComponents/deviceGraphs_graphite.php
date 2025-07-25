<?php
// deviceGraphs_graphite.php
$graphiteList=$activeMonitors;
// Only keep entries where storage === 'graphite'
$graphiteList = array_filter($graphiteList, function ($entry) {
    return isset($entry['storage']) && $entry['storage'] === 'graphite';
});

// debugger($graphiteList);
?>

<div class="container mt-5">
  <div class="card mb-4">
    <div class="card-header bg-primary text-white">
      <h5 class="mb-0">Graphite Graphs</h5>
    </div>
    <div class="card-body">
      <?php if (!empty($graphiteList) && is_array($graphiteList)): ?>
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th>Check Type</th>
              <th>Check Name</th>
              <th>View Graph</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($graphiteList as $check): ?>
              <?php
                $checkName = $check['checkName'] ?? 'unknown';
                $checkTypeRaw = $check['type'] ?? 'unknown';
                $checkType = in_array($checkTypeRaw, ['get', 'walk', 'snmp']) ? 'snmp' : $checkTypeRaw;
              ?>
              <tr>
                <td><?= htmlspecialchars(strtoupper($checkType)) ?></td>
                <td><?= htmlspecialchars($checkName) ?></td>
                <td>
                  <form method="POST" action="/host/index.php?page=/graphs/graphite.php" class="mb-0">
                    <input type="hidden" name="task" value="findGraphs">
                    <input type="hidden" name="hostname" value="<?= htmlspecialchars($hostname) ?>">
                    <input type="hidden" name="specialHostname" value="<?= htmlspecialchars($specialHostname) ?>">
                    <input type="hidden" name="checkType" value="<?= htmlspecialchars($checkType) ?>">
                    <input type="hidden" name="checkName" value="<?= htmlspecialchars($checkName) ?>">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                    <button type="submit" class="btn btn-primary btn-sm">View</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      <?php else: ?>
        <p class="text-muted">No Graphite-based graphs available for this host.</p>
      <?php endif; ?>
    </div>
  </div>
</div>
