<div class="card">
  <div class="card-header bg-primary text-white">
    <h5 class="mb-0">Device Properties</h5>
  </div>
  <div class="card-body p-0">
    <form id="change-properties" action="" method="POST" class="m-0 p-0">
      <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
      <input type="hidden" name="hostname" value="<?= htmlspecialchars($hostname) ?>">

      <table class="table table-striped table-hover mb-0">
        <thead class="table-light">
          <tr>
            <th class="text-center">Delete</th>
            <th>Key</th>
            <th>Value</th>
            <th class="text-end">
              <button type="submit" class="btn btn-warning btn-sm me-2" name="rediscover" form="rediscovery">
                Rediscover
              </button>
              <button type="submit" class="btn btn-primary btn-sm" form="change-properties">
                Save Changes
              </button>
            </th>
          </tr>
        </thead>
        <tbody>
          <!-- New Entry Row -->
          <tr>
            <td class="text-center text-muted">New:</td>
            <td><input type="text" name="new_key" class="form-control form-control-sm"></td>
            <td colspan="2"><input type="text" name="new_value" class="form-control form-control-sm"></td>
          </tr>

          <!-- Existing Properties -->
          <?php foreach ($properties as $key => $value): ?>
            <?php
              if (is_array($value)) {
                $value = json_encode($value, JSON_PRETTY_PRINT);
              }
            ?>
            <tr>
              <td class="text-center">
                <button type="submit" name="remove_key" value="<?= htmlspecialchars($key) ?>" class="btn btn-danger btn-sm">
                  Remove
                </button>
              </td>
              <td>
                <label for="<?= htmlspecialchars($key) ?>" class="form-label"><?= htmlspecialchars($key) ?></label>
              </td>
              <td colspan="2">
                <input type="text" id="<?= htmlspecialchars($key) ?>" name="<?= htmlspecialchars($key) ?>"
                  value="<?= htmlspecialchars($value) ?>" class="form-control form-control-sm">
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </form>

    <!-- Rediscover Form (separate so it can be triggered independently) -->
    <form id="rediscovery" method="POST" class="d-none">
      <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
      <input type="hidden" name="hostname" value="<?= htmlspecialchars($hostname) ?>">
    </form>
  </div>
</div>
