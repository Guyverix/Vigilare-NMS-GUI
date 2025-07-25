<div class="card mb-4">
  <div class="card-header bg-primary text-white">
    <h5 class="card-title">Device Information</h5>
  </div>
  <div class="card-body">
    <form id="change-details" method="POST">
      <div class="row g-3">
        <div class="col-md-2">
          <label for="id" class="form-label">Device ID</label>
          <input type="text" class="form-control" id="id" name="id" value="<?= htmlspecialchars($id) ?>" readonly>
        </div>
        <div class="col-md-4">
          <label for="hostname" class="form-label">Hostname</label>
          <input type="text" class="form-control" id="hostname" name="hostname" value="<?= htmlspecialchars($hostname) ?>">
        </div>
        <div class="col-md-3">
          <label for="address" class="form-label">IP Address</label>
          <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($address) ?>">
        </div>
        <div class="col-md-3">
          <label for="firstSeen" class="form-label">First Seen</label>
          <input type="text" class="form-control" id="firstSeen" name="firstSeen" value="<?= htmlspecialchars($firstSeen) ?>" readonly>
        </div>
      </div>

      <div class="row g-3 mt-3">
        <div class="col-md-4">
          <label for="productionState" class="form-label">Monitoring State</label>
          <select class="form-select" id="productionState" name="productionState">
            <option value="available" <?= $productionState === 'available' ? 'selected' : '' ?>>Available</option>
            <option value="disabled" <?= $productionState === 'disabled' ? 'selected' : '' ?>>Disabled</option>
          </select>
        </div>
        <div class="col-md-2">
          <label for="isAlive" class="form-label">Is Alive</label>
          <input type="text" class="form-control" id="isAlive" name="isAlive" value="<?= htmlspecialchars($isAlive) ?>" readonly>
        </div>
        <div class="col-md-6 d-flex align-items-end justify-content-end">
          <button type="submit" name="changeDevice" class="btn btn-warning">Save Device Changes</button>
        </div>
      </div>
    </form>
  </div>
</div>
