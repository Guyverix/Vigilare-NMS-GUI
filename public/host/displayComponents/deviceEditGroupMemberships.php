<div class="card mb-4">
  <div class="card-header bg-primary text-white">
    <h5 class="card-title">Device Group Memberships</h5>
  </div>
  <div class="card-body p-0">
    <table class="table table-hover table-bordered mb-0">
      <thead class="table-light">
        <tr>
          <th>Device Group</th>
          <th>Service Checks</th>
          <th>Membership Action</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($groupList as $group): 
          $groupName = htmlspecialchars($group['devicegroupName']);
          $inGroup = in_array($group['devicegroupName'], $deviceMemberList);
        ?>
          <tr>
            <!-- Group Name -->
            <td><?= $groupName ?></td>

            <!-- Show Monitors Form -->
            <td>
              <form method="POST" action="">
                <?php include 'displayComponents/deviceEditHiddenInputs.php'; ?>
                <input type="hidden" name="deviceGroupMonitors" value="<?= $groupName ?>">
                <button type="submit" name="find-serviceCheck" class="btn btn-outline-success btn-sm">
                  Show Monitors
                </button>
              </form>
            </td>

            <!-- Membership Toggle Form -->
            <td>
              <form method="POST" action="">
                <?php include 'displayComponents/deviceEditHiddenInputs.php'; ?>
                <input type="hidden" name="deviceGroup" value="<?= $groupName ?>">
                <input type="hidden" name="change" value="<?= $inGroup ? 'remove' : 'add' ?>">
                <button type="submit" name="changeDeviceGroup" class="btn btn-sm <?= $inGroup ? 'btn-warning' : 'btn-success' ?>">
                  <?= $inGroup ? 'Remove Membership' : 'Add Membership' ?>
                </button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
