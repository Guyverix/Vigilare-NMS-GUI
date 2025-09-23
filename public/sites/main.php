<?php
/* main.php

   This is going to be a secondary page as most users will go to the view page.
   This is used specifically to add new application groups

*/

/*
  The following is boilerplate that all pages should have
  Not having this set will cause API calls to not work
*/

require_once(__DIR__ . '/../../functions/generalFunctions.php');
checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff
checkTimer($_COOKIE);
$headers = ['Authorization: Bearer ' . $_COOKIE['token']];
// Load local vars for use (urls, ports, etc)
require_once __DIR__ . "/../../config/api.php";

/*
  PHP specific to this page
*/

$rawGroupsResp = callApiGet('/site/getAllHostnames', $headers);
$cleanGroupResp=json_decode($rawGroupsResp['response'], true);
//$groupResp = $cleanGroupResp['data']['result'];
$groupsResp = $cleanGroupResp['data'];
//debugger($groupResp['data']['result']);
$groups = is_array($groupsResp['result'] ?? null) ? $groupsResp['result'] : (is_array($groupsResp) ? $groupsResp : []);
//debugger($groupResp['result']);
// ---- handle POST actions ---------------------------------------------------
$notice = null;
$error  = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'create') {
        $groupName = trim((string)($_POST['groupName'] ?? ''));
        if ($groupName === '') {
            $error = 'Group name is required.';
        } else {
            $post = ['group' => $groupName];
            $res = callApiPost('/site/addGroupName', $post, $headers);
            if (is_array($res) && !empty($res['error'])) {
                $error = 'Failed to create group: ' . $res['error'];
            } else {
                $notice = 'Group "' . h($groupName) . '" created.';
                // refresh list
                $rawGroupsResp = callApiGet('/site/getAllHostnames', $headers);
                $cleanGroupResp=json_decode($rawGroupsResp['response'], true);
                $groupsResp = $cleanGroupResp['data'];
                $groups = is_array($groupsResp['result'] ?? null) ? $groupsResp['result'] : (is_array($groupsResp) ? $groupsResp : []);
            }
        }
    } elseif ($action === 'delete') {
        $groupName = trim((string)($_POST['groupName'] ?? ''));
        if ($groupName === '') {
            $error = 'Select a group to delete.';
        } else {
            $post = ['group' => $groupName];
            // Adjust to your API name as needed
            $res = callApiPost('/site/deleteGroupName', $post, $headers);
            if (is_array($res) && !empty($res['error'])) {
                $error = 'Failed to delete group: ' . $res['error'];
            } else {
                $notice = 'Group "' . h($groupName) . '" deleted.';
                // refresh list
                $rawGroupsResp = callApiGet('/site/getAllHostnames', $headers);
                $cleanGroupResp=json_decode($rawGroupsResp['response'], true);
                $groupsResp = $cleanGroupResp['data'];
                $groups = is_array($groupsResp['result'] ?? null) ? $groupsResp['result'] : (is_array($groupsResp) ? $groupsResp : []);
            }
        }
    }
}
echo "<!-- End of PHP logic.  Going to display -->";
?>

<div class="container-fluid px-4">
  <h2 class="my-3">Site Groups</h2>

  <?php if ($notice): ?>
    <div class="alert alert-success"><?= $notice ?></div>
  <?php endif; ?>
  <?php if ($error): ?>
    <div class="alert alert-danger"><?= h($error) ?></div>
  <?php endif; ?>

  <div class="row g-3">
    <!-- Create Group -->
    <div class="col-12 col-md-6 col-xl-4">
      <div class="card shadow-sm h-100">
        <div class="card-header fw-semibold">Add New Group</div>
        <div class="card-body">
          <form method="post" class="row gy-2">
            <input type="hidden" name="action" value="create">
            <div class="col-12">
              <label class="form-label mb-1" for="groupNameCreate">Group name</label>
              <input type="text" name="groupName" id="groupNameCreate" class="form-control" placeholder="e.g. web-tier" required>
            </div>
            <div class="col-auto">
              <button type="submit" class="btn btn-primary btn-sm">Create Group</button>
            </div>
          </form>
        </div>
        <div class="card-footer text-muted small">
          Creates an empty application group. You can add hosts on the groups page.
        </div>
      </div>
    </div>

    <!-- Delete Group -->
    <div class="col-12 col-md-6 col-xl-4">
      <div class="card shadow-sm h-100">
        <div class="card-header fw-semibold">Delete Group</div>
        <div class="card-body">
          <form method="post" class="row gy-2">
            <input type="hidden" name="action" value="delete">
            <div class="col-12">
              <label class="form-label mb-1" for="groupNameDelete">Select group</label>
              <select class="form-select" id="groupNameDelete" name="groupName" required>
                <option value="">Choose…</option>
                <?php foreach ($groups as $g): ?>
                  <?php
                    $name = $g['groupName'] ?? '';
                    // show count if present
                    $count = 0;
                    if (isset($g['deviceId'])) {
                        $csv = trim((string)$g['deviceId']);
                        if ($csv !== '') {
                            if (strlen($csv) >= 2 && $csv[0] === "'" && substr($csv, -1) === "'") {
                                $csv = substr($csv, 1, -1);
                            }
                            $count = $csv === '' ? 0 : count(explode(',', str_replace(' ', '', $csv)));
                        }
                    } elseif (isset($g['devices']) && is_array($g['devices'])) {
                        $count = count($g['devices']);
                    }
                  ?>
                  <option value="<?= h($name) ?>"><?= h($name) ?><?= $count ? ' ('.$count.' hosts)' : '' ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-auto">
              <button type="submit" class="btn btn-outline-danger btn-sm"
                      onclick="return confirm('Delete this group? This cannot be undone.');">
                Delete Group
              </button>
            </div>
          </form>
        </div>
        <div class="card-footer text-muted small">
          Deleting a group removes its membership mapping. Devices are not deleted.
        </div>
      </div>
    </div>


    <!-- View / Manage Existing Groups -->
    <div class="col-12 col-md-6 col-xl-4">
      <div class="card shadow-sm h-100">
        <div class="card-header fw-semibold">Manage Existing Groups</div>
        <div class="card-body d-flex flex-column justify-content-between">
          <p class="mb-3">Go to the page that lists all application groups and lets you add hosts to them.</p>
          <div>
            <!-- Adjust href to your existing page -->
            <a href="/sites/index.php?&page=siteDisplay.php" class="btn btn-secondary btn-sm">Open Groups Page</a>
          </div>
        </div>
        <div class="card-footer text-muted small">
          Shows each group with hostname links to device details.
        </div>
      </div>
    </div>
  </div>
</div>

