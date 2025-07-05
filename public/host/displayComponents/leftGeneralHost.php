<?php
//debugger($sharedDevice);
//exit;
?>

<div class="card border-primary rounded-3">
  <div class="card-header bg-primary text-white">
    Host Overview
  </div>
  <div class="card-body">
    <dl class="row">
      <dt class="col-5">Hostname</dt>
      <dd class="col-7"><?= htmlspecialchars($sharedDevice['properties']['data'][0]['hostname'] ?? 'Unknown') ?></dd>

      <dt class="col-5">IP Address</dt>
      <dd class="col-7"><?= htmlspecialchars($sharedDevice['properties']['data'][0]['address'] ?? 'N/A') ?></dd>

      <dt class="col-5">First Seen</dt>
      <dd class="col-7"><?= htmlspecialchars($sharedDevice['properties']['data'][0]['firstSeen'] ?? 'N/A') ?></dd>

      <dt class="col-5">Monitoring</dt>
      <dd class="col-7">
        <?php
          $productionState = $sharedDevice['properties']['data'][0]['productionState'] ?? 1;
          if ((int)$productionState === 0) {
            echo '<span class="badge bg-success">Monitored</span>';
          } else {
            echo '<span class="badge bg-secondary">Not Monitored</span>';
          }
        ?>
    </dd>
    </dl>
  </div>
</div>
