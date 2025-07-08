<div class="card mb-3">
  <div class="card-header bg-secondary text-white">
    Historical Events
  </div>
  <div class="card-body p-2">
    <table class="table table-sm mb-0" id="historyEventsTable">
      <thead>
        <tr>
          <th scope="col">Event</th>
          <th scope="col">Start</th>
          <th scope="col">End</th>
          <th scope="col">Severity</th>
          <th scope="col">Summary</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($sharedDevice['historyEvents']['data'])): ?>
          <?php foreach ($sharedDevice['historyEvents']['data'] as $event): ?>
            <?php
              $sev = (int)$event['eventSeverity'];
              switch ($sev) {
                case 0: $badge = 'bg-success'; $text = 'OK'; break;
                case 1: $badge = 'bg-secondary'; $text = 'Debug'; break;
                case 2: $badge = 'bg-info'; $text = 'Info'; break;
                case 3: $badge = 'bg-warning'; $text = 'Warning'; break;
                case 4: $badge = 'bg-orange'; $text = 'Major'; break;
                case 5: $badge = 'bg-danger'; $text = 'Critical'; break;
                default: $badge = 'bg-dark'; $text = 'Unknown'; break;
              }
            ?>
            <tr>
              <td><?= htmlspecialchars($event['eventName']) ?></td>
              <td><?= htmlspecialchars($event['startEvent']) ?></td>
              <td><?= ($event['endEvent'] !== '0000-00-00 00:00:00') ? htmlspecialchars($event['endEvent']) : 'Active' ?></td>
              <td><span class="badge <?= $badge ?>"><?= $text ?></span></td>
              <td><?= htmlspecialchars($event['eventSummary']) ?></td>
            </tr>
          <?php endforeach; ?>
        <?php else: ?>
          <tr><td colspan="5">No historical events found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
