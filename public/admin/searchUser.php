<?php
require_once(__DIR__ . '/../../functions/generalFunctions.php');
require_once __DIR__ . "/../../config/api.php";

$headers = ['Authorization: Bearer ' . $_COOKIE['token']];
$post = [];

if (isset($_POST['action'])) {
  $post = $_POST;

  switch ($_POST['action']) {
    case "delete":
      $alterUser = callApiPost("/admin/delete", $post, $headers);
      break;
    case "enable":
      $alterUser = callApiPost("/admin/activate", $post, $headers);
      break;
    case "disable":
      $alterUser = callApiPost("/admin/deactivate", $post, $headers);
      break;
    default:
      loadUnknown("Bad action call. Please play again.");
      break;
  }

  $rawAlterUser = json_decode($alterUser['response'], true);
  $responseCodeAlterUser = $rawAlterUser['statusCode'];
  $post = [];

  echo "<br><br><br>";
  if (preg_match('/FAILURE/', $rawAlterUser['data'][0]) || ($responseCodeAlterUser !== 200 && $responseCodeAlterUser !== 403)) {
    decideResponse($responseCodeAlterUser, $rawAlterUser['data'][0]);
  } elseif ($responseCodeAlterUser == 403) {
    load403Warn("Expired access credentials");
  } else {
    successMessage('User ' . $_POST['action'] . ' is successful.');
  }
}

$rawUsersList = callApiPost("/admin/findUsersAll", $post, $headers);
if (!is_array($rawUsersList)) {
  $rawUsersList = json_decode($rawUsersList['response'], true);
}
$usersList = json_decode($rawUsersList['response'], true);
$users = $usersList['data'] ?? [];

$responseCode = $usersList['statusCode'] ?? 500;
$quitEarly = 0;

switch ($responseCode) {
  case 418:
    echo "<br><br><br>";
    load418("Additional access required. Contact an admin");
    $quitEarly = 2;
    break;
  case 403:
    echo "<br><br><br>";
    load4XX();
    $quitEarly = 1;
    break;
  case 200:
    break;
  default:
    echo "<br><br><br>";
    decideResponse($responseCode);
    $quitEarly = 1;
    break;
}

if ($quitEarly === 0) {
?>
<br><br>
<h1 class="text-center my-4">Search All Users</h1>
<div class="container-lg">
  <div class="table-responsive">
    <table id="dt-userList" class="table table-hover align-middle text-center text-nowrap">
      <thead class="thead-light">
        <tr>
          <th>User ID</th>
          <th>Real Name</th>
          <th>Email</th>
          <th>Status</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $user): ?>
          <tr>
            <td><?= htmlspecialchars($user['userId']) ?></td>
            <td><?= htmlspecialchars($user['realName']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td>
              <?php if ($user['enable'] == 1): ?>
                <span class="badge bg-success">Enabled</span>
              <?php else: ?>
                <span class="badge bg-warning text-dark">Disabled</span>
              <?php endif; ?>
            </td>
            <td>
              <?php
              $uid = htmlspecialchars($user['id']);
              $fields = [
                'id' => $uid,
                'userId' => $user['userId'],
                'realName' => $user['realName'],
                'email' => $user['email'],
                'accessList' => $user['accessList'],
                'enable' => $user['enable'],
                'timer' => $user['timer']
              ];

              // Change form
              echo '<form id="changeUser' . $uid . '" method="POST" action="/admin/index.php?page=changeUser.php">';
              foreach ($fields as $name => $val)
                echo '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($val) . '">';
              echo '<input type="hidden" name="action" value="change">';
              echo '</form>';

              // Delete, Enable, Disable, Reset forms
              foreach (['delete', 'enable', 'disable', 'resetPassword'] as $action) {
                $formAction = ($action === 'resetPassword') ? '/admin/index.php?page=resetPassword.php' : '';
                echo '<form id="' . $action . $uid . '" method="POST" action="' . $formAction . '">';
                if ($action === 'enable' || $action === 'disable') {
                  echo '<input type="hidden" name="username" value="' . htmlspecialchars($user['userId']) . '">';
                } else {
                  echo '<input type="hidden" name="id" value="' . $uid . '">';
                }
                if ($action === 'resetPassword') {
                  foreach ($fields as $name => $val)
                    echo '<input type="hidden" name="' . $name . '" value="' . htmlspecialchars($val) . '">';
                }
                echo '<input type="hidden" name="action" value="' . $action . '">';
                echo '</form>';
              }
              ?>
              <div class="d-grid gap-2 d-md-flex justify-content-center">
                <button form="changeUser<?= $uid ?>" class="btn btn-info btn-sm">
                  <i class="fas fa-user-edit"></i> Edit
                </button>
                <?php if ($user['enable']): ?>
                  <button form="disable<?= $uid ?>" class="btn btn-warning btn-sm">
                    <i class="fas fa-user-slash"></i> Disable
                  </button>
                <?php else: ?>
                  <button form="enable<?= $uid ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-user-check"></i> Enable
                  </button>
                <?php endif; ?>
                <button form="delete<?= $uid ?>" class="btn btn-danger btn-sm">
                  <i class="fas fa-trash-alt"></i> Delete
                </button>
                <button form="resetPassword<?= $uid ?>" class="btn btn-secondary btn-sm">
                  <i class="fas fa-key"></i> Reset Password
                </button>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<script src="/js/simple-datatables/simple-datatables.js"></script>
<script>
  window.addEventListener("DOMContentLoaded", () => {
    const table = document.querySelector("#dt-userList");
    if (table) {
      new simpleDatatables.DataTable(table, {
        searchable: true,
        sortable: true,
        storable: false,
        paging: true,
        perPage: 25,
        perPageSelect: [25, 50, 100, 200],
        labels: {
          placeholder: "Search users"
        }
      });
    }
  });
</script>

<?php
}
elseif ($quitEarly === 2) {
  echo "<br>";
}
else {
  loadUnknown("API calls failed in an unexpected way. Please reload.");
}
?>
