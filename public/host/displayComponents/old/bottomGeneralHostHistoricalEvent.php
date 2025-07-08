<?php
debugger($sharedDevice['historyEvents']);
?>
<div class="card border-secondary rounded-3 mt-3">
  <div class="card-header bg-secondary text-white">
    Historical Events
  </div>
  <div class="card-body p-0">
    <?php if (!empty($sharedDevice['historyEvents']['data']) && is_array($sharedDevice['historyEvents']['data'])): ?>
      <ul class="list-group list-group-flush">
        <?php foreach ($sharedDevice['historyEvents']['data'] as $event): ?>
          <?php
            $severity = (int)$event['eventSeverity'];
            // color-code severity
            switch ($severity) {
              case 0: $badgeClass = 'bg-success'; $sevLabel = 'OK'; break;
              case 1: $badgeClass = 'bg-secondary'; $sevLabel = 'DEBUG'; break;
              case 2: $badgeClass = 'bg-info'; $sevLabel = 'INFO'; break;
              case 3: $badgeClass = 'bg-warning text-dark'; $sevLabel = 'WARNING'; break;
              case 4: $badgeClass = 'bg-danger'; $sevLabel = 'MAJOR'; break;
              case 5: $badgeClass = 'bg-danger'; $sevLabel = 'CRITICAL'; break;
              default: $badgeClass = 'bg-dark'; $sevLabel = 'UNKNOWN'; break;
            }
            $summary = htmlspecialchars($event['eventSummary'] ?? '');
            if (strlen($summary) > 80) {
              $summary = substr($summary, 0, 77) . '...';
            }
          ?>
          <li class="list-group-item d-flex justify-content-between align-items-center">
            <div>
              <strong><?= htmlspecialchars($event['eventName']) ?></strong>
              <small class="text-muted ms-2"><?= htmlspecialchars($event['eventAddress']) ?></small><br>
              <small class="text-muted">started <?= htmlspecialchars($event['startEvent']) ?></small><br>
              <small class="text-muted">ended <?= htmlspecialchars($event['endEvent']) ?></small><br>
              <span class="text-body"><?= $summary ?></span>
            </div>
            <span class="badge <?= $badgeClass ?>">
              <?= $sevLabel ?>
            </span>
          </li>
        <?php endforeach; ?>
      </ul>
    <?php else: ?>
      <div class="p-3 text-muted">
        No historical events found for this host.
      </div>
    <?php endif; ?>
  </div>
</div>
