<!-- Load the shared table functionality -->
<?php $historyEvents = $sharedDevice['historyEvents']['data']; ?>

<div class="card mb-3">
  <div class="card-header bg-secondary text-white">
    Historical Events
  </div>
<div class="container mt-4">
  <table id="historyTable" class="table table-bordered">
    <thead>
      <tr>
        <th class="sortable">Severity</th>
        <th class="sortable">Summary</th>
        <th class="sortable">End Time</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($historyEvents as $index => $event): ?>
        <tr class="clickable-row" data-index="<?= $index ?>" data-detail='<?= htmlspecialchars($event["eventRaw"], ENT_QUOTES, "UTF-8") ?>'>
          <td><?= htmlspecialchars($event["eventSeverity"]) ?></td>
          <td><?= htmlspecialchars($event["eventSummary"]) ?></td>
          <td><?= htmlspecialchars($event["endEvent"]) ?></td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  <ul id="pagination-historyTable" class="pagination justify-content-center"></ul>
</div>

<ul class="pagination justify-content-center" id="pagination-historyTable"></ul>
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
