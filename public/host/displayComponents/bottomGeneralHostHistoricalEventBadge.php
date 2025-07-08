<?php $historyEvents = $sharedDevice['historyEvents']['data']; ?>

<div class="card mb-3">
  <div class="card-header bg-secondary text-white">
    Historical Events
  </div>
<div class="container mt-4">
  <table id="historyTable" class="table table-bordered">
    <thead>
      <tr>
        <th>Check Name</th>
        <th class="sortable">Severity</th>
        <th class="sortable">Summary</th>
        <th class="sortable">End Time</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($historyEvents as $index => $event): ?>
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
      // Suspect API bug clobbered the summary when it went to history
      // pull from raw so there is something at least
      if (empty($event['eventSummary'])) {
        $tmp=json_decode($event["eventRaw"], true);
        $event['eventSummary'] = $tmp['eventSummary'];
      }
      echo "<!-- Severity " . $severity . " evid " . $event['evid'] . " endEvent " . $event['endEvent'] . "-->";
      ?>
        <tr class="clickable-row" data-index="<?= $index ?>" data-detail='<?= htmlspecialchars($event["eventRaw"], ENT_QUOTES, "UTF-8") ?>'>
          <td><?= htmlspecialchars($event["eventName"]) ?></td>
<?php
  $severityLabel = "Unknown";
  $badgeClass = "bg-dark";
  switch ($severity) {
    case 5: $severityLabel = "Critical"; $badgeClass = "bg-danger"; break;
    case 4: $severityLabel = "Major";    $badgeClass = "bg-orange"; break;
    case 3: $severityLabel = "Warning";  $badgeClass = "bg-warning text-dark"; break;
    case 2: $severityLabel = "Info";     $badgeClass = "bg-info text-dark"; break;
    case 1: $severityLabel = "Minor";    $badgeClass = "bg-secondary"; break;
    case 0: $severityLabel = "OK";       $badgeClass = "bg-success"; break;
  }
?>
<td data-severity="<?= $severity ?>">
  <span class="badge <?= $badgeClass ?>">
    <?= $severity ?>
  </span>
</td>

          <td><?= htmlspecialchars($event["eventSummary"]) ?></td>
          <td><?= htmlspecialchars($event["endEvent"]) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <ul id="pagination-historyTable" class="pagination justify-content-center"></ul>
</div>
</div>

<!-- Modal -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-labelledby="detailModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="detailModalLabel">Event Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <div id="detailModalContent" class="container"></div>
      </div>
    </div>
  </div>
</div>




<script src="/js/tableSort/tableSort.js"></script>

<script>
  document.addEventListener("DOMContentLoaded", function () {
    enableTableEnhancements("historyTable", 10);
  });
</script>
