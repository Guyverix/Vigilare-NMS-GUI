<!-- Load the shared table functionality -->
<table id="historyTable" class="table table-bordered">
  <thead>
    <tr>
      <th class="sortable">Time</th>
      <th class="sortable">Severity</th>
      <th class="sortable">Summary</th>
    </tr>
  </thead>
  <tbody>
    <tr class="clickable-row" data-index="0">
      <td>2025-07-01 10:00</td>
      <td>3</td>
      <td>Disk usage high</td>
    </tr>
    <tr id="detail-0" class="detail-row" style="display:none">
      <td colspan="3">Detail for Disk usage high</td>
    </tr>
  </tbody>
</table>

<ul id="pagination-historyTable" class="pagination justify-content-center"></ul>

<script src="/js/tableSort/tableSort.js"></script>
<script>
  document.addEventListener("DOMContentLoaded", function () {
    enableTableEnhancements("historyTable", 5);
  });
</script>
