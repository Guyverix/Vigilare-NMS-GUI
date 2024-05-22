<?php
  /*
    We are going to search through a list for a specfic user which
    is going to allow us to change, delete, or disable the user.

    Keep in mind this API path requires ADMIN privs, and will always
    be discrete from user controls for their personal accounts.

    I guarentee this will be an attack vector, so it is going to be
    very sensitive and if things are not 100% correct, it is going to
    start to cry, as well as log EVERYTHING possible from the attempt
    to change or do "things".
  */

  require_once(__DIR__ . '/../../functions/generalFunctions.php');
  // checkCookie($_COOKIE);  // disable check here to test 401 responses elsewhere due to expired stuff

  // Load local vars for use (urls, ports, etc)
  require_once __DIR__ . "/../../config/api.php";


  $headers = array();
  $headers[] = 'Authorization: Bearer ' . $_COOKIE['token'];

  $post = array();  // We are using post, so give it an empty array to post with

  // This is from an INTERNAL POST ONLY when we have changed enable, disable, or delete?
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
        echo "<br><br><br>";
        loadUnknown("Bad action call.  Please play again.");
        break;
    }
    $rawAlterUser = json_decode($alterUser['response'], true);
    $responseCodeAlterUser = $rawAlterUser['statusCode'];
    $post = array();

    // Give a success or fail message for the change attempted
    if ( preg_match('/FAILURE/', $rawAlterUser['data'][0]) || ($responseCodeAlterUser !== 200 && $responseCodeAlterUser !== 403)) { // need a 200 AND to not have the word FAILURE in the response string
      echo "<br><br><br>";
      $responseString = $rawAlterUser['data'][0];
      decideResponse($responseCodeAlterUser, $responseString );
    }
    elseif ( $responseCodeAlterUser == 403) {
      echo "<br><br><br>";
      load403Warn("Expired access credentials");
    }
    else {
      // After a successful change notify and load the page
      echo "<br><br><br>";
      successMessage('User ' . $_POST['action'] . ' is successful.');
    }
  }

  // we SHOULD have gotten an array.... but if not convert it back to one
  $rawUsersList = callApiPost("/admin/findUsersAll" , $post, $headers);
  if (! is_array($rawUsersList)) {
    $rawUsersList = json_decode($rawUsersList['response'], true);
  }
  $usersList = json_decode($rawUsersList['response'], true);
  if ( empty($usersList)) {
    $responseCode = 418;
  }
  else {
    $responseCode = $usersList['statusCode'];
  }
  if ( isset($usersList[0]['code'])) {
    $resposneCode = $usersList[0]['code'];
  }

  $users = $usersList['data'];
  $quitEarly = 0;

  // Sanity check your results
  switch ($responseCode) {
   case 418:
     echo "<br><br><br>";
     load418("Additional access required.  Contact an admin");
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

//debugger($usersList);
//debugger($rawUsersList)
// exit();
  if ($quitEarly == 0) {
  ?>

  <br><br><br> <!--- Drop below the menu banner --->
  <style> td { text-align: center; } </style>
  <center><h1>Search all users</h1></center>
    <div class="container-lg">
      <table id="dt-userList" class="table table-striped table-hover bg-dark table-dark" data-loading-template="loadingTemplate" style="white-space: nowrap;">
        <thead>
          <tr>
            <th><center>User Id</center></th>
            <th><center>User Name</center></th>
            <th><center>User Email</center></th>
            <th><center>Enabled</center></th>
            <th><center>Options</center></th>
        </tr>
      </thead>
        <tbody>
  <?php
    foreach ($usersList['data'] as $users) {
      echo '<tr>';
      echo '<td>' . $users['userId'] . '</td>';
      echo '<td>' . $users['realName'] . '</td>';
      echo '<td>' . $users['email'] . '</td>';
      if ( $users['enable'] == 1 ) {
        echo '<td><img src="/images/generic/green_dot.png" style="width:20px;height:20px;"> enabled </img></td>';
      }
      else {
        echo '<td><img src="/images/generic/orange_dot.png" style="width:20px;height:20px;"> disabled </img></td>';
      }
      echo '<td>';
      echo '<form id="changeUser' . $users['id'] . '" method="POST" action="/admin/index.php?&page=changeUser.php">';
      echo '<input type="hidden" name="id" value="' . $users['id'] . '">';
      echo '<input type="hidden" name="userId" value="' . $users['userId'] . '">';
      echo '<input type="hidden" name="realName" value="' . $users['realName'] . '">';
      echo '<input type="hidden" name="email" value="' . $users['email'] . '">';
      echo '<input type="hidden" name="accessList" value="' . $users['accessList'] . '">';
      echo '<input type="hidden" name="enable" value="' . $users['enable'] . '">';
      echo '<input type="hidden" name="timer" value="' . $users['timer'] . '">';
      echo '<input type="hidden" name="action" value="change">';
      echo '</form>';
      echo '<form id="deleteUser' . $users['id'] . '" method="POST">';
      echo '<input type="hidden" name="id" value="' . $users['id'] . '">';
      echo '<input type="hidden" name="action" value="delete">';
      echo '</form>';
      echo '<form id="disableUser' . $users['id'] . '" method="POST">';
      echo '<input type="hidden" name="username" value="' . $users['userId'] . '">';
      echo '<input type="hidden" name="action" value="disable">';
      echo '</form>';
      echo '<form id="enableUser' . $users['id'] . '" method="POST">';
      echo '<input type="hidden" name="username" value="' . $users['userId'] . '">';
      echo '<input type="hidden" name="action" value="enable">';
      echo '</form>';
      echo '<form id="resetPassword' . $users['id'] . '" method="POST" action="/admin/index.php?&page=resetPassword.php">';
      echo '<input type="hidden" name="id" value="' . $users['id'] . '">';
      echo '<input type="hidden" name="userId" value="' . $users['userId'] . '">';
      echo '<input type="hidden" name="realName" value="' . $users['realName'] . '">';
      echo '<input type="hidden" name="email" value="' . $users['email'] . '">';
      echo '<input type="hidden" name="accessList" value="' . $users['accessList'] . '">';
      echo '<input type="hidden" name="enable" value="' . $users['enable'] . '">';
      echo '<input type="hidden" name="timer" value="' . $users['timer'] . '">';
      echo '<input type="hidden" name="action" value="reset">';
      echo '</form>';
      echo '<button form="changeUser' . $users['id'] . '" type="submit" class="btn btn-default btn-info btn-sm"><span class="glyphicon glyphicon-off"></span>Change Details</button> &nbsp ';
      if ( $users['enable'] == 1 ) {
        echo '<button form="disableUser' . $users['id'] . '" type="submit" class="btn btn-default btn-warning btn-sm"><span class="glyphicon glyphicon-off"></span>Disable User</button> &nbsp ';
      }
      else {
        echo '<button form="enableUser' . $users['id'] . '" type="submit" class="btn btn-default btn-success btn-sm"><span class="glyphicon glyphicon-off"></span>enable User</button> &nbsp ';
      }
      echo '<button form="deleteUser' . $users['id'] . '" type="submit" class="btn btn-default btn-danger btn-sm"><span class="glyphicon glyphicon-off"></span>Delete User</button> &nbsp ';
      echo '<button form="resetPassword' . $users['id'] . '" type="submit" class="btn btn-default btn-success btn-sm"><span class="glyphicon glyphicon-off"></span>Reset Password</button>';
      echo '</td>';
      echo '</tr>';
    }
  ?>
        </tbody>
      </table>
      </div>
      <script> window.addEventListener("DOMContentLoaded", event => {
        const datatablesSimple = document.getElementById("dt-userList");
        if (datatablesSimple) {
          new simpleDatatables.DataTable("#dt-userList", {
            searchable: true,
            sortable: true,
            storable: false,
            paging: true,
            perPage: 25,
            perPageSelect:[25,50,100,200],
            labels: {
              placeholder: "Search Users"
            }
            });
          }
        });
      </script>
      <!-- datatables not loaded with footer, add it now -->
      <script src="/js/simple-datatables/simple-datatables.js"></script>

  <?php


  }
  elseif ( $quitEarly == 2) {
    echo "<br>";
  }
  else {
    // Something went very wrong with the API call, but keep the layout clean...
    loadUnknown("API calls failed in an unexpected way.  Please reload");
  }

?>


