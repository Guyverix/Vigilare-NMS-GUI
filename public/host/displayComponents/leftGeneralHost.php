<div class="card border-primary rounded-3">
  <div class="card-header bg-primary text-white">
    Host Overview
  </div>
  <div class="card-body">

    <!-- OS Image and Name -->
    <?php
      // debugger($sharedDevice['properties']['data'][0]);
      $hostOs = $sharedDevice['properties']['data'][0]['hostOs'] ?? 'Unknown OS';
      $osImage = $sharedDevice['properties']['data'][0]['osImg'] ?? null;
    ?>
    <div class="d-flex align-items-center mb-3">
    <?php if (!empty($osImage)): ?>
      <img src="<?= htmlspecialchars($osImage) ?>" 
           alt="<?= htmlspecialchars($hostOs) ?> Logo"
           class="me-2"
           style="height: 32px; width: auto;">
    <?php endif; ?>
    <span class="fw-semibold"><?= htmlspecialchars($hostOs) ?></span>
  </div>
    <!-- Original Device Details -->
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

      <dt class="col-5">SNMP</dt>
      <dd class="col-7">
        <?php
          $rawProperties = json_decode($sharedDevice['properties']['data'][0]['properties'] ?? '{}', true);
          $snmpEnabled = $rawProperties['snmpEnable'] ?? "false";
          if ($snmpEnabled === 'true') {
            echo '<span class="badge bg-success">Enabled</span>';
          } else {
            echo '<span class="badge bg-secondary">Not Enabled</span>';
          }
        ?>
      </dd>
    </dl>
  </div>
</div>
