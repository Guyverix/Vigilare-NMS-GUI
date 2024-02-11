<!DOCTYPE html>
<html lang="en">
<!-- <META HTTP-EQUIV=Refresh CONTENT="10">  -->
<head>
  <meta charset="utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
  <meta name="description" content="" />
  <meta name="author" content="" />

  <title>Infrastructure UI</title>

  <link href="/js/bootstrap-5/css/bootstrap.min.css" rel="stylesheet" />
  <link href="/css/styles.css" rel="stylesheet" />
  <script src="/js/treeMenu/showCategory.js"></script>
</head>

<body onLoad="nokids();" class="sb-nav-fixed">
  <!-- Navbar -->
  <!-- Upper left corner -->
  <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
    <!-- Navbar Branding -->
    <a class="navbar-brand ps-3" href="/index.php">Main Page</a>
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
        </div> <!-- End side menu -->

        <!-- Add Main panel content here -->
        <div id="layoutSidenav_content" class="bg-dark text-primary">
          <main>
            <div class="container bg-dark text-primary">

<?php

$children = 0;
$no_children = array();
require_once __DIR__ . '/../../config/api.php';

function display_children($parent, $level) {
  global $children;
  global $no_children;
  global $apiUrl;
  global $apiPort;

  $ch=curl_init();
  curl_setopt($ch, CURLOPT_URL, $apiUrl.':'.$apiPort."/infrastructure/findChildren");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, "parent=$parent");
  $output = curl_exec($ch) ;
  $output = json_decode($output, true);
  curl_close($ch);


  // if this is a sub category nest the list
  if($level > 0) {
//    echo "<ul id='$parent' style='display:none;'>\n";
    echo "<ul id='$parent' style='display:none;' class='bg-dark text-primary'>\n";
  }
  $list_id='';
  $row1 = $output['data'];

  // List each child
  foreach ( $row1 as $row ) {
    $children++;
    $list_id = 'list_' . $children;
    echo '<li id="' . $list_id . ' " class="list-group-item bg-dark text-primary" >';
    echo '<a href="#" onClick="show(' . $row['category_id'] .', \'\')">';
    //    echo '<a href="#" onClick="show(' . $row['category_id'] .', \'' . $HTTP_PATH . '\')">';
    echo '<img src="/images/recursion/images/c.gif" title="expand" border="0" id="img_' . $row['category_id'] . '">';
    echo '</a>&nbsp;&nbsp;';
    echo $row['category_name'];
    echo '</li>';

    // Call function again to display childs children
    display_children($row['category_id'], $level+1);
  }


  $child_product = display_products($parent);

  //if this is a sub category nest the list...
  if($level > 0) {
    echo "</ul>\n";
    $no_children[] =  'list_'.$children;

    //if the category has at least one product in it allow us to expand and see that product...
    if($child_product) {
      array_pop($no_children);
    }
  } // end if
}  // end function call



function display_products($parent) {
  global $apiUrl;
  global $apiPort;
  $child_product = false;

  // retrieve all children of $parent
  $ch=curl_init();
  curl_setopt($ch, CURLOPT_URL, $apiUrl.':'.$apiPort."/infrastructure/findChildrenOfParent");
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, "category_id=$parent");
  $output = curl_exec($ch) ;
  $result = json_decode($output, true);
  curl_close($ch);

  $row1=$result['data'];
  foreach($row1 as $row) {
    echo '<li class="list-group-item list-group-item-action bg-dark text-primary">';
    echo '<img src="/images/recursion/images/new.gif" title="product" border="0" id="img_'.$row['category_id'].'" style="margin-top: 5px;">';
    echo '&nbsp;&nbsp;';
    // echo $row['product_name'];  // No links, just hosts named..
    echo '<a href="/host/hostnameDetails.php?id=' . $row['product_name'] . '" ' . ' > ' .  $row['product_name'] . ' </a>';
    echo '</li>';
    $child_product = true;
  }  // end foreach
  return $child_product;
}  // end function


/* Main actual start of function calls */
echo "<ul style='list-style:none;' class='bg-dark text-primary'>";
display_children('',0);
echo "</ul>";
?>
</div>
</main>
</div>
    </div> <!-- End boostrap displays -->




<!-- Cannot use in js, due to inline PHP.  Keep it in the main page -->
<!-- appears that the "red" designation is not used when bootstrap is active -->
<script language="javascript" type="text/javascript">
  function nokids() {
    var kidnot;
    kidnot = new Array();
    <?php
      for($i=0; $i<count($no_children); $i++) {
        print("kidnot.push(\"".$no_children[$i]."\");\n");
      }
    ?>
    for(i in kidnot){
      var theid;
      theid = kidnot[i];
      document.getElementById(kidnot[i]).style.color = "red";
    }
  }
</script>


  <script src="/js/font-awesome/all.min.js" crossorigin="anonymous"></script>
  <script src="/js/bootstrap-5/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>  <!-- Yes, anybody can pull our bootstrap js -->
  <script src="/js/light-switch-bootstrap-main/switch.js"></script>
  <script src="/js/scripts.js"></script>




</body>
</html>
