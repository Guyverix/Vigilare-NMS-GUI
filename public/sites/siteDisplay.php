<?php
 /**
 * Vigilare — main.php page for Site Group Display
 *
 */

  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  //checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";

  // Grab our POSSIBLE values so users can choose what they change
  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];
  $post = array();  // We are using post, so give it an empty array to post with
  $quitEarly = 0;

  // So much work to try to make the clock work anywhere...  sigh
  if (isset($_COOKIE['clientTimezone'])) {
    $localTime = $_COOKIE['clientTimezone'];
  }
  // Session should alredy be set.  This is for testing..
  if (! isset($_SESSION)) {
    session_start();
  }
  if (! isset($localTime) && isset($_SESSION['time'])) {
    $localTime = $_SESSION['time'];
  }
  else {
   if (empty($localTime)) {
     // default to UTC 0 as that SHOULD be the default
     $localTime = "GMT 0";
   }
  }
  $raw = explode( ' ', $localTime);
  $offset = $raw[1];
  $localOffset = ($offset * 3600);
  $localTime2 = (strtotime("now") + $localOffset);
  $timeNow = date('Y-m-d H:i:s',$localTime2);

// --- Fetch data (single API call you already have) -------------------------
//$resp = callApiGet('/applicationGroup/view'); // or your actual endpoint
$rawResp = callApiGet("/site/getAllHostnamesJson", $headers); // or your actual endpoint
$resp = json_decode($rawResp['response'], true);
$groups = is_array($resp['data']['result'] ?? null) ? $resp['data']['result'] : [];
//debugger($groups);
//exit();
// --- Handle add-to-group POST (IDs as CSV from a text input) ---------------
$notice = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'add-to-group') {
    $_POST['action'] = "expired";  // Try for refreshes to not clobber stuff
    $groupName = $_POST['groupName'] ?? '';
    $csvIds    = $_POST['deviceIdsCsv'] ?? '';
    $ids       = csv_to_array($csvIds); // normalize, de-dup next
    $ids       = array_values(array_unique($ids));
    $idsA      = $ids;
    $ids       = implode(',', $ids);

    if ($groupName !== '' && $ids) {
        $post = ['group' => $groupName, 'id' => $ids];
        $rawRes = callApiPost("/site/addHostname", $post, $headers);
        $res = json_decode($rawRes['response'], true);
        if (is_array($res) && !empty($res['error'])) {
          $notice = 'Failed to add devices: ' . $res['error'];
        }
        else {
          $notice = 'Added '.count($idsA).' host(s) to '.$groupName;
        }
// debugger($notice);
        // refresh groups so UI reflects change
        $rawResp = callApiGet("/site/getAllHostnamesJson", $headers);
        $resp = json_decode($rawResp['response'], true);
        $groups = is_array($resp['data']['result'] ?? null) ? $resp['data']['result'] : [];
    }
}
?>
<div class="container-fluid px-4">
  <h2 class="my-3">Application Groups</h2>

  <?php if ($notice): ?>
    <div class="alert alert-info alert-dismissible fade show" role="alert"><?= h($notice) ?> <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>
  <?php endif; ?>

  <div class="row g-3">
    <?php foreach ($groups as $g): ?>
      <?php
        $groupName = $g['groupName'] ?? '(unnamed)';
        $id_and_hostname = json_decode($g['id_to_hostname_json'], true);
        $pairs = [];
        foreach($id_and_hostname as $k => $v) {
          $pairs[] = [
            'id'       => $k       ?? null,
            'hostname' => $v ?? '(unknown)',
          ];
        }
      ?>
      <div class="col-12 col-md-6 col-xl-3">
        <div class="card shadow-sm h-100">
          <div class="card-header d-flex justify-content-between align-items-center">
            <span class="fw-semibold"><?= h($groupName) ?></span>
            <span class="badge bg-secondary"><?= count($pairs) ?> hosts</span>
          </div>

          <div class="card-body">
            <?php if ($pairs): ?>
              <ul class="list-group list-group-flush">
                <?php foreach ($pairs as $row): ?>
                  <li class="list-group-item d-flex justify-content-between align-items-center">
                    <?php if ($row['id'] !== null): ?>
                      <a class="text-decoration-none" href="<?= h(device_details_url($row['id'])) ?>">
                        <?= h($row['hostname']) ?>
                      </a>
                      <span class="text-muted small">#<?= h($row['id']) ?></span>
                    <?php else: ?>
                      <span><?= h($row['hostname']) ?></span>
                    <?php endif; ?>
                  </li>
                <?php endforeach; ?>
              </ul>
            <?php else: ?>
              <div class="text-decoration-none">No hosts in this group yet.</div>
            <?php endif; ?>
          </div>

          <div class="card-footer">
            <form method="post" class="row gy-2 gx-2 align-items-end">
              <input type="hidden" name="action" value="add-to-group">
              <input type="hidden" name="groupName" value="<?= h($groupName) ?>">
              <div class="col-12">
                <label class="form-label mb-1">Add host IDs to <?= h($groupName) ?></label>
                <input type="text" name="deviceIdsCsv" class="form-control"
                       placeholder="e.g. 13,40,88">
                <div class="form-text">
                  Enter one or more device IDs, comma-separated. Duplicates/spaces are handled.
                </div>
              </div>
              <div class="col-auto">
                <button type="submit" class="btn btn-primary btn-sm">Add</button>
              </div>
            </form>
          </div>

        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
