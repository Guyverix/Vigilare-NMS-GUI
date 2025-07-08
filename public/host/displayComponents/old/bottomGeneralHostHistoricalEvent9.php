<!-- Load the shared table functionality -->
<script src="/js/tableSort/tableSort.js"></script>



<div class="card mb-3">
  <div class="card-header bg-secondary text-white">
    Historical Events
  </div>
  <div class="card-body p-2">
<table id="historyTable2" class="table table-striped table-bordered table-hover">
  <thead class="table-dark">
    <tr>
      <th class="sortable">Event Name</th>
      <th class="sortable">Severity</th>
      <th class="sortable">Start</th>
      <th class="sortable">End</th>
      <th class="sortable">Summary</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($sharedDevice['historyEvents']['data'] as $index => $event): ?>
      <?php
        $severity = (int) $event['eventSeverity'];
        $badgeClass = match ($severity) {
          5 => 'bg-danger text-white',
          4 => 'bg-orange text-white',
          3 => 'bg-warning text-dark',
          2 => 'bg-info text-dark',
          1 => 'bg-secondary text-white',
          0 => 'bg-success text-white',
          default => 'bg-dark text-white',
        };
      ?>
      <tr class="clickable-row <?= $badgeClass ?>" data-index="<?= $index ?>" style="cursor: pointer;">
        <td><?= htmlspecialchars($event['eventName']) ?></td>
        <td><?= htmlspecialchars($event['eventSeverity']) ?></td>
        <td><?= htmlspecialchars($event['startEvent']) ?></td>
        <td><?= htmlspecialchars($event['endEvent']) ?></td>
        <td><?= htmlspecialchars($event['eventSummary']) ?></td>
      </tr>
      <tr class="detail-row" id="detail-<?= $index ?>" style="display: none;">
        <td colspan="5" class="bg-light">
          <strong>Details:</strong><br>
          <pre><?= htmlspecialchars($event['eventDetails']) ?></pre>
          <strong>Raw Event JSON:</strong><br>
          <pre><?= htmlspecialchars($event['eventRaw']) ?></pre>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<ul class="pagination justify-content-center" id="pagination-historyTable2"></ul>
  </div>
</div>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    enableTableEnhancements("historyTable2", 10);
  });
</script>
