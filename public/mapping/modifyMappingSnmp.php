<!DOCTYPE html>
<html lang="en">
<!-- <META HTTP-EQUIV=Refresh CONTENT="10">  -->
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="Main Event UI Page" />
  <meta name="author" content="Chris Hubbard" />

  <title>Mapping UI</title>

<!--
  <link href="/js/bootstrap-5/css/bootstrap.min.css" rel="stylesheet" />
  <link href="/js/bootstrap-5/css/bootstrap-grid.css" rel="stylesheet" />
  <link href="/js/bootstrap-5/css/bootstrap-reboot.css" rel="stylesheet" />
  <link href="/js/bootstrap-5/css/bootstrap-utilities.css" rel="stylesheet" />
  <link href="/js/bootstrap-5/css/bootstrap.rtl.css" rel="stylesheet" />
  <link href="/css/styles.css" rel="stylesheet" />
-->
  <link href="/js/sb-demo/css/styles.css" rel="stylesheet" />
  <link href="/js/bootstrap-5/css/bootstrap.css" rel="stylesheet" />

</head>

<!-- fas == font awesome javascript.  Has nice icons, etc -->
<!-- https://fontawesome.com/search?m=free  choose icon, and find the name.  Call in the i class= to integrate in -->

<body class="sb-nav-fixed">

  <!-- Navbar -->
  <!-- Upper left to right across the top -->
  <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">

    <!-- Navbar Branding -->
    <a class="navbar-brand ps-3" href="/mapping/index.php">Events</a>

    <!-- Sidebar Toggle -->
    <button class="btn btn-link btn-sm order-1 order-lg-0 me-4 me-lg-0" id="sidebarToggle" href="#!"><i class="fas fa-bars"></i></button>

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

  <!-- left side vertical menu -->
  <div id="layoutSidenav">
    <div id="layoutSidenav_nav">
      <nav class="sb-sidenav accordion sb-sidenav-dark bg-dark" id="sidenavAccordion">
        <div class="sb-sidenav-menu">
          <div class="nav">
            <div class="sb-sidenav-menu-heading">Main</div>
              <a class="nav-link" href="/index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-table"></i></div>
              Dashboard
              </a>
              <a class="nav-link" href="/event/index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-bell"></i></div>
              Event
              </a>
              <a class="nav-link" href="/infrastructure/index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-network-wired"></i></div>
              Infrastructure
              </a>
              <a class="nav-link" href="/mapping/index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-diagram-project"></i></div>
              Mapping
              </a>
              <a class="nav-link" href="/reporting/index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-flag"></i></div>
              Reporting
              </a>
              <a class="nav-link" href="/daemon/index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-stopwatch"></i></div>
              Daemon
              </a>


              <div class="sb-sidenav-menu-heading">Support</div>
              <a class="nav-link" href="/admin/index.php">
              <div class="sb-nav-link-icon"><i class="fas fa-lock-open"></i></div>
              Admin
              </a>
              <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseLayouts" aria-expanded="false" aria-controls="collapseLayouts">
              <div class="sb-nav-link-icon"><i class="fas fa-columns"></i></div>
              Documentation
              <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
              </a>
              <div class="collapse" id="collapseLayouts" aria-labelledby="headingOne" data-bs-parent="#sidenavAccordion">
                <nav class="sb-sidenav-menu-nested nav">
                  <a class="nav-link" href="/support/hosts.html" target="_blank">Host</a>
                  <a class="nav-link" href="/support/infrastructure.html" target="_blank">Infrastructure</a>
                  <a class="nav-link" href="/support/mapping.html" target="_blank">Mapping</a>
                  <a class="nav-link" href="/support/reporting.html" target="_blank">Reporting</a>
                  <a class="nav-link" href="/support/daemon.html" target="_blank">Daemon</a>
                  <a class="nav-link" href="/support/admin.html" target="_blank">Admin</a>
                  <a class="nav-link" href="https://github.com/Guyverix/Vigilare-NMS-GUI" target="_blank">Gitlab UI</a>
                  <a class="nav-link" href="https://github.com/Guyverix/Vigilare-NMS-API" target="_blank">Gitlab API</a>
                  <a class="nav-link" href="http://webserver01:83/" target="_blank">PHP MyAdmin</a>
                </nav>
              </div>
            </div>
          </div>
        </nav>
      </div>
    </div>



  <!-- Add Main panel content here -->

  <div id="layoutSidenav_content">
    <main>
      <div class="container-flex">
        <form action="http://127.0.0.1:8002/mapping/set">
          <div class="mb-3 md-3">
            <label for="displayName" class="form-label">Display Name:</label>
            <input type="displayName" class="form-control" id="display_name" placeholder="Display Name" name="display_name">
          </div>
          <div class="mb-3">
            <label for="oid" class="form-label">SNMP Trap OID:</label>
            <input type="displayName" class="form-control" id="oid" placeholder="Trap OID" name="oid">
          </div>
          <div class="mb-3">
            <label for="severity" class="form-label">Event Severity:</label>
            <input type="displayName" class="form-control" id="severity" placeholder="1" name="severity">
          </div>
        </form>
      </div>
    </main>
  </div>





  <footer class="py-4 bg-light mt-auto">
    <div class="container-flex px-4">
      <div class="d-flex align-items-center justify-content-between small">
        <?php echo ' <div class="text-muted">&copy; NMS Monitoring ' . date(Y) . ' </div>'; ?>
<!--        <div class="text-muted">Copyright &copy; Your Website 2021</div>    -->
          <div>

            <a href="#">Privacy Policy</a>
              &middot;
            <a href="#">Terms &amp; Conditions</a>
          </div>

      </div>
    </div>
  </footer>

  <script src="/js/font-awesome/all.min.js" crossorigin="anonymous"></script>
  <script src="/js/bootstrap-5/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
  <script src="/js/light-switch-bootstrap-main/switch.js"></script>
  <script src="/js/sb-demo/js/scripts.js"></script>
<!--  <script src="/js/bootstrap-5/js/bootstrap.js"></script>    -->
<!--  <script src="/js/scripts.js"></script>  -->
</body>
</html>
