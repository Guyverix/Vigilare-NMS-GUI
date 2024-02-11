<?php
/*
  Try to give a nice name to the user in the UI
  We know we are storing it from the authentication
  in a cookie.  We are already past validation of
  login state, and the cookie is not expired
*/

$realName = '';
$userId = '';

if ( ! empty($_COOKIE['realName'])) {
  $realName=$_COOKIE['realName'];
}
if ( ! empty($_COOKIE['userId'])) {
  $userId = $_COOKIE['userId'];
}

// Echos before the class showing  user options
echo $realName;   // this is pulled from the cookies
?>
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
      <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
          <?php echo "<li>&nbsp&nbsp UserId ". $userId . "</li>"; ?>
          <li><a class="dropdown-item" href="/user/settings.php">Settings</a></li>
          <li><a class="dropdown-item" href="/user/lockAccount.php">Lock Access</a></li>
          <li><hr class="dropdown-divider" /></li>
          <li>
            <div class="form-check form-switch">
              <label class="form-check-label" for="lightSwitch"> Dark Mode </label>
              <input class="form-check-input" type="checkbox" id="lightSwitch"/>
            </div>
          </li>
          <li><a class="dropdown-item" href="/user/logout.php">Logout</a></li>
        </ul>
      </li>
    </ul>
  <!-- </nav>  close in PHP, never in the tempates -->

