<div class="card mb-4">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0">All Available Monitors</h5>
  </div>
  <div class="card-body p-0">
    <table id="dt-allMonitor" class="table table-striped table-hover mb-0">
      <thead class="table-light">
        <tr>
          <th>Check Name</th>
          <th>Type</th>
          <th>Storage</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($availableMonitors as $monitor): ?>
          <tr>
            <td><?= htmlspecialchars($monitor['checkName']) ?></td>
            <td><?= htmlspecialchars($monitor['type']) ?></td>
            <td><?= htmlspecialchars($monitor['storage']) ?></td>
            <td>
              <form method="POST" action="">
                <input type="hidden" name="monitorId" value="<?= htmlspecialchars($monitor['id']) ?>">
                <input type="hidden" name="hostId" value="<?= htmlspecialchars($id) ?>">
                <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
                <input type="hidden" name="hostname" value="<?= htmlspecialchars($hostname) ?>">
                <button type="submit" name="addMonitor" class="btn btn-success btn-sm">
                  Add
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
