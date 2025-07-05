<?php
debugger($sharedDevice);
?>

<div class="card border-secondary rounded-3">
  <div class="card-header bg-secondary text-white">
    Host Monitors
  </div>
  <div class="card-body p-0">
    <?php if (!empty($sharedDevice['monitors']['data']) && is_array($sharedDevice['monitors']['data'])): ?>
      <ul class="list-group list-group-flush">
        <?php foreach ($sharedDevice['monitors']['data'] as $monitor): ?>
          <li class="list-group-item">
            <strong><?= htmlspecialchars($monitor['checkName']) ?></strong><br>
            <small>Type: <?= htmlspecialchars($monitor['type']) ?></small><br>
            <small>Storage: <?= htmlspecialchars($monitor['storage']) ?></small>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <div class="p-3 text-muted">
        No monitors defined for this host.
      </div>
    <?php endif; ?>
  </div>
</div>

