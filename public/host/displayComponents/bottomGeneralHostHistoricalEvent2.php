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
            switch ($severity) {
              case 0: $rowClass = 'bg-success text-white'; $sevLabel = 'OK'; break;
              case 1: $rowClass = 'bg-secondary text-white'; $sevLabel = 'DEBUG'; break;
              case 2: $rowClass = 'bg-info text-white'; $sevLabel = 'INFO'; break;
              case 3: $rowClass = 'bg-warning text-dark'; $sevLabel = 'WARNING'; break;
              case 4: $rowClass = 'bg-danger text-white'; $sevLabel = 'MAJOR'; break;
              case 5: $rowClass = 'bg-danger text-white'; $sevLabel = 'CRITICAL'; break;
              default: $rowClass = 'bg-dark text-white'; $sevLabel = 'UNKNOWN'; break;
            }
            $summary = htmlspecialchars($event['eventSummary'] ?? '');
            if (strlen($summary) > 80) {
              $summary = substr($summary, 0, 77) . '...';
            }
          ?>
          <li class="list-group-item <?= $rowClass ?>">
            <div>
              <strong><?= htmlspecialchars($event['eventName']) ?></strong>
              <small class="ms-2"><?= htmlspecialchars($event['eventAddress']) ?></small><br>
              <small>started <?= htmlspecialchars($event['startEvent']) ?></small><br>
              <small>ended <?= htmlspecialchars($event['endEvent']) ?></small><br>
              <span><?= $summary ?></span>
            </div>
            <div class="float-end fw-bold">
              <?= $sevLabel ?>
            </div>
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
