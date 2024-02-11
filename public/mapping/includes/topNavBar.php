<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Events" />
  <meta name="author" content="Chris Hubbard" />
  <link href="/js/bootstrap-5/css/bootstrap.css" rel="stylesheet" />
  <link href="/css/styles.css" rel="stylesheet" />
  <title>Events</title>
</head>
<body>
  <!-- Navbar -->
  <!-- Upper left to right across the top -->
  <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">

    <!-- Sidebar Toggle -->
    <!-- <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>  -->

    <!-- Navbar Branding -->
    <a class="navbar-brand ps-3"></a>  <!-- spacer -->
    <a class="navbar-brand ps-3" href="/host/index.php">Devices</a>
    <a class="navbar-brand ps-3" href="/infrastructure/index.php">Infrastructure</a>
    <a class="navbar-brand ps-3" href="/event/event.php">Active Events</a>
    <a class="navbar-brand ps-3" href="/event/history.php">Historical Events</a>
    <a class="navbar-brand ps-3" href="/mapping/index.php">Monitors</a>
    <a class="navbar-brand ps-3" href="/reporting/index.php">Reporting</a>
    <a class="navbar-brand ps-3" href="/users/index.php">Users</a>
    <a class="navbar-brand ps-3" href="/admin/index.php">Administration</a>


    <!-- Navbar Search -->
    <form class="d-none d-md-inline-block form-inline ms-auto me-0 me-md-3 my-2 my-md-0">
      <div class="input-group">
        <input class="form-control" type="text" placeholder="Search for..." aria-label="Search for..." aria-describedby="btnNavbarSearch" />
        <button class="btn btn-primary" id="btnNavbarSearch" type="button"><i class="fas fa-search"></i></button>
      </div>
    </form>

    <!-- Far right top -->
    <ul class="navbar-nav ms-auto ms-md-0 me-3 me-lg-4">
      <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle" id="navbarDropdown" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false"><i class="fas fa-user fa-fw"></i></a>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
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
  </nav>

<?php include './bottomFooter.php'; ?>
